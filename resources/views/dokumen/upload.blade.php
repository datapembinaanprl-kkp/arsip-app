@extends('layouts.app')
@section('title', 'Unggah Dokumen')

@section('content')

<div class="page-header">
  <div>
    <a href="{{ route('dokumen.index') }}" style="font-size:.83rem;color:var(--text-3);text-decoration:none;display:inline-flex;align-items:center;gap:5px;margin-bottom:8px">
      ← Kembali ke Arsip
    </a>
    <h1 class="page-title">Unggah Dokumen</h1>
    <p class="page-sub">Unggah satu atau beberapa file sekaligus (batch upload)</p>
  </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
  <div>
    <strong>Terdapat kesalahan:</strong>
    <ul style="margin-top:6px;padding-left:16px">
      @foreach($errors->all() as $err)
        <li>{{ $err }}</li>
      @endforeach
    </ul>
  </div>
</div>
@endif

<form method="POST" action="{{ route('dokumen.store') }}" enctype="multipart/form-data" id="upload-form">
  @csrf

  <div style="display:grid;grid-template-columns:1fr 380px;gap:20px;align-items:start">

    {{-- Left: File upload --}}
    <div style="display:flex;flex-direction:column;gap:16px">

      {{-- Dropzone --}}
      <div class="card">
        <h3 style="margin-bottom:14px">File Dokumen</h3>
        <div class="dropzone" id="dropzone" onclick="document.getElementById('file-input').click()">
          <input type="file" name="files[]" id="file-input" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.mp4,.avi,.mov,.zip,.geojson,.txt" style="display:none" />
          <div class="dz-icon">📎</div>
          <p class="dz-text">Seret file ke sini atau <span style="color:var(--blue);font-weight:600">pilih file</span></p>
          <p class="dz-sub">PDF, Word, Excel, PowerPoint, Gambar, Video, ZIP — Maks. 100MB/file, 20 file</p>
        </div>

        <div class="file-list" id="file-list"></div>

        <div id="file-error" class="form-error" style="display:none"></div>
      </div>

      {{-- Metadata tambahan --}}
      <div class="card">
        <h3 style="margin-bottom:16px">Informasi Tambahan</h3>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Pengirim</label>
            <input type="text" name="pengirim" value="{{ old('pengirim') }}" class="input" placeholder="Nama/instansi pengirim" />
          </div>
          <div class="form-group">
            <label class="form-label">Penerima</label>
            <input type="text" name="penerima" value="{{ old('penerima') }}" class="input" placeholder="Nama/instansi penerima" />
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Perihal</label>
          <input type="text" name="perihal" value="{{ old('perihal') }}" class="input" placeholder="Perihal / subjek dokumen" />
        </div>
        <div class="form-group">
          <label class="form-label">Tags</label>
          <input type="text" id="tag-input" class="input" placeholder="Ketik tag lalu tekan Enter..." />
          <div id="tag-list" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px"></div>
          <div id="tags-hidden"></div>
          <p class="form-hint">Tekan Enter untuk menambahkan tag</p>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">Keterangan</label>
          <textarea name="keterangan" class="input" rows="2" placeholder="Catatan tambahan...">{{ old('keterangan') }}</textarea>
        </div>
      </div>

    </div>

    {{-- Right: Document info --}}
    <div class="card" style="position:sticky;top:20px">
      <h3 style="margin-bottom:16px">Informasi Dokumen</h3>

      <div class="form-group">
        <label class="form-label" for="judul">Judul Dokumen <span class="req">*</span></label>
        <input type="text" name="judul" id="judul" value="{{ old('judul') }}"
               class="input {{ $errors->has('judul') ? 'is-error' : '' }}"
               placeholder="Contoh: Laporan Keuangan Q1 2024" required />
        @error('judul') <div class="form-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="tipe_dokumen">Tipe Dokumen <span class="req">*</span></label>
        <select name="tipe_dokumen" id="tipe_dokumen"
                class="input {{ $errors->has('tipe_dokumen') ? 'is-error' : '' }}" required>
          <option value="">— Pilih Tipe —</option>
          @foreach($tipeOptions as $val => $label)
            <option value="{{ $val }}" {{ old('tipe_dokumen') == $val ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
        @error('tipe_dokumen') <div class="form-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="department_id">Bidang <span class="req">*</span></label>
        <select name="department_id" id="department_id"
                class="input {{ $errors->has('department_id') ? 'is-error' : '' }}" required>
          <option value="">— Pilih Bidang —</option>
          @foreach($departments as $dept)
            <option value="{{ $dept->id }}"
              {{ (old('department_id', auth()->user()->department_id) == $dept->id) ? 'selected' : '' }}>
              {{ $dept->nama_bidang }}
            </option>
          @endforeach
        </select>
        @error('department_id') <div class="form-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="nomor_surat">Nomor Surat</label>
        <input type="text" name="nomor_surat" id="nomor_surat" value="{{ old('nomor_surat') }}"
               class="input" placeholder="Contoh: 001/SEK/I/2024" />
      </div>

      <div class="form-group">
        <label class="form-label" for="tanggal_dokumen">Tanggal Dokumen <span class="req">*</span></label>
        <input type="date" name="tanggal_dokumen" id="tanggal_dokumen"
               value="{{ old('tanggal_dokumen', now()->format('Y-m-d')) }}"
               class="input {{ $errors->has('tanggal_dokumen') ? 'is-error' : '' }}" required />
        @error('tanggal_dokumen') <div class="form-error">{{ $message }}</div> @enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="tanggal_retensi">Tanggal Retensi</label>
        <input type="date" name="tanggal_retensi" id="tanggal_retensi"
               value="{{ old('tanggal_retensi') }}" class="input" />
        <p class="form-hint">Kosongkan jika tidak ada jadwal retensi (ISO 15489)</p>
      </div>

      <div class="divider"></div>

      {{-- Upload progress --}}
      <div id="upload-progress" style="display:none;margin-bottom:14px">
        <div style="display:flex;justify-content:space-between;font-size:.8rem;margin-bottom:5px">
          <span style="color:var(--text-2)">Mengunggah...</span>
          <span id="progress-pct" style="color:var(--blue);font-weight:600">0%</span>
        </div>
        <div style="height:5px;background:var(--border);border-radius:99px;overflow:hidden">
          <div id="progress-bar" style="height:100%;background:var(--blue);border-radius:99px;transition:width .3s;width:0%"></div>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:8px">
        <button type="submit" class="btn btn-primary btn-lg w-full" id="submit-btn">
          <span id="submit-text">Simpan ke Arsip</span>
          <span id="submit-spinner" class="spinner" style="display:none;width:16px;height:16px;border-color:rgba(255,255,255,.3);border-top-color:#fff"></span>
        </button>
        <a href="{{ route('dokumen.index') }}" class="btn btn-secondary btn-lg w-full" style="text-align:center">Batal</a>
      </div>

    </div>

  </div>

</form>
@endsection

@push('scripts')
<script>
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('file-input');
const fileList  = document.getElementById('file-list');
let selectedFiles = [];

// Drag events
['dragover','dragenter'].forEach(e => dropzone.addEventListener(e, ev => { ev.preventDefault(); dropzone.classList.add('drag-over'); }));
['dragleave','drop'].forEach(e => dropzone.addEventListener(e, ev => { ev.preventDefault?.(); dropzone.classList.remove('drag-over'); }));
dropzone.addEventListener('drop', e => addFiles(Array.from(e.dataTransfer.files)));
fileInput.addEventListener('change', () => addFiles(Array.from(fileInput.files)));

function addFiles(files) {
  selectedFiles = [...selectedFiles, ...files].slice(0, 20);
  renderFileList();
}

function removeFile(idx) {
  selectedFiles.splice(idx, 1);
  renderFileList();
}

function renderFileList() {
  fileList.innerHTML = '';
  const dt = new DataTransfer();
  selectedFiles.forEach((f, i) => {
    dt.items.add(f);
    const icon = f.type.includes('pdf') ? '📄' : f.type.includes('image') ? '🖼' : f.type.includes('video') ? '🎬' : '📎';
    const size = f.size < 1048576 ? (f.size/1024).toFixed(1)+' KB' : (f.size/1048576).toFixed(1)+' MB';
    fileList.insertAdjacentHTML('beforeend', `
      <div class="file-chip">
        <span class="fc-icon">${icon}</span>
        <span class="fc-name">${f.name}</span>
        <span class="fc-size">${size}</span>
        <button type="button" class="fc-remove" onclick="removeFile(${i})">✕</button>
      </div>
    `);
  });
  fileInput.files = dt.files;
}

// Tags
const tagInput = document.getElementById('tag-input');
const tagList  = document.getElementById('tag-list');
const tagsHidden = document.getElementById('tags-hidden');
let tags = [];

tagInput.addEventListener('keydown', e => {
  if (e.key === 'Enter') {
    e.preventDefault();
    const t = tagInput.value.trim();
    if (t && !tags.includes(t)) { tags.push(t); renderTags(); }
    tagInput.value = '';
  }
});

function removeTag(i) { tags.splice(i,1); renderTags(); }

function renderTags() {
  tagList.innerHTML = tags.map((t,i) => `
    <span class="badge badge-blue" style="cursor:default">
      ${t}
      <button type="button" onclick="removeTag(${i})" style="background:none;border:none;cursor:pointer;margin-left:3px;font-size:11px;color:var(--blue)">✕</button>
    </span>
  `).join('');
  tagsHidden.innerHTML = tags.map(t => `<input type="hidden" name="tags[]" value="${t}" />`).join('');
}

// Submit with progress
document.getElementById('upload-form')?.addEventListener('submit', function(e) {
  if (selectedFiles.length === 0) {
    e.preventDefault();
    document.getElementById('file-error').textContent = 'Pilih minimal satu file untuk diunggah.';
    document.getElementById('file-error').style.display = 'block';
    return;
  }
  document.getElementById('submit-text').style.display = 'none';
  document.getElementById('submit-spinner').style.display = 'inline-block';
  document.getElementById('submit-btn').disabled = true;
  document.getElementById('upload-progress').style.display = 'block';
});
</script>
@endpush