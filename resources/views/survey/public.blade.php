<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>{{ $survey->judul }} — Survey</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    body { background: var(--bg); padding: 0; }
    .pub-wrap { min-height: 100vh; display: flex; flex-direction: column; align-items: center; padding: 32px 16px 60px; }
    .pub-card  { width: 100%; max-width: 600px; }
    .pub-header { text-align: center; margin-bottom: 28px; }
    .pub-header .mark { width: 52px; height: 52px; background: var(--blue); border-radius: var(--radius); display: inline-flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 14px; }
    .pub-header h1 { font-size: 1.4rem; font-weight: 700; color: var(--text-1); }
    .pub-header p { font-size: .88rem; color: var(--text-3); margin-top: 6px; }
    .section-title { font-size: .88rem; font-weight: 600; color: var(--text-3); text-transform: uppercase; letter-spacing: .06em; padding-bottom: 10px; border-bottom: 1px solid var(--border); margin-bottom: 16px; }
    #mini-map { height: 200px; border-radius: var(--radius-sm); border: 1.5px solid var(--border); overflow: hidden; margin-top: 8px; }
  </style>
</head>
<body>

<div class="pub-wrap">
  <div class="pub-card">

    {{-- Header --}}
    <div class="pub-header">
      <div class="mark">📋</div>
      <h1>{{ $survey->judul }}</h1>
      @if($survey->deskripsi)
      <p>{{ $survey->deskripsi }}</p>
      @endif
      @if($survey->batas_waktu)
      <p style="color:var(--amber);font-size:.82rem;margin-top:8px">⏱ Batas waktu: {{ $survey->batas_waktu->format('d F Y, H:i') }} WIB</p>
      @endif
    </div>

    {{-- Success state --}}
    @if(session('survey_success'))
    <div class="card" style="text-align:center;padding:48px">
      <div style="font-size:3.5rem;margin-bottom:16px">✅</div>
      <h2 style="font-size:1.2rem;font-weight:700;color:var(--text-1)">Data Berhasil Dikirim!</h2>
      <p class="text-muted" style="margin-top:8px">Terima kasih atas partisipasi Anda.</p>
      @if(session('submission_id'))
      <p style="font-size:.78rem;color:var(--text-3);margin-top:12px;font-family:var(--mono)">
        ID: {{ session('submission_id') }}
      </p>
      @endif
    </div>

    {{-- Closed state --}}
    @elseif(!$survey->isOpen())
    <div class="card" style="text-align:center;padding:48px">
      <div style="font-size:3.5rem;margin-bottom:16px">🔒</div>
      <h2 style="font-size:1.1rem;font-weight:700;color:var(--text-1)">Survey Sudah Ditutup</h2>
      <p class="text-muted" style="margin-top:8px">Survey ini tidak lagi menerima kiriman data.</p>
    </div>

    {{-- Form --}}
    @else
    <form method="POST" action="{{ route('survey.public.submit', $survey->token_akses) }}" enctype="multipart/form-data" id="survey-form">
      @csrf

      @if($errors->any())
      <div class="alert alert-danger">
        <div>
          <strong>Perbaiki kesalahan berikut:</strong>
          <ul style="padding-left:16px;margin-top:4px">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        </div>
      </div>
      @endif

      {{-- Identitas responden --}}
      <div class="card mb-4">
        <div class="section-title">Identitas Responden</div>
        <div class="form-group">
          <label class="form-label">Nama Lengkap <span class="req">*</span></label>
          <input type="text" name="nama_responden" value="{{ old('nama_responden') }}" class="input" required placeholder="Nama Anda" />
        </div>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Instansi / Lembaga</label>
            <input type="text" name="instansi" value="{{ old('instansi') }}" class="input" placeholder="Opsional" />
          </div>
          <div class="form-group">
            <label class="form-label">No. Telepon</label>
            <input type="tel" name="no_telp" value="{{ old('no_telp') }}" class="input" placeholder="08xx..." />
          </div>
        </div>
      </div>

      {{-- Lokasi / GIS --}}
      <div class="card mb-4">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
          <div class="section-title" style="margin-bottom:0;border:none;padding:0">Lokasi / Koordinat</div>
          <button type="button" class="btn btn-secondary btn-sm" id="detect-btn" onclick="detectLocation()">
            📍 Deteksi Otomatis
          </button>
        </div>
        <div class="grid-2">
          <div class="form-group">
            <label class="form-label">Latitude</label>
            <input type="number" name="latitude" id="lat-input" step="any" value="{{ old('latitude') }}" class="input" placeholder="-7.250000" oninput="updateMap()" />
          </div>
          <div class="form-group">
            <label class="form-label">Longitude</label>
            <input type="number" name="longitude" id="lng-input" step="any" value="{{ old('longitude') }}" class="input" placeholder="112.750000" oninput="updateMap()" />
          </div>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label class="form-label">Alamat Lokasi</label>
          <input type="text" name="alamat_lokasi" value="{{ old('alamat_lokasi') }}" class="input" placeholder="Nama jalan, kelurahan, kecamatan..." />
        </div>
        <div id="mini-map" style="display:none"></div>
      </div>

      {{-- Dynamic fields --}}
      <div class="card mb-4">
        <div class="section-title">Data Lapangan</div>
        @foreach($survey->fields as $idx => $field)
        <div class="form-group">
          <label class="form-label">
            {{ $field['label'] }}
            @if($field['required'] ?? false)<span class="req">*</span>@endif
          </label>

          @if(in_array($field['type'], ['text','number','date']))
            <input type="{{ $field['type'] }}" name="data_lapangan[{{ $field['label'] }}]"
                   value="{{ old('data_lapangan.'.$field['label']) }}"
                   class="input" {{ ($field['required']??false) ? 'required' : '' }} />

          @elseif($field['type'] === 'textarea')
            <textarea name="data_lapangan[{{ $field['label'] }}]" class="input" {{ ($field['required']??false) ? 'required' : '' }}>{{ old('data_lapangan.'.$field['label']) }}</textarea>

          @elseif($field['type'] === 'select')
            <select name="data_lapangan[{{ $field['label'] }}]" class="input" {{ ($field['required']??false) ? 'required' : '' }}>
              <option value="">— Pilih —</option>
              @foreach($field['options'] ?? [] as $opt)
              <option value="{{ $opt }}" {{ old('data_lapangan.'.$field['label']) == $opt ? 'selected' : '' }}>{{ $opt }}</option>
              @endforeach
            </select>

          @elseif($field['type'] === 'radio')
            <div style="display:flex;flex-direction:column;gap:8px;margin-top:4px">
              @foreach($field['options'] ?? [] as $opt)
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.87rem;color:var(--text-1)">
                <input type="radio" name="data_lapangan[{{ $field['label'] }}]" value="{{ $opt }}"
                       {{ old('data_lapangan.'.$field['label']) == $opt ? 'checked' : '' }}
                       {{ ($field['required']??false) ? 'required' : '' }}
                       style="accent-color:var(--blue)" />
                {{ $opt }}
              </label>
              @endforeach
            </div>

          @elseif($field['type'] === 'checkbox')
            <div style="display:flex;flex-direction:column;gap:8px;margin-top:4px">
              @foreach($field['options'] ?? [] as $opt)
              <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:.87rem;color:var(--text-1)">
                <input type="checkbox" name="data_lapangan[{{ $field['label'] }}][]" value="{{ $opt }}"
                       {{ in_array($opt, (array)old('data_lapangan.'.$field['label'], [])) ? 'checked' : '' }}
                       style="accent-color:var(--blue)" />
                {{ $opt }}
              </label>
              @endforeach
            </div>

          @elseif($field['type'] === 'file')
            <input type="file" name="lampiran[]" class="input" multiple />
          @endif

          @error('data_lapangan.'.$field['label'])
          <div class="form-error">{{ $message }}</div>
          @enderror
        </div>
        @endforeach
      </div>

      {{-- Submit --}}
      <button type="submit" class="btn btn-primary btn-lg w-full" id="submit-btn">
        <span id="submit-text">Kirim Data Survey</span>
        <span id="submit-spinner" class="spinner" style="display:none;width:16px;height:16px;border-color:rgba(255,255,255,.3);border-top-color:#fff"></span>
      </button>

    </form>
    @endif

    <p style="text-align:center;font-size:.75rem;color:var(--text-3);margin-top:20px">
      Sistem Arsip Digital — Pemerintah Kota
    </p>

  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let leafletMap = null, marker = null;

