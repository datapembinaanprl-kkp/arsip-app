<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Document;
use App\Services\AuditService;
use App\Services\DocumentService;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DokumenController extends Controller
{
    public function __construct(
        private readonly DocumentService $documentService,
        private readonly StorageService  $storage,
    ) {}

    public function index(Request $request): View
    {
        $user         = auth()->user();
        $isRestricted = $user->hasAnyRole(['kepala_bidang', 'staff_operator']);

        $query = Document::active()
            ->with(['department', 'uploader', 'metadata'])
            ->when($isRestricted, fn($q) => $q->forDepartment($user->department_id));

        // Filters
        if ($search = $request->get('q')) {
            $query->where(fn($q) => $q
                ->where('judul', 'ilike', "%{$search}%")
                ->orWhere('nomor_dokumen', 'ilike', "%{$search}%")
                ->orWhere('nomor_surat', 'ilike', "%{$search}%")
                ->orWhereHas('metadata', fn($m) => $m
                    ->where('perihal', 'ilike', "%{$search}%")
                    ->orWhere('pengirim', 'ilike', "%{$search}%"))
                ->orWhereRaw('ocr_content ILIKE ?', ["%{$search}%"])
            );
        }

        if ($tipe = $request->get('tipe')) {
            $query->where('tipe_dokumen', $tipe);
        }

        if ($deptId = $request->get('department_id')) {
            $query->where('department_id', $deptId);
        }

        if ($dari = $request->get('dari')) {
            $query->where('tanggal_dokumen', '>=', $dari);
        }

        if ($sampai = $request->get('sampai')) {
            $query->where('tanggal_dokumen', '<=', $sampai);
        }

        $documents   = $query->orderByDesc('tanggal_dokumen')->paginate(20)->withQueryString();
        $departments = Department::where('is_active', true)->orderBy('nama_bidang')->get();
        $tipeOptions = Document::TIPE_OPTIONS;

        return view('dokumen.index', compact('documents', 'departments', 'tipeOptions'));
    }

    public function create(): View
    {
        $departments = Department::where('is_active', true)->orderBy('nama_bidang')->get();
        $tipeOptions = Document::TIPE_OPTIONS;
        return view('dokumen.upload', compact('departments', 'tipeOptions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'files'           => 'required|array|min:1|max:20',
            'files.*'         => 'file|max:102400',
            'judul'           => 'required|string|max:255',
            'tipe_dokumen'    => 'required|in:' . implode(',', array_keys(Document::TIPE_OPTIONS)),
            'department_id'   => 'required|uuid|exists:departments,id',
            'tanggal_dokumen' => 'required|date',
            'nomor_surat'     => 'nullable|string|max:100',
            'tanggal_retensi' => 'nullable|date|after:tanggal_dokumen',
            'pengirim'        => 'nullable|string|max:255',
            'penerima'        => 'nullable|string|max:255',
            'perihal'         => 'nullable|string|max:500',
            'tags'            => 'nullable|array',
            'keterangan'      => 'nullable|string|max:1000',
        ], [
            'files.required'           => 'Pilih minimal satu file.',
            'judul.required'           => 'Judul dokumen wajib diisi.',
            'tipe_dokumen.required'    => 'Tipe dokumen wajib dipilih.',
            'department_id.required'   => 'Bidang wajib dipilih.',
            'tanggal_dokumen.required' => 'Tanggal dokumen wajib diisi.',
        ]);

        try {
            $documents = $this->documentService->storeBatch(
                $request->except('files'),
                $request->file('files'),
                auth()->id()
            );

            $count = count($documents);
            return redirect()->route('dokumen.index')
                ->with('success', "{$count} dokumen berhasil diarsipkan.");

        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['files' => $e->getMessage()])->withInput();
        } catch (\Throwable $e) {
            return back()->withErrors(['files' => 'Upload gagal. Coba lagi.'])->withInput();
        }
    }

    public function show(Document $document): View
    {
        $this->authorize('view', $document);

        AuditService::logView($document->id);

        $auditLogs = $document->auditLogs()
            ->with('user')
            ->latest('created_at')
            ->limit(20)
            ->get();

        return view('dokumen.show', compact('document', 'auditLogs'));
    }

    public function getUrl(Request $request, Document $document): JsonResponse
    {
        $this->authorize('view', $document);

        $type = $request->get('type', 'preview');
        AuditService::log($type, $document->id);

        return response()->json([
            'url'       => $this->storage->getTemporaryUrl($document->file_path),
            'file_name' => $document->file_name,
            'mime_type' => $document->mime_type,
        ]);
    }

    public function destroy(Request $request, Document $document): RedirectResponse
    {
        $request->validate(['alasan' => 'required|string|min:10']);
        $this->authorize('delete', Document::class);

        $this->documentService->softDelete($document, auth()->id(), $request->alasan);

        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil dihapus dari arsip aktif.');
    }

    public function restore(Document $document): RedirectResponse
    {
        $this->authorize('restore', Document::class);
        $this->documentService->restore($document);

        return redirect()->route('dokumen.index')
            ->with('success', 'Dokumen berhasil dipulihkan.');
    }

    public function departments(): JsonResponse
    {
        return response()->json(
            Department::where('is_active', true)->orderBy('nama_bidang')->get(['id', 'nama_bidang', 'kode_bidang'])
        );
    }
}