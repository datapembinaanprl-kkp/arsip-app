@extends('layouts.app')
@section('title', 'Arsip Dokumen')

@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Arsip Dokumen</h1>
    <p class="page-sub">{{ $archives->total() }} dokumen ditemukan</p>
  </div>
  @can('dokumen.upload')
  <a href="{{ route('archives.create') }}" class="btn btn-primary">+ Unggah</a>
  @endcan
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('archives.index') }}" class="card mb-4">
  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <div style="flex:1;min-width:180px">
      <input type="text" name="q" value="{{ request('q') }}"
             class="input" placeholder="Cari judul atau deskripsi..." />
    </div>
    <select name="status" class="input" style="width:auto">
      <option value="">Semua Status</option>
      <option value="aktif"      {{ request('status')=='aktif'      ? 'selected':'' }}>Aktif</option>
      <option value="ditolak"    {{ request('status')=='ditolak'    ? 'selected':'' }}>Ditolak</option>
      <option value="diarsipkan" {{ request('status')=='diarsipkan' ? 'selected':'' }}>Diarsipkan</option>
    </select>
    @if($kategoriList->isNotEmpty())
    <select name="kategori" class="input" style="width:auto">
      <option value="">Semua Kategori</option>
      @foreach($kategoriList as $kat)
      <option value="{{ $kat }}" {{ request('kategori')==$kat ? 'selected':'' }}>{{ $kat }}</option>
      @endforeach
    </select>
    @endif
    <button type="submit" class="btn btn-primary">Cari</button>
    @if(request()->hasAny(['q','status','kategori']))
    <a href="{{ route('archives.index') }}" class="btn btn-secondary">Reset</a>
    @endif
  </div>
</form>

{{-- Notifikasi dokumen ditolak untuk staf --}}
@if(auth()->user()->hasRole('staf'))
@php $ditolak = \App\Models\Archive::ditolak()->where('user_id', auth()->id())->count(); @endphp
@if($ditolak > 0)
<div class="alert alert-danger" style="margin-bottom:16px">
  ⚠ <strong>{{ $ditolak }} dokumen Anda ditolak</strong> dan perlu direvisi.
  <a href="{{ route('archives.index', ['status'=>'ditolak']) }}" style="color:var(--red);font-weight:600">Lihat →</a>
</div>
@endif
@endif

