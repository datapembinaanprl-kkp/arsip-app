@php $isEdit = isset($asset) && $asset !== null; @endphp

@if($errors->any())
    <div class="bmn-alert bmn-alert-error" style="margin-bottom:1.25rem">
        <strong>Mohon perbaiki kesalahan berikut:</strong>
        <ul style="margin:.5rem 0 0 1.25rem;padding:0">
            @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="bmn-section-title">Identitas Barang</div>
<div class="bmn-form-grid">
    <div class="bmn-field">
        <label class="bmn-label">Kode Barang <span class="bmn-req">*</span></label>
        <input type="text" name="kode_barang"
               class="bmn-input @error('kode_barang') bmn-input-error @enderror"
               value="{{ old('kode_barang', $isEdit ? $asset->kode_barang : '') }}"
               placeholder="cth. BMN-2024-001" required>
        @error('kode_barang')<span class="bmn-field-error">{{ $message }}</span>@enderror
    </div>

    <div class="bmn-field">
        <label class="bmn-label">Kategori <span class="bmn-req">*</span></label>
        <select name="kategori" class="bmn-input @error('kategori') bmn-input-error @enderror" required>
            <option value="">— Pilih Kategori —</option>
            @foreach(\App\Models\Asset::KATEGORI as $kat)
                <option value="{{ $kat }}"
                    {{ old('kategori', $isEdit ? $asset->kategori : '') === $kat ? 'selected' : '' }}>
                    {{ $kat }}
                </option>
            @endforeach
        </select>
        @error('kategori')<span class="bmn-field-error">{{ $message }}</span>@enderror
    </div>

    <div class="bmn-field bmn-field-full">
        <label class="bmn-label">Nama Barang <span class="bmn-req">*</span></label>
        <input type="text" name="nama_barang"
               class="bmn-input @error('nama_barang') bmn-input-error @enderror"
               value="{{ old('nama_barang', $isEdit ? $asset->nama_barang : '') }}"
               placeholder="Nama lengkap barang" required>
        @error('nama_barang')<span class="bmn-field-error">{{ $message }}</span>@enderror
    </div>

    <div class="bmn-field">
        <label class="bmn-label">Merk / Tipe</label>
        <input type="text" name="merk_tipe" class="bmn-input"
               value="{{ old('merk_tipe', $isEdit ? $asset->merk_tipe : '') }}"
               placeholder="cth. Toyota Avanza">
    </div>

    <div class="bmn-field">
        <label class="bmn-label">No. Seri / NUP</label>
        <input type="text" name="no_seri" class="bmn-input"
               value="{{ old('no_seri', $isEdit ? $asset->no_seri : '') }}"
               placeholder="Nomor seri pabrik">
    </div>
</div>

