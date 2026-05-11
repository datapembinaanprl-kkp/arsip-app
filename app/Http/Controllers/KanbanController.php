<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Services\ActivityLogger;
use Inertia\Inertia;
use Inertia\Response;

class KanbanController extends Controller
{
    /**
     * Tampilan kanban — hanya direktur.
     * Menampilkan semua dokumen semua staff dikelompokkan per status.
     */
    public function index(): Response
    {
        // Eager load assignee untuk tiap card
        $documents = Document::with(['assignee', 'histories'])
            ->orderBy('deadline')
            ->get();

        // Kelompokkan per status sesuai urutan kolom
        $columns = collect(Document::STATUSES)->map(function ($config, $status) use ($documents) {
            return [
                'label'     => $config['label'],
                'color'     => $config['color'],
                'documents' => $documents->where('status', $status)->values(),
            ];
        });

        // Summary untuk header
        $summary = [
            'total'    => $documents->count(),
            'overdue'  => $documents->filter->is_overdue->count(),
            'selesai'  => $documents->where('status', 'selesai')->count(),
            'revisi'   => $documents->where('status', 'revisi')->count(),
        ];

        return Inertia::render('Kanban/Index', [
            'columns' => $columns,
            'summary' => $summary,
        ]);
    }

    /**
     * Update status dokumen — bisa diakses direktur dan staff (dengan batasan).
     * Validasi transisi dilakukan di sini berdasarkan role.
     */
    public function updateStatus(Request $request, Document $document)
    {
        $user     = Auth::user();
        $role     = $user->hasRole('direktur') ? 'direktur' : 'staff';
        $newStatus = $request->input('status');

        // Validasi: apakah transisi ini diizinkan untuk role ini?
        $allowed = $document->availableTransitions($role);
        if (!in_array($newStatus, $allowed)) {
            return response()->json([
                'message' => 'Transisi status tidak diizinkan.',
            ], 403);
        }

        // Staff hanya bisa update dokumen yang di-assign ke mereka
        if ($role === 'staff' && $document->assignee_id !== $user->id) {
            return response()->json(['message' => 'Bukan dokumen Anda.'], 403);
        }

        $oldStatus = $document->status;
        $catatan   = $request->input('catatan');

        // Update timestamp milestone jika relevan
        $timestamps = match ($newStatus) {
            'diajukan'  => ['diajukan_at'  => now()],
            'disetujui' => ['disetujui_at' => now()],
            'selesai'   => ['selesai_at'   => now()],
            default     => [],
        };

        // Simpan alasan revisi jika dikembalikan
        if ($newStatus === 'revisi') {
            $timestamps['alasan_revisi'] = $catatan;
        }

        $document->update(array_merge(['status' => $newStatus], $timestamps));
        ActivityLogger::dokumenStatusBerubah($document->judul, $newStatus, $document->id);

        // Catat ke audit trail
        DocumentHistory::create([
            'document_id' => $document->id,
            'status_dari' => $oldStatus,
            'status_ke'   => $newStatus,
            'catatan'     => $catatan,
            'changed_by'  => $user->id,
        ]);

        return response()->json([
            'message'    => 'Status berhasil diperbarui.',
            'new_status' => $newStatus,
            'label'      => $document->fresh()->status_label,
        ]);
    }
}