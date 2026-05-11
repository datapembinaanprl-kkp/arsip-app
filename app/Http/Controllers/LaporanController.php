<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Document;
use App\Models\SurveyForm;
use App\Models\SurveySubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Inertia\Inertia;
use Inertia\Response;

class LaporanController extends Controller
{
    public function index(Request $request): Response
    {
        $bulan = (int) $request->get('bulan', now()->month);
        $tahun = (int) $request->get('tahun', now()->year);

        $user         = auth()->user();
        $isRestricted = $user->hasAnyRole(['kepala_bidang']);

        $base = Document::active()
            ->when($isRestricted, fn($q) => $q->forDepartment($user->department_id))
            ->whereMonth('tanggal_dokumen', $bulan)
            ->whereYear('tanggal_dokumen', $tahun);

        $summary = [
            'total_dokumen'    => $base->clone()->count(),
            'total_download'   => AuditLog::where('aksi','download')->whereMonth('created_at',$bulan)->whereYear('created_at',$tahun)->count(),
            'total_survey'     => SurveySubmission::whereMonth('submitted_at',$bulan)->whereYear('submitted_at',$tahun)->count(),
            'staf_aktif_upload'=> $base->clone()->distinct('uploaded_by')->count('uploaded_by'),
        ];

        // Chart: dokumen per bidang
        $bidangRaw = $base->clone()
            ->selectRaw('departments.nama_bidang, COUNT(*) as total')
            ->join('departments','documents.department_id','=','departments.id')
            ->groupBy('departments.nama_bidang')
            ->get();

        $chartBidang = [
            'labels' => $bidangRaw->pluck('nama_bidang')->toArray(),
            'values' => $bidangRaw->pluck('total')->map(fn($v)=>(int)$v)->toArray(),
        ];

        // Chart: dokumen per tipe
        $tipeRaw = $base->clone()
            ->selectRaw('tipe_dokumen, COUNT(*) as total')
            ->groupBy('tipe_dokumen')
            ->get();

        $chartTipe = [
            'labels' => $tipeRaw->pluck('tipe_dokumen')->map(fn($t) => Document::TIPE_OPTIONS[$t] ?? $t)->toArray(),
            'values' => $tipeRaw->pluck('total')->map(fn($v)=>(int)$v)->toArray(),
        ];

        // Aktivitas per user
        $aktivitasUser = User::select('users.id','users.nama','users.email','departments.nama_bidang')
            ->selectRaw("(SELECT COUNT(*) FROM documents d WHERE d.uploaded_by = users.id AND EXTRACT(MONTH FROM d.tanggal_dokumen)=? AND EXTRACT(YEAR FROM d.tanggal_dokumen)=? AND d.is_deleted=false) as total_upload", [$bulan, $tahun])
            ->selectRaw("(SELECT COUNT(*) FROM audit_logs al WHERE al.user_id = users.id AND al.aksi='download' AND EXTRACT(MONTH FROM al.created_at)=? AND EXTRACT(YEAR FROM al.created_at)=?) as total_download", [$bulan, $tahun])
            ->selectRaw("(SELECT name FROM model_has_roles mr JOIN roles r ON r.id = mr.role_id WHERE mr.model_id = users.id LIMIT 1) as role")
            ->leftJoin('departments','users.department_id','=','departments.id')
            ->where('users.is_active', true)
            ->orderByDesc('total_upload')
            ->get();

        // Survey recap
        $surveyRecap = SurveyForm::withCount([
            'submissions as total_submission',
            'submissions as pending'    => fn($q) => $q->where('status','pending'),
            'submissions as diproses'   => fn($q) => $q->where('status','diproses'),
            'submissions as selesai'    => fn($q) => $q->where('status','selesai'),
        ])->orderByDesc('created_at')->get();

        return Inertia::render('laporan/Index', [
            'summary' => $summary,
            'chartBidang' => $chartBidang,
            'chartTipe' => $chartTipe,
            'aktivitasUser' => $aktivitasUser,
            'surveyRecap' => $surveyRecap,
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);
    }

    public function retensi(): Response
    {
        $user         = auth()->user();
        $isRestricted = $user->hasAnyRole(['kepala_bidang','staff_operator']);

        $base = Document::active()
            ->with('department')
            ->whereNotNull('tanggal_retensi')
            ->when($isRestricted, fn($q) => $q->forDepartment($user->department_id))
            ->orderBy('tanggal_retensi');

        $expired    = $base->clone()->where('tanggal_retensi', '<', now())->get();
        $expiring30 = $base->clone()->whereBetween('tanggal_retensi', [now(), now()->addDays(30)])->get();
        $expiring90 = $base->clone()->whereBetween('tanggal_retensi', [now()->addDays(30), now()->addDays(90)])->get();

        $totalWithRetensi = $base->clone()->count();
        $allWithRetensi   = $base->clone()->paginate(20);

        return Inertia::render('laporan/Retensi', [
            'expired' => $expired,
            'expiring30' => $expiring30,
            'expiring90' => $expiring90,
            'totalWithRetensi' => $totalWithRetensi,
            'allWithRetensi' => $allWithRetensi
        ]);
    }

    public function export(Request $request)
    {
        // Export PDF atau Excel (implementasi dengan laravel-dompdf atau maatwebsite/excel)
        // Stub untuk development
        return back()->with('error', 'Fitur export sedang dalam pengembangan.');
    }
}