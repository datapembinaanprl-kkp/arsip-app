@extends('layouts.app')
@section('title', 'Arsip Dokumen')

@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Arsip Dokumen</h1>
    <p class="page-sub">{{ number_format($documents->total()) }} dokumen ditemukan</p>
  </div>
  @cannot('auditor')
  <a href="{{ route('dokumen.upload') }}" class="btn btn-primary">
    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
    </svg>
    Unggah Dokumen
  </a>
  @endcannot
</div>

{{-- Filter bar --}}
<div class="card mb-5">
  <form method="GET" action="{{ route('dokumen.index') }}" id="filter-form">
    <div style="display:flex;flex-direction:column;gap:12px">

      {{-- Search --}}
      <div class="search-wrap">
        <span class="search-icon">
          <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/>
          </svg>
        </span>
        <input type="text" name="q" value="{{ request('q') }}"
               class="input" placeholder="Cari judul, nomor surat, pengirim, isi dokumen..." />
      </div>

      {{-- Filter row --}}
      <div style="display:flex;flex-wrap:wrap;gap:10px;align-items:flex-end">

        <div style="flex:1;min-width:160px">
          <label class="form-label" style="margin-bottom:4px">Tipe Dokumen</label>
          <select name="tipe" class="input">
            <option value="">Semua Tipe</option>
            @foreach($tipeOptions as $val => $label)
              <option value="{{ $val }}" {{ request('tipe') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>

        @hasanyrole('admin_sistem|kepala_dinas|auditor_internal')
        <div style="flex:1;min-width:160px">
          <label class="form-label" style="margin-bottom:4px">Bidang</label>
          <select name="department_id" class="input">
            <option value="">Semua Bidang</option>
            @foreach($departments as $dept)
              <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->nama_bidang }}</option>
            @endforeach
          </select>
        </div>
        @endhasanyrole

        <div style="flex:1;min-width:140px">
          <label class="form-label" style="margin-bottom:4px">Dari Tanggal</label>
          <input type="date" name="dari" value="{{ request('dari') }}" class="input" />
        </div>

        <div style="flex:1;min-width:140px">
          <label class="form-label" style="margin-bottom:4px">Sampai Tanggal</label>
          <input type="date" name="sampai" value="{{ request('sampai') }}" class="input" />
        </div>

        <div style="display:flex;gap:8px;flex-shrink:0">
          <button type="submit" class="btn btn-primary">Cari</button>
          @if(request()->hasAny(['q','tipe','department_id','dari','sampai']))
            <a href="{{ route('dokumen.index') }}" class="btn btn-secondary">Reset</a>
          @endif
        </div>

      </div>
    </div>
  </form>
</div>

