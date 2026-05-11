<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\SurveyQuestion;
use App\Models\SurveySubmission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class SurveyController extends Controller
{
    // ═══════════════════════════════════════════════════════════════
    // DASHBOARD (auth)
    // ═══════════════════════════════════════════════════════════════

    public function index(): Response
    {
        $surveys = Survey::with('creator')
            ->withCount('submissions')
            ->latest()
            ->paginate(15);

        return Inertia::render('Survey/Index', [
            'surveys' => $surveys,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Survey/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'judul'       => ['required', 'string', 'max:200'],
            'deskripsi'   => ['nullable', 'string'],
            'status'      => ['required', 'in:draft,aktif,tutup'],
            'batas_waktu' => ['nullable', 'date', 'after:now'],

            // Validasi array pertanyaan
            'questions'              => ['required', 'array', 'min:1'],
            'questions.*.label'      => ['required', 'string', 'max:300'],
            'questions.*.type'       => ['required', 'in:text,textarea,radio,checkbox,select,date,rating'],
            'questions.*.required'   => ['nullable', 'boolean'],
            'questions.*.options'    => ['nullable', 'string'], // Newline-separated
        ]);

        DB::transaction(function () use ($request) {
            $survey = Survey::create([
                'judul'       => $request->judul,
                'deskripsi'   => $request->deskripsi,
                'status'      => $request->status,
                'batas_waktu' => $request->batas_waktu,
                'created_by'  => Auth::id(),
            ]);

            // Simpan setiap pertanyaan
            foreach ($request->questions as $order => $q) {
                $options = null;
                if (!empty($q['options'])) {
                    // Pisahkan opsi berdasarkan baris baru, trim spasi
                    $options = array_filter(
                        array_map('trim', explode("\n", $q['options']))
                    );
                    $options = array_values($options);
                }

                SurveyQuestion::create([
                    'survey_id' => $survey->id,
                    'label'     => $q['label'],
                    'type'      => $q['type'],
                    'options'   => $options,
                    'required'  => isset($q['required']),
                    'order'     => $order,
                ]);
            }
        });

        return redirect()->route('survey.index')
            ->with('success', 'Survey berhasil dibuat.');
    }

    public function show(Survey $survey): Response
    {
        $survey->load(['questions', 'creator']);
        $survey->loadCount('submissions');

        return Inertia::render('Survey/Show', [
            'survey' => $survey,
        ]);
    }

    public function edit(Survey $survey): Response
    {
        $survey->load('questions');
        return Inertia::render('Survey/Edit', [
            'survey' => $survey,
        ]);
    }

    public function update(Request $request, Survey $survey): RedirectResponse
    {
        $request->validate([
            'judul'                  => ['required', 'string', 'max:200'],
            'deskripsi'              => ['nullable', 'string'],
            'status'                 => ['required', 'in:draft,aktif,tutup'],
            'batas_waktu'            => ['nullable', 'date'],
            'questions'              => ['required', 'array', 'min:1'],
            'questions.*.label'      => ['required', 'string', 'max:300'],
            'questions.*.type'       => ['required', 'in:text,textarea,radio,checkbox,select,date,rating'],
            'questions.*.required'   => ['nullable', 'boolean'],
            'questions.*.options'    => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($request, $survey) {
            $survey->update([
                'judul'       => $request->judul,
                'deskripsi'   => $request->deskripsi,
                'status'      => $request->status,
                'batas_waktu' => $request->batas_waktu,
            ]);

            // Hapus semua pertanyaan lama, replace dengan yang baru
            $survey->questions()->delete();

            foreach ($request->questions as $order => $q) {
                $options = null;
                if (!empty($q['options'])) {
                    $options = array_values(array_filter(
                        array_map('trim', explode("\n", $q['options']))
                    ));
                }

                SurveyQuestion::create([
                    'survey_id' => $survey->id,
                    'label'     => $q['label'],
                    'type'      => $q['type'],
                    'options'   => $options,
                    'required'  => isset($q['required']),
                    'order'     => $order,
                ]);
            }
        });

        return redirect()->route('survey.show', $survey)
            ->with('success', 'Survey berhasil diperbarui.');
    }

    public function destroy(Survey $survey): RedirectResponse
    {
        $survey->delete();
        return redirect()->route('survey.index')
            ->with('success', 'Survey berhasil dihapus.');
    }

    public function results(Survey $survey): Response
    {
        $survey->load('questions');
        $submissions = SurveySubmission::where('survey_id', $survey->id)
            ->latest('submitted_at')
            ->paginate(20);

        return Inertia::render('Survey/Results', [
            'survey' => $survey,
            'submissions' => $submissions,
        ]);
    }

    // ═══════════════════════════════════════════════════════════════
    // PUBLIK (tanpa auth)
    // ════════════════════════════════════
    public function publicShow(string $token): Response
    {
        $survey = Survey::where('token', $token)
            ->with('questions')
            ->firstOrFail();

        return Inertia::render('Survey/PublicShow', [
            'survey' => $survey,
        ]);
    }

    public function publicSubmit(Request $request, string $token): RedirectResponse
    {
        $survey = Survey::where('token', $token)
            ->with('questions')
            ->firstOrFail();

        if (!$survey->isOpen()) {
            return back()->with('error', 'Survey sudah ditutup.');
        }

        // Validasi identitas responden
        $request->validate([
            'nama_responden' => ['required', 'string', 'max:150'],
            'instansi'       => ['nullable', 'string', 'max:150'],
            'no_telp'        => ['nullable', 'string', 'max:20'],
            'latitude'       => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'      => ['nullable', 'numeric', 'between:-180,180'],
            'alamat_lokasi'  => ['nullable', 'string', 'max:300'],
        ]);

        // Kumpulkan jawaban per question ID
        $jawaban = [];
        foreach ($survey->questions as $question) {
            $key = 'q_' . $question->id;
            $jawaban[$question->id] = $request->input($key);
        }

        $submission = SurveySubmission::create([
            'survey_id'      => $survey->id,
            'nama_responden' => $request->nama_responden,
            'instansi'       => $request->instansi,
            'no_telp'        => $request->no_telp,
            'latitude'       => $request->latitude,
            'longitude'      => $request->longitude,
            'alamat_lokasi'  => $request->alamat_lokasi,
            'jawaban'        => $jawaban,
            'ip_address'     => $request->ip(),
            'submitted_at'   => now(),
        ]);

        return redirect()
            ->route('survey.public', $token)
            ->with('survey_success', true)
            ->with('submission_id', $submission->id);
    }
}