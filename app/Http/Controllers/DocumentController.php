<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\TimKerja;
use App\Models\Category;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

    class DocumentController extends Controller
{
    public function index(Request $request): Response
    {
        $documents = Document::query()
            ->with([
                'assignee:id,name',
                'timKerja:id,nama,kode',
            ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $term = '%' . $request->search . '%';
                $q->where(fn($q) =>
                    $q->where('judul', 'ilike', $term)
                      ->orWhere('nomor_dokumen', 'ilike', $term)
                      ->orWhere('catatan', 'ilike', $term)
                );
            })
            ->when($request->filled('tim_kerja_id'), fn($q) =>
                $q->where('tim_kerja_id', $request->tim_kerja_id)
            )
            ->when($request->filled('status'), fn($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->filled('deadline_from'), fn($q) =>
                $q->whereDate('deadline', '>=', $request->deadline_from)
            )
            ->when($request->filled('deadline_to'), fn($q) =>
                $q->whereDate('deadline', '<=', $request->deadline_to)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Documents/index', [
            'documents'     => $documents,
            'tim_kerja_list' => TimKerja::where('is_active', true)->orderBy('nama')->get(['id', 'nama', 'kode']),
            'filters'       => $request->only(['search', 'tim_kerja_id', 'status', 'deadline_from', 'deadline_to']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Documents/Create', [
            'tim_kerja_list' => TimKerja::where('is_active', true)->orderBy('nama')->get(['id', 'nama', 'kode']),
            'status_options' => Document::statusOptions(),
            'users'          => \App\Models\User::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(StoreDocumentRequest $request)
    {
        Document::create([
            ...$request->validated(),
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil ditambahkan.');
    }

    public function show(Document $document): Response
    {
        return Inertia::render('Documents/Show', [
            'document' => $document->load(['assignee:id,name', 'creator:id,name', 'timKerja', 'histories.changedByUser:id,name']),
        ]);
    }

    public function edit(Document $document): Response
    {
        return Inertia::render('Documents/Edit', [
            'document'       => $document->load(['assignee:id,name', 'timKerja:id,nama,kode']),
            'tim_kerja_list' => TimKerja::where('is_active', true)->orderBy('nama')->get(['id', 'nama', 'kode']),
            'status_options' => Document::statusOptions(),
            'users'          => \App\Models\User::active()->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $document->update($request->validated());

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function destroy(Document $document)
    {
        $document->delete();

        return redirect()->route('documents.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }
}