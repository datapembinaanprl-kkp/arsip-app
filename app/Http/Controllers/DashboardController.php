<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        // ── Base query dokumen sesuai role ─────────────────────────────────
        $base = Document::query();

        if ($user->role === UserRole::Staff) {
            $base->where('assignee_id', $user->id);
        }

        // ── Stats ──────────────────────────────────────────────────────────
        $stats = [
            'total_dokumen'    => (clone $base)->count(),
            'upload_bulan_ini' => (clone $base)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'dokumen_review'   => (clone $base)->where('status', 'review')->count(),
            'dokumen_approved' => (clone $base)->where('status', 'approved')->count(),
            'dokumen_ditolak'  => (clone $base)->where('status', 'rejected')->count(),
            'staf_aktif'       => $user->role === UserRole::Staff
                ? null
                : User::where('status', 'active')->count(),
        ];

        // ── Chart: dokumen per bulan tahun ini ─────────────────────────────
        $chartRaw = (clone $base)
            ->selectRaw("EXTRACT(MONTH FROM created_at)::int AS bulan, COUNT(*) AS total")
            ->whereYear('created_at', now()->year)
            ->groupByRaw("EXTRACT(MONTH FROM created_at)::int")
            ->pluck('total', 'bulan');

        $chartData = array_map(
            fn($m) => (int) ($chartRaw[$m] ?? 0),
            range(1, 12)
        );

        // ── Progres per status ─────────────────────────────────────────────
        $progresStatus = (clone $base)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // ── Dokumen terbaru ────────────────────────────────────────────────
        $recentDocs = (clone $base)
            ->with(['assignee:id,name', 'timKerja:id,nama,kode'])
            ->latest()
            ->limit(6)
            ->get(['id', 'judul', 'nomor_dokumen', 'status', 'deadline', 'assignee_id', 'tim_kerja_id', 'created_at']);

        // ── Deadline mendekat (7 hari ke depan) ───────────────────────────
        $deadlineAlert = (clone $base)
            ->whereNotNull('deadline')
            ->whereDate('deadline', '>=', now())
            ->whereDate('deadline', '<=', now()->addDays(7))
            ->whereNotIn('status', ['approved', 'archived'])
            ->with('assignee:id,name')
            ->orderBy('deadline')
            ->limit(5)
            ->get(['id', 'judul', 'status', 'deadline', 'assignee_id']);

        // ── Aktivitas terbaru — safe fallback ─────────────────────────────
        $recentActivity = [];
        if (class_exists(\App\Models\ActivityLog::class)) {
            try {
                $recentActivity = \App\Models\ActivityLog::with('user:id,name')
                    ->latest()
                    ->limit(8)
                    ->get();
            } catch (\Throwable) {
                $recentActivity = [];
            }
        }

        return Inertia::render('dashboard', [
            'stats'          => $stats,
            'chartData'      => $chartData,
            'recentDocs'     => $recentDocs,
            'recentActivity' => $recentActivity,
            'progresStatus'  => $progresStatus,
            'deadlineAlert'  => $deadlineAlert,
        ]);
    }

    public function chartData(Request $request)
    {
        $tahun = (int) $request->get('tahun', now()->year);
        $user  = auth()->user();

        $query = Document::query();
        if ($user->role === UserRole::Staff) {
            $query->where('assignee_id', $user->id);
        }

        $raw = $query
            ->selectRaw("EXTRACT(MONTH FROM created_at)::int AS bulan, COUNT(*) AS total")
            ->whereYear('created_at', $tahun)
            ->groupByRaw("EXTRACT(MONTH FROM created_at)::int")
            ->pluck('total', 'bulan');

        return response()->json(
            array_map(fn($m) => (int) ($raw[$m] ?? 0), range(1, 12))
        );
    }
}