function detectLocation() {
  const btn = document.getElementById('detect-btn');
  btn.textContent = '⏳ Mendeteksi...';
  btn.disabled = true;

  navigator.geolocation?.getCurrentPosition(
    pos => {
      document.getElementById('lat-input').value = pos.coords.latitude.toFixed(7);
      document.getElementById('lng-input').value = pos.coords.longitude.toFixed(7);
      btn.textContent = '✓ Lokasi Terdeteksi';
      btn.className = 'btn btn-success btn-sm';
      updateMap();
    },
    () => { btn.textContent = '📍 Deteksi Otomatis'; btn.disabled = false; alert('Gagal. Aktifkan izin lokasi.'); }
  );
}

function updateMap() {
  const lat = parseFloat(document.getElementById('lat-input').value);
  const lng = parseFloat(document.getElementById('lng-input').value);
  if (isNaN(lat) || isNaN(lng)) return;

  const mapEl = document.getElementById('mini-map');
  mapEl.style.display = 'block';

  if (!leafletMap) {
    leafletMap = L.map(mapEl).setView([lat, lng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(leafletMap);
    marker = L.marker([lat, lng]).addTo(leafletMap);
  } else {
    leafletMap.setView([lat, lng], 14);
    marker.setLatLng([lat, lng]);
  }
}

document.getElementById('survey-form')?.addEventListener('submit', () => {
  document.getElementById('submit-text').style.display = 'none';
  document.getElementById('submit-spinner').style.display = 'inline-block';
  document.getElementById('submit-btn').disabled = true;
});
</script>
</body>
</html>