<div class="bmn-section-title" style="margin-top:1.5rem">Nilai & Kondisi</div>
<div class="bmn-form-grid">
    <div class="bmn-field">
        <label class="bmn-label">Tahun Perolehan</label>
        <input type="number" name="tahun_perolehan" class="bmn-input"
               value="{{ old('tahun_perolehan', $isEdit ? $asset->tahun_perolehan : '') }}"
               min="1900" max="{{ date('Y') }}" placeholder="{{ date('Y') }}">
    </div>

    <div class="bmn-field">
        <label class="bmn-label">Nilai Perolehan (Rp)</label>
        <input type="number" name="nilai_perolehan" class="bmn-input"
               value="{{ old('nilai_perolehan', $isEdit ? $asset->nilai_perolehan : '') }}"
               min="0" step="1000" placeholder="0">
    </div>

    <div class="bmn-field">
        <label class="bmn-label">Kondisi <span class="bmn-req">*</span></label>
        <select name="kondisi" class="bmn-input @error('kondisi') bmn-input-error @enderror" required>
            @foreach(array_keys(\App\Models\Asset::KONDISI) as $kond)
                <option value="{{ $kond }}"
                    {{ old('kondisi', $isEdit ? $asset->kondisi : 'Baik') === $kond ? 'selected' : '' }}>
                    {{ $kond }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="bmn-section-title" style="margin-top:1.5rem">Lokasi & Unit</div>
<div class="bmn-form-grid">
    <div class="bmn-field bmn-field-full">
        <label class="bmn-label">Lokasi Fisik Aset <span class="bmn-req">*</span></label>
        <input type="text" name="lokasi"
               class="bmn-input @error('lokasi') bmn-input-error @enderror"
               value="{{ old('lokasi', $isEdit ? $asset->lokasi : '') }}"
               placeholder="cth. Gedung A, Lantai 2, Ruang 201" required>
        @error('lokasi')<span class="bmn-field-error">{{ $message }}</span>@enderror
    </div>

    <div class="bmn-field bmn-field-full">
        <label class="bmn-label">Unit Pengguna / Satuan Kerja <span class="bmn-req">*</span></label>
        <input type="text" name="unit_pengguna"
               class="bmn-input @error('unit_pengguna') bmn-input-error @enderror"
               value="{{ old('unit_pengguna', $isEdit ? $asset->unit_pengguna : '') }}"
               placeholder="cth. Bagian Umum dan Kepegawaian" required>
        @error('unit_pengguna')<span class="bmn-field-error">{{ $message }}</span>@enderror
    </div>

    <div class="bmn-field bmn-field-full">
        <label class="bmn-label">Keterangan Tambahan</label>
        <textarea name="keterangan" class="bmn-input bmn-textarea" rows="3"
                  placeholder="Catatan tambahan tentang aset ini...">{{ old('keterangan', $isEdit ? $asset->keterangan : '') }}</textarea>
    </div>
</div>

<div class="bmn-section-title" style="margin-top:1.5rem">Lampiran</div>
<div class="bmn-form-grid">
    <div class="bmn-field">
        <label class="bmn-label">Foto Aset</label>
        @if($isEdit && $asset->foto)
            <img id="foto-preview" src="{{ $asset->foto_url }}" class="bmn-preview-img">
        @else
            <img id="foto-preview" src="" class="bmn-preview-img" style="display:none">
        @endif
        <div class="bmn-file-wrap">
            <input type="file" id="foto" name="foto" class="bmn-file-input"
                   accept="image/jpg,image/jpeg,image/png,image/webp">
            <label for="foto" class="bmn-file-label">↑ Pilih Foto</label>
            <span id="foto-name" class="bmn-file-name">
                {{ $isEdit && $asset->foto ? basename($asset->foto) : 'Belum dipilih' }}
            </span>
        </div>
        <span class="bmn-hint">JPG/PNG/WebP, maks. 2MB</span>
        @error('foto')<span class="bmn-field-error">{{ $message }}</span>@enderror
    </div>

    <div class="bmn-field">
        <label class="bmn-label">Dokumen Pendukung</label>
        @if($isEdit && $asset->dokumen_url)
            <div style="margin-bottom:.5rem">
                <a href="{{ $asset->dokumen_url }}" target="_blank" class="bmn-link">
                    ↓ Lihat dokumen saat ini
                </a>
            </div>
        @endif
        <div class="bmn-file-wrap">
            <input type="file" id="dokumen" name="dokumen" class="bmn-file-input"
                   accept=".pdf,.doc,.docx">
            <label for="dokumen" class="bmn-file-label">↑ Pilih Dokumen</label>
            <span id="dokumen-name" class="bmn-file-name">Belum dipilih</span>
        </div>
        <span class="bmn-hint">PDF/DOC/DOCX, maks. 5MB</span>
        @error('dokumen')<span class="bmn-field-error">{{ $message }}</span>@enderror
    </div>
</div>

<div class="bmn-form-footer">
    <a href="{{ route('assets.index') }}" class="bmn-btn-secondary">Batal</a>
    <button type="submit" class="bmn-btn-primary">
        {{ $isEdit ? 'Perbarui Aset' : 'Simpan Aset' }}
    </button>
</div>