@extends('layouts.app')
@section('title', 'Laporan')

@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Laporan</h1>
    <p class="page-sub">Rekap aktivitas arsip dan progres tim kerja</p>
  </div>
  <div style="display:flex;gap:10px">
    <a href="{{ route('laporan.export', ['type'=>'pdf', 'bulan'=>request('bulan',now()->month), 'tahun'=>request('tahun',now()->year)]) }}"
       class="btn btn-secondary">
      📄 Export PDF
    </a>
    <a href="{{ route('laporan.export', ['type'=>'excel', 'bulan'=>request('bulan',now()->month), 'tahun'=>request('tahun',now()->year)]) }}"
       class="btn btn-secondary">
      📊 Export Excel
    </a>
  </div>
</div>

{{-- Filter periode --}}
<form method="GET" action="{{ route('laporan.index') }}" class="card mb-5">
  <div style="display:flex;gap:12px;align-items:flex-end">
    <div class="form-group" style="margin-bottom:0;flex:1">
      <label class="form-label" style="margin-bottom:4px">Bulan</label>
      <select name="bulan" class="input">
        @foreach(['1'=>'Januari','2'=>'Februari','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni','7'=>'Juli','8'=>'Agustus','9'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $m => $n)
          <option value="{{ $m }}" {{ request('bulan', now()->month) == $m ? 'selected' : '' }}>{{ $n }}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group" style="margin-bottom:0;flex:1">
      <label class="form-label" style="margin-bottom:4px">Tahun</label>
      <select name="tahun" class="input">
        @for($y=now()->year; $y>=now()->year-3; $y--)
          <option value="{{ $y }}" {{ request('tahun',now()->year) == $y ? 'selected':'' }}>{{ $y }}</option>
        @endfor
      </select>
    </div>
    <button type="submit" class="btn btn-primary" style="flex-shrink:0">Tampilkan</button>
  </div>
</form>

{{-- Summary stats --}}
<div class="stat-grid mb-6">
  <div class="stat-card">
    <div>
      <div class="stat-label">Total Dokumen</div>
      <div class="stat-value">{{ number_format($summary['total_dokumen']) }}</div>
      <div class="stat-sub">Periode ini</div>
    </div>
    <div class="stat-icon blue">📂</div>
  </div>
  <div class="stat-card">
    <div>
      <div class="stat-label">Total Unduhan</div>
      <div class="stat-value">{{ number_format($summary['total_download']) }}</div>
      <div class="stat-sub">Log aktivitas</div>
    </div>
    <div class="stat-icon green">📥</div>
  </div>
  <div class="stat-card">
    <div>
      <div class="stat-label">Submission Survey</div>
      <div class="stat-value">{{ number_format($summary['total_survey']) }}</div>
      <div class="stat-sub">Periode ini</div>
    </div>
    <div class="stat-icon amber">📋</div>
  </div>
  <div class="stat-card">
    <div>
      <div class="stat-label">Staf Upload</div>
      <div class="stat-value">{{ $summary['staf_aktif_upload'] }}</div>
      <div class="stat-sub">Pengguna aktif</div>
    </div>
    <div class="stat-icon purple">👤</div>
  </div>
</div>

{{-- Charts row --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">

  <div class="card">
    <div class="card-header">
      <span class="card-title">Dokumen per Bidang</span>
    </div>
    <canvas id="bidangChart" height="220"></canvas>
  </div>

  <div class="card">
    <div class="card-header">
      <span class="card-title">Distribusi Tipe Dokumen</span>
    </div>
    <canvas id="tipeChart" height="220"></canvas>
  </div>

</div>

{{-- Aktivitas per user --}}
<div class="card mb-5">
  <div class="card-header">
    <span class="card-title">Progres Tim — Upload per Pengguna</span>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:40px">#</th>
          <th>Nama Staf</th>
          <th>Bidang</th>
          <th>Role</th>
          <th style="text-align:right">Dokumen Diupload</th>
          <th style="text-align:right">Dokumen Diunduh</th>
          <th style="width:160px">Progres</th>
        </tr>
      </thead>
      <tbody>
        @php $maxUpload = $aktivitasUser->max('total_upload') ?: 1; @endphp
        @forelse($aktivitasUser as $i => $row)
        <tr>
          <td style="color:var(--text-3);font-size:.8rem">{{ $i+1 }}</td>
          <td>
            <div style="font-weight:500;font-size:.87rem">{{ $row->nama }}</div>
            <div style="font-size:.74rem;color:var(--text-3)">{{ $row->email }}</div>
          </td>
          <td style="font-size:.83rem;color:var(--text-2)">{{ $row->nama_bidang }}</td>
          <td><span class="badge badge-gray" style="font-size:.7rem">{{ $row->role }}</span></td>
          <td style="text-align:right;font-weight:600;font-size:.9rem">{{ $row->total_upload }}</td>
          <td style="text-align:right;font-size:.87rem;color:var(--text-2)">{{ $row->total_download }}</td>
          <td>
            <div style="height:6px;background:var(--border);border-radius:99px;overflow:hidden">
              <div style="height:100%;background:var(--blue);border-radius:99px;width:{{ min(100, round($row->total_upload/$maxUpload*100)) }}%;transition:width .5s"></div>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;color:var(--text-3);padding:32px">Tidak ada data aktivitas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- Survey recap --}}
<div class="card">
  <div class="card-header">
    <span class="card-title">Rekap Submission Survey</span>
    <a href="{{ route('survey.index') }}" class="btn btn-secondary btn-sm">Lihat Detail</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Judul Survey</th>
          <th>Total Submission</th>
          <th>Pending</th>
          <th>Diproses</th>
          <th>Selesai</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($surveyRecap as $s)
        <tr>
          <td style="font-weight:500;font-size:.87rem">{{ $s->judul }}</td>
          <td style="font-weight:600">{{ $s->total_submission }}</td>
          <td><span class="badge badge-amber">{{ $s->pending }}</span></td>
          <td><span class="badge badge-blue">{{ $s->diproses }}</span></td>
          <td><span class="badge badge-green">{{ $s->selesai }}</span></td>
          <td>
            <span class="badge {{ $s->is_active ? 'badge-green' : 'badge-gray' }}">
              {{ $s->is_active ? 'Aktif' : 'Ditutup' }}
            </span>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;color:var(--text-3);padding:24px">Belum ada survey.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const bidangData = @json($chartBidang);
const tipeData   = @json($chartTipe);

new Chart(document.getElementById('bidangChart'), {
  type: 'bar',
  data: {
    labels: bidangData.labels,
    datasets: [{
      data: bidangData.values,
      backgroundColor: ['rgba(28,100,242,.15)','rgba(13,158,106,.15)','rgba(217,119,6,.15)','rgba(124,58,237,.15)','rgba(220,38,38,.15)'],
      borderColor:     ['rgba(28,100,242,.8)','rgba(13,158,106,.8)','rgba(217,119,6,.8)','rgba(124,58,237,.8)','rgba(220,38,38,.8)'],
      borderWidth: 1.5, borderRadius: 6,
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

new Chart(document.getElementById('tipeChart'), {
  type: 'doughnut',
  data: {
    labels: tipeData.labels,
    datasets: [{
      data: tipeData.values,
      backgroundColor: ['#1C64F2','#0D9E6A','#D97706','#7C3AED','#DC2626','#64748b'],
      borderWidth: 0,
    }]
  },
  options: {
    responsive: true,
    cutout: '68%',
    plugins: {
      legend: { position: 'bottom', labels: { font: { size: 11, family: 'Plus Jakarta Sans' }, padding: 14, boxWidth: 10 }}
    }
  }
});
</script>
@endpush