{{-- Document list --}}
<div class="card" style="padding:0;overflow:hidden">

  @if($documents->isEmpty())
  <div style="text-align:center;padding:60px 20px">
    <div style="font-size:3rem;margin-bottom:12px;opacity:.4">📂</div>
    <p style="font-weight:600;color:var(--text-1)">Tidak ada dokumen ditemukan</p>
    <p class="text-muted text-sm" style="margin-top:4px">Coba ubah filter atau unggah dokumen baru.</p>
    @cannot('auditor')
    <a href="{{ route('dokumen.upload') }}" class="btn btn-primary" style="margin-top:16px">Unggah Sekarang</a>
    @endcannot
  </div>
  @else

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th style="width:42%">Dokumen</th>
          <th>Bidang</th>
          <th>Tipe</th>
          <th>Tanggal</th>
          <th>Ukuran</th>
          <th style="text-align:right">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($documents as $doc)
        <tr>
          {{-- Dokumen info --}}
          <td>
            <div style="display:flex;align-items:center;gap:11px">
              <div style="width:38px;height:38px;border-radius:var(--radius-sm);background:var(--surface-2);display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">
                @if(str_contains($doc->mime_type, 'pdf'))📄
                @elseif(str_contains($doc->mime_type, 'image'))🖼
                @elseif(str_contains($doc->mime_type, 'video'))🎬
                @elseif(str_contains($doc->mime_type, 'word') || str_contains($doc->mime_type,'document'))📝
                @elseif(str_contains($doc->mime_type, 'excel') || str_contains($doc->mime_type,'sheet'))📊
                @else📎@endif
              </div>
              <div style="min-width:0">
                <a href="{{ route('dokumen.show', $doc) }}"
                   style="font-size:.87rem;font-weight:600;color:var(--text-1);text-decoration:none;display:block"
                   class="truncate"
                   onmouseover="this.style.color='var(--blue)'" onmouseout="this.style.color='var(--text-1)'">
                  {{ $doc->judul }}
                </a>
                <span style="font-size:.74rem;color:var(--text-3);font-family:var(--mono)">
                  {{ $doc->nomor_dokumen }}
                  @if($doc->nomor_surat) · {{ $doc->nomor_surat }} @endif
                </span>
                {{-- Retention warning --}}
                @if($doc->tanggal_retensi && $doc->tanggal_retensi->diffInDays(now()) <= 30)
                <div class="retention-bar" style="padding:3px 8px;font-size:.71rem;display:inline-flex;margin-top:3px">
                  ⚠ Retensi {{ $doc->tanggal_retensi->diffForHumans() }}
                </div>
                @endif
              </div>
            </div>
          </td>

          <td>
            <span style="font-size:.83rem;color:var(--text-2)">{{ $doc->department->nama_bidang }}</span>
          </td>

          <td>
            <span class="badge
              {{ match($doc->tipe_dokumen) {
                'surat_masuk','surat_keluar' => 'badge-blue',
                'laporan_keuangan'           => 'badge-amber',
                'kontrak'                    => 'badge-purple',
                'kepegawaian'                => 'badge-green',
                'survey_gis'                 => 'badge-green',
                default                      => 'badge-gray'
              } }}">
              {{ $doc->tipe_dokumen_label }}
            </span>
          </td>

          <td>
            <span style="font-size:.83rem;color:var(--text-2)">{{ $doc->tanggal_dokumen->format('d M Y') }}</span>
          </td>

          <td>
            <span style="font-size:.8rem;color:var(--text-3);font-family:var(--mono)">{{ $doc->file_size_human }}</span>
          </td>

          <td style="text-align:right">
            <div style="display:flex;justify-content:flex-end;gap:6px">
              {{-- Preview --}}
              <button class="btn btn-secondary btn-icon btn-sm"
                      title="Preview"
                      onclick="openPreview('{{ $doc->id }}','{{ addslashes($doc->judul) }}','{{ $doc->mime_type }}','{{ $doc->tipe_dokumen }}')">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>

              {{-- Detail --}}
              <a href="{{ route('dokumen.show', $doc) }}" class="btn btn-secondary btn-icon btn-sm" title="Detail">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
              </a>

              {{-- Hapus (admin only) --}}
              @hasrole('admin_sistem')
              <button class="btn btn-danger btn-icon btn-sm"
                      title="Hapus"
                      onclick="openDeleteModal('{{ $doc->id }}','{{ addslashes($doc->judul) }}')">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
              @endhasrole
            </div>
          </td>

        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  @if($documents->hasPages())
  <div style="padding:14px 20px;border-top:1px solid var(--border);display:flex;align-items:center;justify-content:space-between">
    <span style="font-size:.8rem;color:var(--text-3)">
      Menampilkan {{ $documents->firstItem() }}–{{ $documents->lastItem() }} dari {{ $documents->total() }} dokumen
    </span>
    <div class="pagination" style="padding:0">
      @if($documents->onFirstPage())
        <span class="page-item disabled">←</span>
      @else
        <a href="{{ $documents->previousPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="page-item">←</a>
      @endif

      @foreach($documents->getUrlRange(max(1,$documents->currentPage()-2), min($documents->lastPage(),$documents->currentPage()+2)) as $page => $url)
        <a href="{{ $url }}&{{ http_build_query(request()->except('page')) }}"
           class="page-item {{ $page == $documents->currentPage() ? 'active' : '' }}">{{ $page }}</a>
      @endforeach

      @if($documents->hasMorePages())
        <a href="{{ $documents->nextPageUrl() }}&{{ http_build_query(request()->except('page')) }}" class="page-item">→</a>
      @else
        <span class="page-item disabled">→</span>
      @endif
    </div>
  </div>
  @endif

  @endif

</div>

{{-- Preview Modal --}}
<div class="preview-overlay" id="preview-overlay">
  <div class="preview-toolbar">
    <div style="min-width:0;flex:1;margin-right:16px">
      <p id="preview-title" style="font-size:.9rem;font-weight:600;color:#fff" class="truncate"></p>
      <p id="preview-sub" style="font-size:.76rem;color:rgba(255,255,255,.45)"></p>
    </div>
    <div style="display:flex;align-items:center;gap:8px">
      <button id="preview-download" class="btn btn-primary btn-sm">↓ Unduh</button>
      <button onclick="closePreview()" class="btn btn-secondary btn-sm" style="background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.12);color:#fff">✕ Tutup</button>
    </div>
  </div>
  <div class="preview-content" id="preview-content">
    <div id="preview-loader" style="display:flex;flex-direction:column;align-items:center;gap:12px;color:rgba(255,255,255,.6);margin-top:80px">
      <div class="spinner" style="width:28px;height:28px;border-color:rgba(255,255,255,.2);border-top-color:rgba(255,255,255,.8)"></div>
      <span style="font-size:.85rem">Memuat dokumen...</span>
    </div>
    <iframe id="preview-iframe" src="" style="display:none;width:100%;max-width:900px;height:calc(100vh - 100px);border-radius:10px;border:none;background:#fff"></iframe>
    <img id="preview-img" src="" style="display:none;max-width:900px;width:100%;border-radius:10px;max-height:calc(100vh - 100px);object-fit:contain" />
    <video id="preview-video" src="" controls style="display:none;max-width:900px;width:100%;border-radius:10px;max-height:calc(100vh - 100px)"></video>
    <div id="preview-map" style="display:none;width:100%;max-width:900px;height:calc(100vh - 120px);border-radius:10px;overflow:hidden"></div>
  </div>
