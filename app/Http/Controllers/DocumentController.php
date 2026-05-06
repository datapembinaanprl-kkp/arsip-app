<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\ActivityLogger;

class DocumentController extends Controller
{
    /** Daftar dokumen milik staff yang login */
    public function index(): View
    {
        $user = Auth::user();

        // Direktur lihat semua, staff lihat miliknya saja
        $query = $user->hasRole('direktur')
            ? Document::with('assignee')
            : Document::with('assignee')->forStaff($user->id);

        $documents = $query->latest()->paginate(20);

        return view('documents.index', compact('documents'));
    }

    public function create(): View
    {
        // Daftar staff sebagai pilihan assignee
        $staffList = User::role('staff')->orderBy('name')->get();
        return view('documents.create', compact('staffList'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'judul'           => ['required', 'string', 'max:200'],
            'nomor_dokumen'   => ['nullable', 'string', 'max:50', 'unique:documents'],
            'deadline'        => ['nullable', 'date', 'after_or_equal:today'],
            'catatan'         => ['nullable', 'string'],
            'assignee_id'     => ['required', 'exists:users,id'],
        ]);

        $data['created_by'] = Auth::id();
        $data['status']     = 'draft';

        $doc = Document::create($data);
        ActivityLogger::dokumenDibuat($doc->judul, $doc->id);

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil dibuat.');
    }

    public function show(Document $document): View
    {
        $this->authorizeAccess($document);
        $document->load(['assignee', 'creator', 'histories.changedBy']);

        return view('documents.show', compact('document'));
    }

    public function edit(Document $document): View
    {
        $this->authorizeAccess($document);
        $staffList = User::role('staff')->orderBy('name')->get();
        return view('documents.edit', compact('document', 'staffList'));
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
        ]);

        $document->update($data);
        ActivityLogger::dokumenDiperbarui($document->judul, $document->id);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        ActivityLogger::dokumenDihapus($document->judul);
        $document->delete();
        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }

    /** Staff hanya bisa akses dokumen miliknya, direktur bisa semua */
    private function authorizeAccess(Document $document): void
    {
        $user = Auth::user();
        if (!$user->hasRole('direktur') && $document->assignee_id !== $user->id) {
            abort(403);
        }
    }
}