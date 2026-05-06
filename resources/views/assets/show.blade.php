@extends('layouts.app')
@section('title', 'Detail Aset — ' . $asset->nama_barang)

@section('content')
<div class="bmn-page">

    <div class="bmn-header">
        <div>
            <h1 class="bmn-title">{{ $asset->nama_barang }}</h1>
            <p class="bmn-subtitle bmn-mono">{{ $asset->kode_barang }}</p>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
            <a href="{{ route('assets.edit', $asset) }}" class="bmn-btn-primary">Edit Aset</a>
            <a href="{{ route('assets.index') }}" class="bmn-btn-secondary">← Kembali</a>
        </div>
    </div>

    <div class="bmn-detail-grid">

        {{-- ── Informasi Utama ──────────────────────────────────── --}}
        <div class="bmn-card bmn-detail-main">
            <div class="bmn-card-header">Informasi Aset</div>
            <div class="bmn-card-body">

                {{-- Foto --}}
                <div style="text-align:center;margin-bottom:1.25rem">
                    <img src="{{ $asset->foto_url }}" alt="{{ $asset->nama_barang }}"
                         class="bmn-detail-photo">
                </div>

                <div class="bmn-detail-rows">
                    <div class="bmn-detail-row">
                        <span class="bmn-detail-label">Kode Barang</span>
                        <span class="bmn-mono">{{ $asset->kode_barang }}</span>
                    </div>
                    <div class="bmn-detail-row">
                        <span class="bmn-detail-label">Kategori</span>
                        <span class="bmn-tag">{{ $asset->kategori }}</span>
                    </div>
                    <div class="bmn-detail-row">
                        <span class="bmn-detail-label">Merk / Tipe</span>
                        <span>{{ $asset->merk_tipe ?? '—' }}</span>
                    </div>
                    <div class="bmn-detail-row">
                        <span class="bmn-detail-label">No. Seri / NUP</span>
                        <span class="bmn-mono">{{ $asset->no_seri ?? '—' }}</span>
                    </div>
                    <div class="bmn-detail-row">
                        <span class="bmn-detail-label">Tahun Perolehan</span>
                        <span>{{ $asset->tahun_perolehan ?? '—' }}</span>
                    </div>
                    <div class="bmn-detail-row">
                        <span class="bmn-detail-label">Nilai Perolehan</span>
                        <span class="bmn-mono" style="font-weight:600">{{ $asset->nilai_format }}</span>
                    </div>
                    <div class="bmn-detail-row">
                        <span class="bmn-detail-label">Kondisi</span>
                        <span class="bmn-badge bmn-badge-{{ $asset->kondisi_badge }}">
                            {{ $asset->kondisi }}
                        </span>
                    </div>
                    <div class="bmn-detail-row">
                        <span class="bmn-detail-label">Lokasi</span>
                        <span>{{ $asset->lokasi }}</span>
                    </div>
                    <div class="bmn-detail-row">
                        <span class="bmn-detail-label">Unit Pengguna</span>
                        <span>{{ $asset->unit_pengguna }}</span>
                    </div>
                    @if($asset->keterangan)
                    <div class="bmn-detail-row bmn-detail-row-col">
                        <span class="bmn-detail-label">Keterangan</span>
                        <span class="bmn-muted">{{ $asset->keterangan }}</span>
                    </div>
                    @endif
                    @if($asset->dokumen_url)
                    <div class="bmn-detail-row">
                        <span class="bmn-detail-label">Dokumen</span>
                        <a href="{{ $asset->dokumen_url }}" target="_blank" class="bmn-link">
                            ↓ Unduh Dokumen
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Riwayat Mutasi + Form Mutasi ─────────────────────── --}}
        <div style="display:flex;flex-direction:column;gap:1rem;">

            {{-- Form Mutasi --}}
            <div class="bmn-card">
                <div class="bmn-card-header">Catat Mutasi Aset</div>
                <div class="bmn-card-body">
                    <form action="{{ route('assets.mutation.store', $asset) }}"
                          method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($errors->any())
                            <div class="bmn-alert bmn-alert-error" style="margin-bottom:1rem">
                                {{ $errors->first() }}
                            </div>
                        @endif
                        <div class="bmn-form-grid-2">
                            <div class="bmn-field">
                                <label class="bmn-label">Unit Asal</label>
                                <input type="text" class="bmn-input"
                                       value="{{ $asset->unit_pengguna }}" readonly
                                       style="background:#f8fafc">
                            </div>
                            <div class="bmn-field">
                                <label class="bmn-label">Unit Tujuan <span class="bmn-req">*</span></label>
                                <input type="text" name="unit_tujuan"
                                       class="bmn-input @error('unit_tujuan') bmn-input-error @enderror"
                                       value="{{ old('unit_tujuan') }}"
                                       placeholder="Nama unit tujuan" required>
                            </div>
                            <div class="bmn-field">
                                <label class="bmn-label">Tanggal Mutasi <span class="bmn-req">*</span></label>
                                <input type="date" name="tanggal_mutasi"
                                       class="bmn-input @error('tanggal_mutasi') bmn-input-error @enderror"
                                       value="{{ old('tanggal_mutasi', date('Y-m-d')) }}" required>
                            </div>
                            <div class="bmn-field">
                                <label class="bmn-label">No. Berita Acara</label>
                                <input type="text" name="no_berita_acara"
                                       class="bmn-input"
                                       value="{{ old('no_berita_acara') }}"
                                       placeholder="Nomor BA (opsional)">
                            </div>
                            <div class="bmn-field bmn-field-full">
                                <label class="bmn-label">Keterangan</label>
                                <textarea name="keterangan" class="bmn-input bmn-textarea"
                                          rows="2" placeholder="Alasan mutasi...">{{ old('keterangan') }}</textarea>
                            </div>
                            <div class="bmn-field bmn-field-full">
                                <label class="bmn-label">Dokumen BA/SK</label>
                                <input type="file" name="dokumen" class="bmn-input"
                                       accept=".pdf,.doc,.docx">
                                <span class="bmn-hint">PDF/DOC, maks. 5MB</span>
                            </div>
                        </div>
                        <div style="margin-top:1rem;text-align:right">
                            <button type="submit" class="bmn-btn-primary">Simpan Mutasi</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Riwayat Mutasi --}}
            <div class="bmn-card">
                <div class="bmn-card-header">Riwayat Mutasi</div>
                <div class="bmn-card-body" style="padding:0">
                    @forelse($asset->mutations as $mut)
                    <div class="bmn-mutation-row">
                        <div class="bmn-mutation-dot"></div>
                        <div class="bmn-mutation-body">
                            <div class="bmn-mutation-title">
                                {{ $mut->unit_asal }}
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16" style="margin:0 .3rem"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8"/></svg>
                                <strong>{{ $mut->unit_tujuan }}</strong>
                            </div>
                            <div class="bmn-mutation-meta">
                                {{ $mut->tanggal_mutasi->format('d M Y') }}
                                @if($mut->no_berita_acara) · BA: {{ $mut->no_berita_acara }} @endif
                                @if($mut->createdBy) · oleh {{ $mut->createdBy->name }} @endif
                            </div>
                            @if($mut->keterangan)
                                <div class="bmn-muted" style="font-size:.8rem;margin-top:.2rem">{{ $mut->keterangan }}</div>
                            @endif
                            @if($mut->dokumen_url)
                                <a href="{{ $mut->dokumen_url }}" target="_blank" class="bmn-link" style="font-size:.8rem">↓ Dokumen</a>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="bmn-empty" style="padding:1.5rem">Belum ada riwayat mutasi.</div>
                    @endforelse
                </div>
            </div>

        </div>{{-- end right col --}}
    </div>{{-- end detail grid --}}

</div>
@include('assets.styles')
@endsection