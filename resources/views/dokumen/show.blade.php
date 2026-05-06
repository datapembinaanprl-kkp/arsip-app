@extends('layouts.app')
@section('title', $document->judul)

@section('content')

<div style="margin-bottom:20px">
  <a href="{{ route('dokumen.index') }}" style="font-size:.83rem;color:var(--text-3);text-decoration:none;display:inline-flex;align-items:center;gap:5px">
    ← Kembali ke Arsip
  </a>
</div>

<div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start">

  {{-- Left: Preview + Detail --}}
  <div style="display:flex;flex-direction:column;gap:16px">

    {{-- Document preview --}}
    <div class="card" style="padding:0;overflow:hidden;min-height:500px">
      <div style="height:48px;background:var(--surface-2);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 16px;gap:12px">
        <span style="font-size:1.2rem">
          @if(str_contains($document->mime_type,'pdf'))📄
          @elseif(str_contains($document->mime_type,'image'))🖼
          @elseif(str_contains($document->mime_type,'video'))🎬
          @else📎@endif
        </span>
        <span style="font-size:.87rem;font-weight:500;color:var(--text-1);flex:1" class="truncate">{{ $document->file_name }}</span>
        <div style="display:flex;gap:8px">
          <button onclick="downloadDoc()" class="btn btn-primary btn-sm">↓ Unduh</button>
          @hasrole('admin_sistem')
          <button onclick="openDeleteModal()" class="btn btn-danger btn-sm">Hapus</button>
          @endhasrole
        </div>
      </div>

      {{-- Preview area --}}
      <div id="preview-area" style="padding:20px;min-height:460px;display:flex;align-items:center;justify-content:center">
        <div id="preview-loader" style="display:flex;flex-direction:column;align-items:center;gap:10px;color:var(--text-3)">
          <div class="spinner"></div>
          <span style="font-size:.85rem">Memuat preview...</span>
        </div>
        <iframe id="preview-pdf" src="" style="display:none;width:100%;height:520px;border:none;border-radius:var(--radius-sm)"></iframe>
        <img id="preview-img" src="" alt="{{ $document->judul }}" style="display:none;max-width:100%;border-radius:var(--radius-sm)" />
        <video id="preview-video" controls style="display:none;width:100%;border-radius:var(--radius-sm)"></video>
        <div id="preview-map" style="display:none;width:100%;height:480px;border-radius:var(--radius-sm);overflow:hidden"></div>
        <div id="preview-unsupported" style="display:none;text-align:center">
          <div style="font-size:3rem;opacity:.4;margin-bottom:12px">📎</div>
          <p style="color:var(--text-2);font-weight:500">Preview tidak tersedia</p>
          <p class="text-muted text-sm">Unduh file untuk membukanya.</p>
          <button onclick="downloadDoc()" class="btn btn-primary" style="margin-top:12px">↓ Unduh File</button>
        </div>
      </div>
    </div>

    {{-- Audit trail --}}
    <div class="card">
      <div class="card-header">
        <span class="card-title">Riwayat Akses</span>
      </div>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Aksi</th>
              <th>Pengguna</th>
              <th>IP Address</th>
              <th>Waktu</th>
            </tr>
          </thead>
          <tbody>
            @forelse($auditLogs as $log)
            <tr>
              <td>
                <span class="badge {{ match($log->aksi) { 'upload'=>'badge-green','download'=>'badge-blue','view'=>'badge-gray','delete'=>'badge-red','restore'=>'badge-purple',default=>'badge-gray' } }}">
                  {{ $log->aksi }}
                </span>
              </td>
              <td style="font-size:.83rem">{{ $log->user->nama ?? 'Sistem' }}</td>
              <td class="mono" style="color:var(--text-3)">{{ $log->ip_address }}</td>
              <td style="font-size:.8rem;color:var(--text-3)">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
            </tr>
            @empty
            <tr><td colspan="4" style="text-align:center;color:var(--text-3);padding:24px">Belum ada riwayat.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>

  {{-- Right: Metadata --}}
  <div style="display:flex;flex-direction:column;gap:16px">

    {{-- Main info --}}
    <div class="card">
      <h2 style="font-size:1rem;font-weight:700;color:var(--text-1);margin-bottom:4px;line-height:1.4">{{ $document->judul }}</h2>
      <span class="badge
        {{ match($document->tipe_dokumen){ 'surat_masuk','surat_keluar'=>'badge-blue','laporan_keuangan'=>'badge-amber','kontrak'=>'badge-purple','kepegawaian'=>'badge-green',default=>'badge-gray' } }}"
        style="margin-bottom:16px;display:inline-flex">
        {{ $document->tipe_dokumen_label }}
      </span>

      @if($document->tanggal_retensi && $document->tanggal_retensi->diffInDays(now()) <= 30)
      <div class="retention-bar">
        ⚠ Retensi berakhir {{ $document->tanggal_retensi->diffForHumans() }}
      </div>
      @endif

      <div class="divider"></div>

      @php
      $metaItems = [
        ['label' => 'Nomor Arsip',   'value' => $document->nomor_dokumen, 'mono' => true],
        ['label' => 'Nomor Surat',   'value' => $document->nomor_surat],
        ['label' => 'Bidang',        'value' => $document->department->nama_bidang],
        ['label' => 'Tanggal',       'value' => $document->tanggal_dokumen->format('d F Y')],
        ['label' => 'Tanggal Retensi','value' => $document->tanggal_retensi?->format('d F Y') ?? '—'],
        ['label' => 'Status',        'value' => $document->status],
        ['label' => 'Ukuran File',   'value' => $document->file_size_human, 'mono' => true],
        ['label' => 'Format',        'value' => strtoupper(pathinfo($document->file_name, PATHINFO_EXTENSION)), 'mono' => true],
        ['label' => 'Diunggah Oleh', 'value' => $document->uploader->nama],
        ['label' => 'Tanggal Upload','value' => $document->created_at->format('d F Y H:i')],
      ];
      @endphp

      @foreach($metaItems as $item)
      @if($item['value'])
      <div style="display:flex;justify-content:space-between;align-items:flex-start;padding:7px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:.78rem;color:var(--text-3);font-weight:500;flex-shrink:0;width:120px">{{ $item['label'] }}</span>
        <span style="font-size:.82rem;color:var(--text-1);text-align:right;{{ ($item['mono']??false) ? 'font-family:var(--mono)' : '' }}">{{ $item['value'] }}</span>
      </div>
      @endif
      @endforeach
    </div>

    {{-- Metadata tambahan --}}
    @if($document->metadata)
    <div class="card">
      <h3 style="margin-bottom:14px">Metadata Surat</h3>
      @foreach(['pengirim'=>'Pengirim','penerima'=>'Penerima','perihal'=>'Perihal','keterangan'=>'Keterangan'] as $key => $label)
      @if($document->metadata->$key)
      <div style="margin-bottom:10px">
        <p style="font-size:.76rem;font-weight:600;color:var(--text-3);margin-bottom:2px">{{ $label }}</p>
        <p style="font-size:.85rem;color:var(--text-1)">{{ $document->metadata->$key }}</p>
      </div>
      @endif
      @endforeach

      @if($document->metadata->tags && count($document->metadata->tags))
      <div>
        <p style="font-size:.76rem;font-weight:600;color:var(--text-3);margin-bottom:6px">Tags</p>
        <div style="display:flex;flex-wrap:wrap;gap:5px">
          @foreach($document->metadata->tags as $tag)
          <span class="badge badge-blue">{{ $tag }}</span>
          @endforeach
        </div>
      </div>
      @endif
    </div>
    @endif

    {{-- Versions --}}
    @if($document->versions->count() > 0)
    <div class="card">
      <h3 style="margin-bottom:14px">Riwayat Versi</h3>
      @foreach($document->versions as $ver)
      <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--border)">
        <div>
          <span class="badge badge-gray">v{{ $ver->versi }}</span>
          <span style="font-size:.78rem;color:var(--text-3);margin-left:8px">{{ $ver->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <span style="font-size:.78rem;color:var(--text-2)">{{ $ver->uploader->nama }}</span>
      </div>
      @endforeach
    </div>
    @endif

  </div>
</div>

{{-- Delete Modal --}}
<div class="modal-backdrop" id="delete-modal">
  <div class="modal">
    <h3 class="modal-title">Hapus Dokumen</h3>
    <p class="modal-sub">Dokumen ini akan ditandai terhapus (soft delete). Anda bisa memulihkannya kembali.</p>
    <form method="POST" action="{{ route('dokumen.destroy', $document) }}">
      @csrf @method('DELETE')
      <div class="form-group">
        <label class="form-label">Alasan <span class="req">*</span></label>
        <textarea name="alasan" class="input" rows="3" required minlength="10" placeholder="Min. 10 karakter..."></textarea>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('delete-modal').classList.remove('open')">Batal</button>
        <button type="submit" class="btn btn-danger" style="background:var(--red);color:#fff">Hapus</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
// Auto-load preview on page load
window.addEventListener('DOMContentLoaded', () => {
  fetch(`/dokumen/{{ $document->id }}/url?type=preview`, {
    headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    document.getElementById('preview-loader').style.display = 'none';
    window._previewUrl = data.url;
    window._fileName   = data.file_name;

    const mime = '{{ $document->mime_type }}';
    const tipe = '{{ $document->tipe_dokumen }}';

    if (tipe === 'survey_gis') {
      document.getElementById('preview-map').style.display = 'block';
    } else if (mime.includes('pdf')) {
      const f = document.getElementById('preview-pdf');
      f.src = data.url + '#toolbar=0';
      f.style.display = 'block';
    } else if (mime.startsWith('image/')) {
      const i = document.getElementById('preview-img');
      i.src = data.url; i.style.display = 'block';
    } else if (mime.startsWith('video/')) {
      const v = document.getElementById('preview-video');
      v.src = data.url; v.style.display = 'block';
    } else {
      document.getElementById('preview-unsupported').style.display = 'block';
    }
  })
  .catch(() => {
    document.getElementById('preview-loader').style.display = 'none';
    document.getElementById('preview-unsupported').style.display = 'block';
  });
});

function downloadDoc() {
  fetch(`/dokumen/{{ $document->id }}/url?type=download`, {
    headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(d => { const a = document.createElement('a'); a.href = d.url; a.download = d.file_name; a.click(); });
}

function openDeleteModal() {
  document.getElementById('delete-modal').classList.add('open');
}
document.getElementById('delete-modal')?.addEventListener('click', e => {
  if (e.target === e.currentTarget) e.currentTarget.classList.remove('open');
});
</script>
@endpush