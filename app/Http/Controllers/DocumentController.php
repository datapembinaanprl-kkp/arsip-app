<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\TimKerja;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function index(Request $request): Response
    {
        $user = Auth::user();

        $documents = Document::query()
            ->with([
                'assignee:id,name',
                'timKerja:id,nama,kode',
                'creator:id,name',
            ])
            ->visibleFor($user)
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where(function ($q) use ($request) {
                    $q->where('judul', 'ilike', "%{$request->search}%")
                      ->orWhere('nomor_dokumen', 'ilike', "%{$request->search}%");
                });
            })
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->filled('tim_kerja_id'), fn ($q) =>
                $q->where('tim_kerja_id', $request->tim_kerja_id)
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Documents/Index', [
            'documents'     => $documents,
            'timKerjas'     => $user->hasAnyRole(['admin', 'direktur'])
                                ? TimKerja::select('id', 'nama', 'kode')->orderBy('nama')->get()
                                : [],
            'staffList'     => $user->hasAnyRole(['admin', 'direktur', 'kepala_tim_kerja'])
                                ? User::select('id', 'name', 'tim_kerja_id')
                                      ->when($user->hasRole('kepala_tim_kerja'), fn ($q) =>
                                          $q->where('tim_kerja_id', $user->tim_kerja_id)
                                      )
                                      ->role('staff')
                                      ->orderBy('name')
                                      ->get()
                                : [],
            'statusOptions' => Document::STATUS_OPTIONS,
            'filters'       => $request->only(['search', 'status', 'tim_kerja_id']),
        ]);
    }

    public function create(): Response
    {
        $user = Auth::user();

        return Inertia::render('Documents/Create', [
            'timKerjas' => TimKerja::select('id', 'nama', 'kode')->orderBy('nama')->get(),
            'staffList' => User::select('id', 'name')
                               ->role('staff')
                               ->when($user->hasRole('kepala_tim_kerja'), fn ($q) =>
                                   $q->where('tim_kerja_id', $user->tim_kerja_id)
                               )
                               ->orderBy('name')
                               ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'judul'         => ['required', 'string', 'max:200'],
            'nomor_dokumen' => ['nullable', 'string', 'max:50', 'unique:documents'],
            'deadline'      => ['nullable', 'date', 'after_or_equal:today'],
            'catatan'       => ['nullable', 'string'],
            'assignee_id'   => ['required', 'exists:users,id'],
            'tim_kerja_id'  => ['required', 'exists:tim_kerjas,id'],
        ]);

        $data['created_by'] = Auth::id();
        $data['status']     = 'draft';

        $doc = Document::create($data);

        ActivityLogger::dokumenDibuat($doc->judul, $doc->id);

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil dibuat.');
    }

    public function show(Document $document): Response
    {
        $this->authorizeAccess($document);

        $document->load(['assignee', 'creator', 'timKerja', 'histories.changedBy']);

        return Inertia::render('Documents/Show', [
            'document' => $document,
        ]);
    }

    public function edit(Document $document): Response
    {
        $this->authorizeAccess($document);

        $user = Auth::user();

        return Inertia::render('Documents/Edit', [
            'document'  => $document,
            'timKerjas' => TimKerja::select('id', 'nama', 'kode')->orderBy('nama')->get(),
            'staffList' => User::select('id', 'name')
                               ->role('staff')
                               ->when($user->hasRole('kepala_tim_kerja'), fn ($q) =>
                                   $q->where('tim_kerja_id', $user->tim_kerja_id)
                               )
                               ->orderBy('name')
                               ->get(),
        ]);
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        $this->authorizeAccess($document);

        $data = $request->validate([
            'judul'         => ['required', 'string', 'max:200'],
            'nomor_dokumen' => ['nullable', 'string', 'max:50', "unique:documents,nomor_dokumen,{$document->id}"],
            'deadline'      => ['nullable', 'date'],
            'catatan'       => ['nullable', 'string'],
            'assignee_id'   => ['required', 'exists:users,id'],
            'tim_kerja_id'  => ['required', 'exists:tim_kerjas,id'],
        ]);

        $document->update($data);

        ActivityLogger::dokumenDiperbarui($document->judul, $document->id);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $this->authorizeAccess($document);

        ActivityLogger::dokumenDihapus($document->judul);

        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    private function authorizeAccess(Document $document): void
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['admin', 'direktur'])) {
            return;
        }

        if ($user->hasRole('kepala_tim_kerja') && $document->tim_kerja_id === $user->tim_kerja_id) {
            return;
        }

        if ($document->assignee_id === $user->id) {
            return;
        }

        abort(403);
    }
}