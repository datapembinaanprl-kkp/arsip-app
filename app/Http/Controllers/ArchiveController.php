<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ArchiveController extends Controller
{
    use AuthorizesRequests;
    // ─── Index — list dokumen sesuai role ─────────────────
    public function index(Request $request): View
    {
        $user  = auth()->user();
        $query = Archive::with(['user', 'reviewer']);

        // Staf hanya lihat dokumen milik sendiri
        if ($user->hasRole('staf')) {
            $query->where('user_id', $user->id);
        }

        // Filter status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        } else {
            // Default: hanya yang aktif (tidak tampil yang soft-deleted)
            $query->whereNotNull('status');
        }

        // Filter kategori
        if ($kategori = $request->get('kategori')) {
            $query->where('kategori', $kategori);
        }

        // Search
        if ($search = $request->get('q')) {
            $query->where(fn($q) => $q
                ->where('title', 'ilike', "%{$search}%")
                ->orWhere('description', 'ilike', "%{$search}%")
                ->orWhere('kategori', 'ilike', "%{$search}%")
            );
        }

        $archives = $query->latest()->paginate(15)->withQueryString();

        // Kategori untuk filter dropdown
        $kategoriList = Archive::select('kategori')
            ->whereNotNull('kategori')
            ->distinct()
            ->pluck('kategori');

        return view('archives.index', compact('archives', 'kategoriList'));
    }

    // ─── Create form ──────────────────────────────────────
    public function create(): View
    {
        $this->authorize('create', Archive::class);
        return view('archives.create');
    }

    // ─── Store ────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Archive::class);

        $request->validate([
            'title'    => 'required|string|max:255',
            'file'     => 'required|file|max:10240', // 10MB
            'kategori' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'Judul dokumen wajib diisi.',
            'file.required'  => 'File wajib diunggah.',
            'file.max'       => 'Ukuran file maksimal 10MB.',
        ]);

        $path = $request->file('file')->store('archives', 'public');

        Archive::create([
            'user_id'     => auth()->id(),
            'title'       => $request->title,
            'file'        => $path,
            'description' => $request->description,
            'kategori'    => $request->kategori,
            'status'      => Archive::STATUS_AKTIF,
        ]);

        return redirect()->route('archives.index')
            ->with('success', 'Dokumen berhasil diunggah.');
    }

    // ─── Show detail ──────────────────────────────────────
    public function show(Archive $archive): View
    {
        $this->authorize('view', $archive);
        return view('archives.show', compact('archive'));
    }

   public function edit(Archive $archive): View
    {
    $this->authorize('update', $archive);

    return view('archives.edit', compact('archive'));
    }

    // ─── Update ───────────────────────────────────────────
    public function update(Request $request, Archive $archive): RedirectResponse
    {
        $this->authorize('update', $archive);

        // Staf hanya boleh edit dokumen yang ditolak (untuk revisi)
        if (auth()->user()->hasRole('staf') && ! $archive->isDitolak()) {
            return back()->with('error', 'Hanya dokumen yang ditolak yang bisa direvisi.');
        }

        $request->validate([
            'title'       => 'required|string|max:255',
            'file'        => 'nullable|file|max:10240',
            'kategori'    => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'kategori'    => $request->kategori,
        ];

        // Jika ada file baru, hapus yang lama dan simpan yang baru
        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($archive->file);
            $data['file']   = $request->file('file')->store('archives', 'public');
            $data['status'] = Archive::STATUS_AKTIF; // Reset status setelah revisi
            $data['catatan_review'] = null;
            $data['reviewed_at']    = null;
        }

        $archive->update($data);

        return redirect()->route('archives.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    // ─── Tolak dokumen (Supervisor / Direktur / Admin) ────
    public function tolak(Request $request, Archive $archive): RedirectResponse
    {
        // Cek permission Spatie
        if (! auth()->user()->can('dokumen.tolak')) {
            abort(403, 'Anda tidak memiliki izin untuk menolak dokumen.');
        }

        $request->validate([
            'catatan_review' => 'required|string|min:10|max:500',
        ], [
            'catatan_review.required' => 'Alasan penolakan wajib diisi.',
            'catatan_review.min'      => 'Alasan minimal 10 karakter.',
        ]);

        $archive->update([
            'status'         => Archive::STATUS_DITOLAK,
            'reviewed_by'    => auth()->id(),
            'catatan_review' => $request->catatan_review,
            'reviewed_at'    => now(),
        ]);

        return back()->with('success', "Dokumen \"{$archive->title}\" telah ditolak. Staf akan dinotifikasi.");
    }

    // ─── Hapus soft delete (Admin / Direktur) ─────────────
    public function destroy(Archive $archive): RedirectResponse
    {
        $this->authorize('delete', $archive);

        Storage::disk('public')->delete($archive->file);
        $archive->delete(); // SoftDelete

        return redirect()->route('archives.index')
            ->with('success', 'Dokumen berhasil dihapus.');
    }
}