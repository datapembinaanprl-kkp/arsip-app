@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')

{{-- Page header --}}
<div class="page-header">
  <div>
    <h1 class="page-title">Dashboard</h1>
    {{-- FIX: Ganti ->nama ke ->name, hapus ->department yang tidak ada --}}
    <p class="page-sub">Selamat datang, {{ auth()->user()->name }}</p>
  </div>
  <div style="display:flex;gap:10px;align-items:center">
    {{-- FIX: Ganti route dokumen.upload ke archives.create yang ada --}}
    <a href="{{ route('archives.create') }}" class="btn btn-primary">
      <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
      </svg>
      Unggah Dokumen
    </a>
  </div>
</div>

{{-- Stat cards --}}
<div class="stat-grid mb-6">
  <div class="stat-card">
    <div>
      <div class="stat-label">Total Dokumen</div>
      <div class="stat-value">{{ number_format($stats['total_dokumen']) }}</div>
      <div class="stat-sub">Dalam arsip aktif</div>
    </div>
    <div class="stat-icon blue">📂</div>
  </div>
  <div class="stat-card">
    <div>
      <div class="stat-label">Upload Bulan Ini</div>
      <div class="stat-value">{{ number_format($stats['upload_bulan_ini']) }}</div>
      <div class="stat-sub">{{ now()->format('F Y') }}</div>
    </div>
    <div class="stat-icon green">📤</div>
  </div>
  <div class="stat-card">
    <div>
      <div class="stat-label">Staf Aktif</div>
      <div class="stat-value">{{ $stats['staf_aktif'] }}</div>
      <div class="stat-sub">Pengguna terdaftar</div>
    </div>
    <div class="stat-icon purple">👤</div>
  </div>
  <div class="stat-card">
    <div>
      <div class="stat-label">Submission Survey</div>
      <div class="stat-value">{{ number_format($stats['survey_bulan_ini'] ?? 0) }}</div>
      <div class="stat-sub">Bulan ini</div>
    </div>
    <div class="stat-icon amber">📋</div>
  </div>
</div>

{{-- Charts + Activity --}}
<div style="display:grid;grid-template-columns:2fr 1fr;gap:16px;margin-bottom:16px">

  {{-- Bar chart --}}
  <div class="card">
    <div class="card-header">
      <span class="card-title">Dokumen Diunggah per Bulan</span>
      <select id="chart-year" class="input" style="width:auto;padding:5px 10px;font-size:.8rem">
        @for($y = now()->year; $y >= now()->year - 2; $y--)
          <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
        @endfor
      </select>
    </div>
    <canvas id="dokumenChart" height="100"></canvas>
  </div>


  {{-- Aktivitas Terbaru --}}
<div class="card">
    <div class="card-header">
        <span class="card-title">Aktivitas Terbaru</span>
        <span style="font-size:.75rem;color:var(--text-3)">10 aktivitas terakhir</span>
    </div>

    <div class="act-list">
        @forelse($recentActivity as $log)
        <div class="act-item">

            {{-- Avatar inisial user --}}
            <div class="act-avatar" style="background: {{ $log->modul_color }}">
                {{ $log->initials }}
            </div>

            {{-- Konten --}}
            <div class="act-body">
                <div class="act-main">
                    {{-- Nama user --}}
                    <span class="act-user">{{ $log->user?->name ?? 'Sistem' }}</span>
                    {{-- Aksi --}}
                    <span class="act-action">{{ $log->aksi }}</span>
                    {{-- Nama item — link jika ada URL --}}
                    @if($log->url)
                        <a href="{{ $log->url }}" class="act-item-link" title="Lihat detail">
                            {{ $log->nama_item }}
                        </a>
                    @else
                        <span class="act-item-name">{{ $log->nama_item }}</span>
                    @endif
                </div>

                <div class="act-meta">
                    {{-- Badge modul --}}
                    <span class="act-modul-badge" style="background:{{ $log->modul_color }}1a;color:{{ $log->modul_color }}">
                        {{ $log->modul_label }}
                    </span>
                    {{-- Waktu relatif --}}
                    <span class="act-time" title="{{ $log->created_at->format('d M Y, H:i') }}">
                        {{ $log->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>

        </div>
        @empty
        <div class="act-empty">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" style="color:#cbd5e1;margin-bottom:.5rem">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/>
            </svg>
            <p>Belum ada aktivitas tercatat.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- Scoped CSS card aktivitas --}}
<style>
.act-list  { display: flex; flex-direction: column; }

.act-item  {
    display: flex;
    align-items: flex-start;
    gap: .75rem;
    padding: .75rem 0;
    border-bottom: 1px solid var(--border, #e2e8f0);
}
.act-item:last-child { border-bottom: none; }

/* Avatar lingkaran dengan inisial */
.act-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    color: #fff;
    font-size: .7rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    letter-spacing: .03em;
}

.act-body  { flex: 1; min-width: 0; }

