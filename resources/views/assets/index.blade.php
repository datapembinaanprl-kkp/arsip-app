@extends('layouts.app')
@section('title', 'Daftar Aset BMN')

@section('content')
<div class="bmn-page">

    {{-- ── Header ──────────────────────────────────────────────── --}}
    <div class="bmn-header">
        <div>
            <h1 class="bmn-title">Aset Barang Milik Negara</h1>
            <p class="bmn-subtitle">Pengelolaan dan pencatatan aset BMN</p>
        </div>
        <a href="{{ route('assets.create') }}" class="bmn-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2z"/>
            </svg>
            Tambah Aset
        </a>
    </div>

    {{-- ── Flash ───────────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="bmn-alert bmn-alert-success">{{ session('success') }}</div>
    @endif

    {{-- ── Summary Cards ───────────────────────────────────────── --}}
    <div class="bmn-summary-grid">
        <div class="bmn-summary-card">
            <div class="bmn-summary-icon" style="background:#eff6ff;color:#2563eb">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4M5 13a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/></svg>
            </div>
            <div>
                <div class="bmn-summary-val">{{ number_format($summary['total']) }}</div>
                <div class="bmn-summary-label">Total Aset</div>
            </div>
        </div>
        <div class="bmn-summary-card">
            <div class="bmn-summary-icon" style="background:#f0fdf4;color:#16a34a">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>
            </div>
            <div>
                <div class="bmn-summary-val">{{ number_format($summary['baik']) }}</div>
                <div class="bmn-summary-label">Kondisi Baik</div>
            </div>
        </div>
        <div class="bmn-summary-card">
            <div class="bmn-summary-icon" style="background:#fffbeb;color:#d97706">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/></svg>
            </div>
            <div>
                <div class="bmn-summary-val">{{ number_format($summary['rusak_ringan']) }}</div>
                <div class="bmn-summary-label">Rusak Ringan</div>
            </div>
        </div>
        <div class="bmn-summary-card">
            <div class="bmn-summary-icon" style="background:#fef2f2;color:#dc2626">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/><path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/></svg>
            </div>
            <div>
                <div class="bmn-summary-val">{{ number_format($summary['rusak_berat']) }}</div>
                <div class="bmn-summary-label">Rusak Berat</div>
            </div>
        </div>
        <div class="bmn-summary-card bmn-summary-card-wide">
            <div class="bmn-summary-icon" style="background:#f5f3ff;color:#7c3aed">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16"><path d="M4 10.781c.148 1.667 1.513 2.85 3.591 3.003V15h1.043v-1.216c2.27-.179 3.678-1.438 3.678-3.3 0-1.59-.947-2.51-2.956-3.028l-.722-.187V3.467c1.122.11 1.879.714 2.07 1.616h1.47c-.166-1.6-1.54-2.748-3.54-2.875V1H7.591v1.233c-1.939.23-3.27 1.472-3.27 3.156 0 1.454.966 2.483 2.661 2.917l.61.162v4.031c-1.149-.17-1.94-.8-2.131-1.718zm3.391-3.836c-1.043-.263-1.6-.825-1.6-1.616 0-.944.704-1.641 1.8-1.828v3.495l-.2-.05zm1.591 1.872c1.287.323 1.852.859 1.852 1.769 0 1.097-.826 1.828-2.2 1.939V8.73z"/></svg>
            </div>
            <div>
                <div class="bmn-summary-val">Rp {{ number_format($summary['nilai_total'], 0, ',', '.') }}</div>
                <div class="bmn-summary-label">Total Nilai Perolehan</div>
            </div>
        </div>
    </div>

    {{-- ── Filter & Search ─────────────────────────────────────── --}}
    <div class="bmn-filter-bar">
        <form method="GET" action="{{ route('assets.index') }}" class="bmn-filter-form">
            <input type="text" name="q" value="{{ request('q') }}"
                   class="bmn-input bmn-search" placeholder="Cari kode, nama, atau unit...">

            <select name="kategori" class="bmn-input bmn-select">
                <option value="">Semua Kategori</option>
                @foreach(\App\Models\Asset::KATEGORI as $kat)
                    <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>
                        {{ $kat }}
                    </option>
                @endforeach
            </select>

            <select name="kondisi" class="bmn-input bmn-select">
                <option value="">Semua Kondisi</option>
                @foreach(array_keys(\App\Models\Asset::KONDISI) as $kond)
                    <option value="{{ $kond }}" {{ request('kondisi') === $kond ? 'selected' : '' }}>
                        {{ $kond }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bmn-btn-primary">Filter</button>
            @if(request()->hasAny(['q','kategori','kondisi']))
                <a href="{{ route('assets.index') }}" class="bmn-btn-secondary">Reset</a>
            @endif
        </form>

        {{-- Export buttons --}}
        <div class="bmn-export-group">
            <a href="{{ route('assets.export.pdf', request()->query()) }}"
               class="bmn-btn-export" target="_blank">
                ↓ PDF
            </a>
            <a href="{{ route('assets.export.excel', request()->query()) }}"
               class="bmn-btn-export">
                ↓ Excel/CSV
            </a>
        </div>
    </div>

    {{-- ── Table ───────────────────────────────────────────────── --}}
    <div class="bmn-card">
        <table class="bmn-table">
            <thead>
                <tr>
                    <th>Kode Barang</th>
                    <th>Foto</th>
                    <th>Nama Barang</th>
                    <th>Kategori</th>
                    <th>Kondisi</th>
                    <th>Unit Pengguna</th>
                    <th>Nilai Perolehan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                <tr>
                    <td class="bmn-mono">{{ $asset->kode_barang }}</td>
                    <td>
                        <img src="{{ $asset->foto_url }}" alt="{{ $asset->nama_barang }}"
                             class="bmn-avatar">
                    </td>
                    <td>
                        <div class="bmn-asset-name">{{ $asset->nama_barang }}</div>
                        @if($asset->merk_tipe)
                            <div class="bmn-asset-sub">{{ $asset->merk_tipe }}</div>
                        @endif
                    </td>
                    <td><span class="bmn-tag">{{ $asset->kategori }}</span></td>
                    <td>
                        <span class="bmn-badge bmn-badge-{{ $asset->kondisi_badge }}">
                            {{ $asset->kondisi }}
                        </span>
                    </td>
                    <td class="bmn-muted">{{ $asset->unit_pengguna }}</td>
                    <td class="bmn-mono">{{ $asset->nilai_format }}</td>
                    <td>
                        <div class="bmn-actions">
                            <a href="{{ route('assets.show', $asset) }}"
                               class="bmn-btn-icon bmn-btn-view" title="Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.822-2.068C4.506 4.41 6.183 3.5 8 3.5s3.494.91 5.005 2.432A13 13 0 0 1 14.827 8a13 13 0 0 1-1.822 2.068C11.494 11.59 9.817 12.5 8 12.5s-3.494-.91-5.005-2.432A13 13 0 0 1 1.173 8"/><path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/></svg>
                            </a>
                            <a href="{{ route('assets.edit', $asset) }}"
                               class="bmn-btn-icon bmn-btn-edit" title="Edit">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>
                            </a>
                            <form action="{{ route('assets.destroy', $asset) }}" method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button class="bmn-btn-icon bmn-btn-delete" title="Hapus"
                                    onclick="return confirm('Hapus aset {{ addslashes($asset->nama_barang) }}?')">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="bmn-empty">Belum ada data aset.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($assets->hasPages())
        <div class="bmn-pagination">
            {{ $assets->links() }}
        </div>
        @endif
    </div>

</div>
@include('assets.styles')
@endsection