</div>

{{-- Delete Modal --}}
<div class="modal-backdrop" id="delete-modal">
  <div class="modal">
    <h3 class="modal-title">Hapus Dokumen</h3>
    <p class="modal-sub">Dokumen "<span id="delete-doc-name" style="font-weight:600;color:var(--text-1)"></span>" akan ditandai terhapus. Tindakan ini tercatat di audit log.</p>
    <form method="POST" id="delete-form">
      @csrf @method('DELETE')
      <div class="form-group">
        <label class="form-label">Alasan Penghapusan <span class="req">*</span></label>
        <textarea name="alasan" class="input" rows="3" placeholder="Tuliskan alasan penghapusan (min. 10 karakter)..." required minlength="10"></textarea>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
        <button type="submit" class="btn btn-danger" style="background:var(--red);color:#fff">Hapus Dokumen</button>
      </div>
    </form>
  </div>
</div>

@endsection

@push('scripts')
<script>
let currentDocId = null;

function openPreview(id, judul, mime, tipe) {
  const overlay = document.getElementById('preview-overlay');
  document.getElementById('preview-title').textContent = judul;
  document.getElementById('preview-loader').style.display = 'flex';
  document.getElementById('preview-iframe').style.display = 'none';
  document.getElementById('preview-img').style.display = 'none';
  document.getElementById('preview-video').style.display = 'none';
  document.getElementById('preview-map').style.display = 'none';
  overlay.classList.add('open');
  currentDocId = id;

  document.getElementById('preview-download').onclick = () => downloadDoc(id);

  fetch(`/dokumen/${id}/url?type=preview`, {
    headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(data => {
    document.getElementById('preview-loader').style.display = 'none';
    document.getElementById('preview-sub').textContent = data.file_name;

    if (tipe === 'survey_gis') {
      document.getElementById('preview-map').style.display = 'block';
      initMiniMap(id);
    } else if (mime.includes('pdf')) {
      const iframe = document.getElementById('preview-iframe');
      iframe.src = data.url + '#toolbar=0';
      iframe.style.display = 'block';
    } else if (mime.startsWith('image/')) {
      const img = document.getElementById('preview-img');
      img.src = data.url;
      img.style.display = 'block';
    } else if (mime.startsWith('video/')) {
      const vid = document.getElementById('preview-video');
      vid.src = data.url;
      vid.style.display = 'block';
    } else {
      document.getElementById('preview-loader').innerHTML = `<div style="font-size:3rem">📎</div><p style="color:rgba(255,255,255,.6)">Preview tidak tersedia.</p><button class="btn btn-primary" onclick="downloadDoc('${id}')">↓ Unduh File</button>`;
      document.getElementById('preview-loader').style.display = 'flex';
    }
  });
}

function closePreview() {
  document.getElementById('preview-overlay').classList.remove('open');
  document.getElementById('preview-video').pause?.();
  document.getElementById('preview-video').src = '';
  document.getElementById('preview-iframe').src = '';
}

function downloadDoc(id) {
  fetch(`/dokumen/${id}/url?type=download`, {
    headers: { 'X-CSRF-TOKEN': window.CSRF_TOKEN, 'Accept': 'application/json' }
  })
  .then(r => r.json())
  .then(d => { const a = document.createElement('a'); a.href = d.url; a.download = d.file_name; a.click(); });
}

function openDeleteModal(id, judul) {
  document.getElementById('delete-doc-name').textContent = judul;
  document.getElementById('delete-form').action = `/dokumen/${id}`;
  document.getElementById('delete-modal').classList.add('open');
}
function closeDeleteModal() {
  document.getElementById('delete-modal').classList.remove('open');
}

// Close on backdrop click
document.getElementById('delete-modal')?.addEventListener('click', e => {
  if (e.target === e.currentTarget) closeDeleteModal();
});

document.addEventListener('keydown', e => {
  if (e.key === 'Escape') { closePreview(); closeDeleteModal(); }
});
</script>
@endpush