.act-main  {
    font-size: .8125rem;
    color: var(--text-1, #1e293b);
    line-height: 1.5;
    display: flex;
    flex-wrap: wrap;
    align-items: baseline;
    gap: .25rem;
}

.act-user        { font-weight: 700; }
.act-action      { color: var(--text-2, #475569); }
.act-item-name   { font-weight: 600; color: var(--text-1, #1e293b); }
.act-item-link   {
    font-weight: 600;
    color: #2563eb;
    text-decoration: none;
    border-bottom: 1px dashed #93c5fd;
    transition: color .15s;
}
.act-item-link:hover { color: #1d4ed8; border-bottom-color: #1d4ed8; }

.act-meta  {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-top: .3rem;
    flex-wrap: wrap;
}

.act-modul-badge {
    display: inline-block;
    font-size: .68rem;
    font-weight: 600;
    padding: .1rem .5rem;
    border-radius: 20px;
    letter-spacing: .02em;
}

.act-time  { font-size: .75rem; color: var(--text-3, #94a3b8); }

.act-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem 1rem;
    color: var(--text-3, #94a3b8);
    font-size: .85rem;
}
</style>

</div>

{{-- Dokumen terbaru + Retensi --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

  {{-- Dokumen terbaru --}}
  <div class="card">
    <div class="card-header">
      <span class="card-title">Dokumen Terbaru</span>
      {{-- FIX: Ganti route ke archives.index --}}
      <a href="{{ route('archives.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>
    <div style="display:flex;flex-direction:column;gap:10px">
      @forelse($recentDocs as $doc)
      {{-- FIX: Ganti route dokumen.show ke archives.show --}}
      <a href="{{ route('archives.show', $doc) }}" style="display:flex;align-items:center;gap:10px;text-decoration:none;padding:8px;border-radius:8px;transition:background .15s" onmouseover="this.style.background='var(--surface-2)'" onmouseout="this.style.background=''">
        <div style="width:36px;height:36px;border-radius:8px;background:var(--surface-2);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
          {{-- FIX: Pakai field 'file' yang ada, cek ekstensi --}}
          @php
            $ext = pathinfo($doc->file ?? '', PATHINFO_EXTENSION);
          @endphp
          {{ in_array($ext, ['pdf']) ? '📄' : (in_array($ext, ['jpg','jpeg','png','gif']) ? '🖼' : (in_array($ext, ['mp4','avi']) ? '🎬' : '📎')) }}
        </div>
        <div style="min-width:0;flex:1">
          {{-- FIX: Pakai 'title' bukan 'judul' --}}
          <p style="font-size:.83rem;font-weight:500;color:var(--text-1);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $doc->title }}</p>
          <p style="font-size:.74rem;color:var(--text-3)">{{ $doc->created_at->format('d/m/Y') }}</p>
        </div>
      </a>
      @empty
      <p style="text-align:center;padding:12px;color:var(--text-3);font-size:.83rem">Belum ada dokumen.</p>
      @endforelse
    </div>
  </div>

  {{-- Retensi akan habis --}}
  <div class="card">
    <div class="card-header">
      <span class="card-title">⚠ Retensi Akan Habis</span>
    </div>
    @forelse($retensiWarning as $doc)
    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border)">
      <div style="min-width:0;flex:1;margin-right:12px">
        <p style="font-size:.83rem;font-weight:500;color:var(--text-1);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $doc->title }}</p>
      </div>
      <div style="text-align:right;flex-shrink:0">
        {{-- FIX: Null-safe karena tanggal_retensi nullable --}}
        @if($doc->tanggal_retensi)
        <span style="font-size:.75rem;padding:2px 8px;border-radius:99px;background:{{ $doc->tanggal_retensi->diffInDays(now()) <= 7 ? '#FEE2E2' : '#FEF3C7' }};color:{{ $doc->tanggal_retensi->diffInDays(now()) <= 7 ? '#991b1b' : '#92400e' }}">
          {{ $doc->tanggal_retensi->diffForHumans() }}
        </span>
        @endif
      </div>
    </div>
    @empty
    <div style="text-align:center;padding:24px">
      <div style="font-size:2rem;margin-bottom:8px">✅</div>
      <p style="color:var(--text-3);font-size:.83rem">Tidak ada dokumen yang akan habis retensi.</p>
    </div>
    @endforelse
  </div>

</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  const chartData = @json($chartData);
  const BULAN = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Ags','Sep','Okt','Nov','Des'];

  const ctx = document.getElementById('dokumenChart').getContext('2d');
  const chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: BULAN,
      datasets: [{
        label: 'Jumlah Dokumen',
        data: chartData,
        backgroundColor: 'rgba(28,100,242,.1)',
        borderColor: 'rgba(28,100,242,.75)',
        borderWidth: 1.5,
        borderRadius: 6,
        borderSkipped: false,
      }]
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 11 }, color: '#9C9A92' }},
        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,.04)' }, ticks: { font: { size: 11 }, color: '#9C9A92', precision: 0 }}
      }
    }
  });

  // FIX: Gunakan route yang benar untuk chart AJAX
  document.getElementById('chart-year')?.addEventListener('change', function() {
    fetch(`/dashboard/chart-data?tahun=${this.value}`, {
      headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
    })
      .then(r => r.json())
      .then(d => { chart.data.datasets[0].data = d; chart.update(); })
      .catch(() => {}); // Silent fail jika endpoint belum ada
  });
</script>
@endpush