{{-- Tabel --}}
<div class="card" style="padding:0;overflow:hidden">
  @if($archives->isEmpty())
  <div style="text-align:center;padding:60px;color:var(--text-3)">
    <div style="font-size:3rem;margin-bottom:12px;opacity:.4">📂</div>
    <p style="font-weight:600;color:var(--text-1)">Tidak ada dokumen</p>
    @can('dokumen.upload')
    <a href="{{ route('archives.create') }}" class="btn btn-primary" style="margin-top:14px">Unggah Sekarang</a>
    @endcan
  </div>
  @else
  <table style="width:100%;border-collapse:collapse">
    <thead style="background:var(--surface-2)">
      <tr>
        <th style="padding:10px 16px;text-align:left;font-size:.74rem;color:var(--text-3);font-weight:600">Judul</th>
        <th style="padding:10px 16px;text-align:left;font-size:.74rem;color:var(--text-3);font-weight:600">Kategori</th>
        @cannot('staf') {{-- Staf tidak perlu lihat kolom Diunggah Oleh --}}
        <th style="padding:10px 16px;text-align:left;font-size:.74rem;color:var(--text-3);font-weight:600">Diunggah Oleh</th>
        @endcannot
        <th style="padding:10px 16px;text-align:left;font-size:.74rem;color:var(--text-3);font-weight:600">Status</th>
        <th style="padding:10px 16px;text-align:left;font-size:.74rem;color:var(--text-3);font-weight:600">Tanggal</th>
        <th style="padding:10px 16px;text-align:right;font-size:.74rem;color:var(--text-3);font-weight:600">Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach($archives as $archive)
      <tr style="border-bottom:1px solid var(--border)">
        <td style="padding:12px 16px">
          <div style="font-size:.87rem;font-weight:500;color:var(--text-1)">{{ $archive->title }}</div>
          @if($archive->description)
          <div style="font-size:.74rem;color:var(--text-3);margin-top:2px">
            {{ Str::limit($archive->description, 60) }}
          </div>
          @endif
          {{-- Tampilkan catatan tolak untuk staf --}}
          @if($archive->isDitolak() && $archive->catatan_review)
          <div style="font-size:.74rem;color:var(--red);margin-top:4px;background:var(--red-light);padding:4px 8px;border-radius:4px">
            Alasan: {{ $archive->catatan_review }}
          </div>
          @endif
        </td>
        <td style="padding:12px 16px;font-size:.83rem;color:var(--text-2)">
          {{ $archive->kategori ?? '—' }}
        </td>
        @cannot('staf')
        <td style="padding:12px 16px;font-size:.83rem;color:var(--text-2)">
          {{ $archive->user->name ?? '—' }}
        </td>
        @endcannot
        <td style="padding:12px 16px">
          <span class="status-badge status-{{ $archive->status }}">
            {{ $archive->status_label }}
          </span>
        </td>
        <td style="padding:12px 16px;font-size:.8rem;color:var(--text-3)">
          {{ $archive->created_at->format('d/m/Y') }}
        </td>
        <td style="padding:12px 16px;text-align:right">
          <div style="display:flex;justify-content:flex-end;gap:6px">
            {{-- Download --}}
            <a href="{{ Storage::url($archive->file) }}" target="_blank"
               class="btn btn-secondary btn-sm" title="Unduh">↓</a>

            {{-- Edit/Revisi (staf hanya edit yang ditolak) --}}
            @can('update', $archive)
            <a href="{{ route('archives.edit', $archive) }}"
               class="btn btn-secondary btn-sm">
              {{ auth()->user()->hasRole('staf') ? 'Revisi' : 'Edit' }}
            </a>
            @endcan

            {{-- Tolak (supervisor/direktur/admin) --}}
            @can('dokumen.tolak')
            @if($archive->status !== 'ditolak')
            <button class="btn btn-secondary btn-sm" style="color:var(--amber)"
                    onclick="openTolakModal({{ $archive->id }}, '{{ addslashes($archive->title) }}')">
              Tolak
            </button>
            @endif
            @endcan

            {{-- Hapus (admin/direktur) --}}
            @can('delete', $archive)
            <form method="POST" action="{{ route('archives.destroy', $archive) }}"
                  onsubmit="return confirm('Hapus dokumen ini permanen?')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
            </form>
            @endcan
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{-- Pagination --}}
  @if($archives->hasPages())
  <div style="padding:14px 20px;border-top:1px solid var(--border);display:flex;justify-content:space-between;align-items:center">
    <span style="font-size:.8rem;color:var(--text-3)">
      {{ $archives->firstItem() }}–{{ $archives->lastItem() }} dari {{ $archives->total() }}
    </span>
    {{ $archives->links() }}
  </div>
  @endif
  @endif
</div>

{{-- Modal tolak dokumen --}}
<div id="tolak-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:900;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:16px;padding:28px;max-width:480px;width:90%;box-shadow:0 12px 32px rgba(0,0,0,.15)">
    <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:6px">Tolak Dokumen</h3>
    <p id="tolak-doc-name" style="font-size:.85rem;color:var(--text-2);margin-bottom:16px"></p>
    <form id="tolak-form" method="POST">
      @csrf
      <div style="margin-bottom:16px">
        <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px">
          Alasan Penolakan <span style="color:var(--red)">*</span>
        </label>
        <textarea name="catatan_review" rows="3" required minlength="10"
                  class="input" placeholder="Jelaskan alasan penolakan (min. 10 karakter)..."></textarea>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" class="btn btn-secondary" onclick="closeTolakModal()">Batal</button>
        <button type="submit" class="btn btn-primary" style="background:var(--red)">Tolak Dokumen</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
function openTolakModal(id, judul) {
  document.getElementById('tolak-doc-name').textContent = 'Dokumen: ' + judul;
  document.getElementById('tolak-form').action = '/archives/' + id + '/tolak';
  const modal = document.getElementById('tolak-modal');
  modal.style.display = 'flex';
}
function closeTolakModal() {
  document.getElementById('tolak-modal').style.display = 'none';
}
document.getElementById('tolak-modal')?.addEventListener('click', function(e) {
  if (e.target === this) closeTolakModal();
});
</script>
@endpush