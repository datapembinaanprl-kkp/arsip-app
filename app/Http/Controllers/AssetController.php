<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetMutationRequest;
use App\Http\Requests\AssetRequest;
use App\Models\Asset;
use App\Models\AssetMutation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\ActivityLogger;
use Inertia\Inertia;
use Inertia\Response;

class AssetController extends Controller
{
    // ─── CRUD ─────────────────────────────────────────────────────

    public function index(Request $request): Response
    {
        $assets = Asset::search($request->q)
            ->filterKategori($request->kategori)
            ->filterKondisi($request->kondisi)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        // Summary cards
        $summary = [
            'total'        => Asset::count(),
            'baik'         => Asset::where('kondisi', 'Baik')->count(),
            'rusak_ringan' => Asset::where('kondisi', 'Rusak Ringan')->count(),
            'rusak_berat'  => Asset::where('kondisi', 'Rusak Berat')->count(),
            'nilai_total'  => Asset::sum('nilai_perolehan'),
        ];

        return Inertia::render('Assets/Index', [
            'assets' => $assets,
            'summary' => $summary,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Assets/Create');
    }

    public function store(AssetRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('assets/foto', 'public');
        }

        if ($request->hasFile('dokumen')) {
            $data['dokumen'] = $request->file('dokumen')->store('assets/dokumen', 'public');
        }

        $asset = Asset::create($data);
        ActivityLogger::asetDibuat($asset->nama_barang, $asset->id);
        
        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show(Asset $asset): Response
    {
        $asset->load('mutations.createdBy');
        return Inertia::render('Assets/Show', [
            'asset' => $asset,
        ]);
    }

    public function edit(Asset $asset): Response
    {
        return Inertia::render('Assets/Edit', [
            'asset' => $asset,
        ]);
    }

    public function update(AssetRequest $request, Asset $asset): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            Storage::disk('public')->delete($asset->foto ?? '');
            $data['foto'] = $request->file('foto')->store('assets/foto', 'public');
        }

        if ($request->hasFile('dokumen')) {
            Storage::disk('public')->delete($asset->dokumen ?? '');
            $data['dokumen'] = $request->file('dokumen')->store('assets/dokumen', 'public');
        }

        $asset->update($data);

        ActivityLogger::asetDiperbarui($asset->nama_barang, $asset->id);

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset): RedirectResponse
    {
        Storage::disk('public')->delete(array_filter([$asset->foto, $asset->dokumen]));
        ActivityLogger::asetDihapus($asset->nama_barang);
        $asset->delete();

        return redirect()->route('assets.index')
            ->with('success', 'Aset berhasil dihapus.');
    }

    // ─── Mutasi ───────────────────────────────────────────────────

    public function mutationStore(AssetMutationRequest $request, Asset $asset): RedirectResponse
    {
        $data                = $request->validated();
        $data['asset_id']    = $asset->id;
        $data['unit_asal']   = $asset->unit_pengguna; // Catat unit asal sebelum mutasi
        $data['created_by']  = Auth::id();

        if ($request->hasFile('dokumen')) {
            $data['dokumen'] = $request->file('dokumen')->store('assets/mutasi', 'public');
        }

        AssetMutation::create($data);

        // Update unit pengguna aset sesuai tujuan mutasi
        $asset->update(['unit_pengguna' => $data['unit_tujuan']]);
        
        ActivityLogger::asetDimutasi($asset->nama_barang, $data['unit_tujuan'], $asset->id);
        return redirect()->route('assets.show', $asset)
            ->with('success', 'Mutasi aset berhasil dicatat.');
    }

    // ─── Export ───────────────────────────────────────────────────

    /**
     * Export daftar aset ke PDF menggunakan barryvdh/laravel-dompdf.
     * Install: composer require barryvdh/laravel-dompdf
     */
    public function exportPdf(Request $request)
    {
        $assets = Asset::search($request->q)
            ->filterKategori($request->kategori)
            ->filterKondisi($request->kondisi)
            ->orderBy('kategori')
            ->orderBy('nama_barang')
            ->get();

        $pdf = Pdf::loadView('assets.export-pdf', compact('assets'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('daftar-bmn-' . date('Ymd') . '.pdf');
    }

    /**
     * Export ke Excel menggunakan maatwebsite/excel.
     * Install: composer require maatwebsite/excel
     */
    public function exportExcel(Request $request)
    {
        // Implementasi dengan Laravel Excel Export class
        // return Excel::download(new AssetsExport($request), 'daftar-bmn.xlsx');

        // Fallback: CSV sederhana tanpa package tambahan
        $assets = Asset::search($request->q)
            ->filterKategori($request->kategori)
            ->filterKondisi($request->kondisi)
            ->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="daftar-bmn-' . date('Ymd') . '.csv"',
        ];

        $callback = function () use ($assets) {
            $handle = fopen('php://output', 'w');

            // Header kolom
            fputcsv($handle, [
                'Kode Barang', 'Nama Barang', 'Kategori', 'Merk/Tipe',
                'No. Seri', 'Tahun Perolehan', 'Nilai Perolehan',
                'Kondisi', 'Lokasi', 'Unit Pengguna',
            ]);

            foreach ($assets as $asset) {
                fputcsv($handle, [
                    $asset->kode_barang,
                    $asset->nama_barang,
                    $asset->kategori,
                    $asset->merk_tipe,
                    $asset->no_seri,
                    $asset->tahun_perolehan,
                    $asset->nilai_perolehan,
                    $asset->kondisi,
                    $asset->lokasi,
                    $asset->unit_pengguna,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
    
}