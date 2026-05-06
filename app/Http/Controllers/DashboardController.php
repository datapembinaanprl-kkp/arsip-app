<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $user = auth()->user();

        // ─── Query dasar sesuai role ───────────────────────
        // Staf: hanya lihat dokumen milik sendiri
        // Admin/Supervisor/Direktur: lihat semua dokumen aktif
        $baseQuery = Archive::aktif() // FIX: pakai scope aktif(), bukan where("status"="aktif")
            ->when(
                $user->hasRole('staf'),
                fn($q) => $q->where('user_id', $user->id)
            );

        // ─── Statistik ────────────────────────────────────
        $stats = [
            'total_dokumen'    => (clone $baseQuery)->count(),

            'upload_bulan_ini' => (clone $baseQuery)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            // Staf aktif hanya tampil untuk non-staf
            'staf_aktif' => $user->hasRole('staf')
                ? null
                : User::active()->count(),

            'survey_bulan_ini' => Survey::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            // Dokumen ditolak milik user ini (untuk notifikasi staf)
            'dokumen_ditolak' => Archive::ditolak()
                ->where('user_id', $user->id)
                ->count(),
        ];

        // ─── Chart data (12 bulan tahun ini) ──────────────
        $chartRaw = (clone $baseQuery)
            ->selectRaw('EXTRACT(MONTH FROM created_at)::int AS bulan, COUNT(*) AS total')
            ->whereYear('created_at', now()->year)
            ->groupByRaw('EXTRACT(MONTH FROM created_at)::int')
            ->pluck('total', 'bulan');

        $chartData = array_map(
            fn($m) => (int) ($chartRaw[$m] ?? 0),
            range(1, 12)
        );

        // ─── Dokumen terbaru ───────────────────────────────
        $recentDocs = (clone $baseQuery)
            ->with('user')
            ->latest()
            ->limit(6)
            ->get();

        // ─── Dokumen ditolak (untuk staf — notifikasi revisi) ─
        $dokumenDitolak = $user->hasRole('staf')
            ? Archive::ditolak()
                ->where('user_id', $user->id)
                ->with('reviewer')
                ->latest('reviewed_at')
                ->limit(5)
                ->get()
            : collect();

        // ─── Progres per status (untuk supervisor/direktur/admin) ─
        $progresStatus = [];
        if ($user->hasAnyRole(['admin', 'supervisor', 'direktur'])) {
            $progresStatus = Archive::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->toArray();
        }

        // Tidak ada AuditLog table → kirim empty collection
        $recentActivity = collect();
        $retensiWarning = collect();
        $recentActivity = ActivityLog::with('user')
        ->latest()
        ->take(10)
        ->get();
        
        return view('dashboard', compact(
            'stats',
            'chartData',
            'recentDocs',
            'recentActivity',
            'retensiWarning',
            'dokumenDitolak',
            'progresStatus',
        ));
    }

    // AJAX: chart data per tahun
    public function chartData(Request $request): JsonResponse
    {
        $tahun = (int) $request->get('tahun', now()->year);
        $user  = auth()->user();

        $raw = Archive::aktif()
            ->when($user->hasRole('staf'), fn($q) => $q->where('user_id', $user->id))
            ->selectRaw('EXTRACT(MONTH FROM created_at)::int AS bulan, COUNT(*) AS total')
            ->whereYear('created_at', $tahun)
            ->groupByRaw('EXTRACT(MONTH FROM created_at)::int')
            ->pluck('total', 'bulan');

        return response()->json(
            array_map(fn($m) => (int) ($raw[$m] ?? 0), range(1, 12))
        );
    }
}