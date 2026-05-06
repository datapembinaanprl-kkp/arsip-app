@php $isEdit = isset($survey) && $survey !== null; @endphp

@if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:1rem">
        <strong>Perbaiki kesalahan:</strong>
        <ul style="margin:.4rem 0 0 1.25rem;padding:0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="sv-form-layout">

    {{-- ── Kolom Kiri: Info Survey ─────────────────────────────── --}}
    <div style="display:flex;flex-direction:column;gap:1rem">

        <div class="sv-card">
            <div class="sv-card-header">Informasi Survey</div>
            <div class="sv-card-body">

                <div class="sv-field">
                    <label class="sv-label">Judul Survey <span class="sv-req">*</span></label>
                    <input type="text" name="judul"
                           class="sv-input @error('judul') sv-input-error @enderror"
                           value="{{ old('judul', $isEdit ? $survey->judul : '') }}"
                           placeholder="cth. Survey Kepuasan Layanan 2024"
                           required>
                    @error('judul')<span class="sv-field-error">{{ $message }}</span>@enderror
                </div>

                <div class="sv-field">
                    <label class="sv-label">Deskripsi</label>
                    <textarea name="deskripsi" class="sv-input sv-textarea" rows="3"
                              placeholder="Jelaskan tujuan survey ini...">{{ old('deskripsi', $isEdit ? $survey->deskripsi : '') }}</textarea>
                </div>

                <div class="sv-field">
                    <label class="sv-label">Status <span class="sv-req">*</span></label>
                    <select name="status" class="sv-input">
                        @foreach(['draft' => 'Draft', 'aktif' => 'Aktif', 'tutup' => 'Tutup'] as $val => $lab)
                            <option value="{{ $val }}"
                                {{ old('status', $isEdit ? $survey->status : 'draft') === $val ? 'selected' : '' }}>
                                {{ $lab }}
                            </option>
                        @endforeach
                    </select>
                    <span class="sv-hint">Draft = tidak bisa diisi responden. Aktif = bisa diisi.</span>
                </div>

                <div class="sv-field">
                    <label class="sv-label">Batas Waktu</label>
                    <input type="datetime-local" name="batas_waktu" class="sv-input"
                           value="{{ old('batas_waktu', $isEdit && $survey->batas_waktu ? $survey->batas_waktu->format('Y-m-d\TH:i') : '') }}">
                    <span class="sv-hint">Kosongkan jika tidak ada batas waktu.</span>
                </div>

            </div>
        </div>

        {{-- Link publik (hanya saat edit) --}}
        @if($isEdit)
        <div class="sv-card">
            <div class="sv-card-header">Link Publik</div>
            <div class="sv-card-body">
                <div style="display:flex;gap:.5rem;align-items:center">
                    <input type="text" value="{{ $survey->publik_url }}"
                           class="sv-input sv-mono" id="publik-url" readonly
                           style="font-size:.78rem">
                    <button type="button" class="sv-btn-secondary" onclick="copyUrl()" id="copy-btn"
                            style="white-space:nowrap;flex-shrink:0">
                        📋 Salin
                    </button>
                </div>
                <a href="{{ $survey->publik_url }}" target="_blank"
                   class="sv-link" style="font-size:.8rem;margin-top:.5rem;display:inline-block">
                    🔗 Buka halaman publik →
                </a>
            </div>
        </div>
        @endif

    </div>

    {{-- ── Kolom Kanan: Form Builder ────────────────────────────── --}}
    <div class="sv-card">
        <div class="sv-card-header" style="display:flex;justify-content:space-between;align-items:center">
            <span>Pertanyaan Survey</span>
            <button type="button" class="sv-btn-primary sv-btn-sm" onclick="addQuestion()">
                + Tambah Pertanyaan
            </button>
        </div>
        <div class="sv-card-body" style="padding:0">

            {{-- Container pertanyaan --}}
            <div id="questions-container">
                @if($isEdit && $survey->questions->count())
                    @foreach($survey->questions as $i => $q)
                        {{-- Render pertanyaan existing --}}
                        <div class="sv-question-block" data-index="{{ $i }}">
                            @include('survey.question-block', ['i' => $i, 'q' => $q])
                        </div>
                    @endforeach
                @else
                    {{-- Satu pertanyaan kosong default --}}
                    <div class="sv-question-block" data-index="0">
                        @include('survey.question-block', ['i' => 0, 'q' => null])
                    </div>
                @endif
            </div>

            <div style="padding:1rem;border-top:1px solid var(--border);text-align:center">
                <button type="button" class="sv-btn-secondary" onclick="addQuestion()">
                    + Tambah Pertanyaan
                </button>
            </div>

        </div>
    </div>

</div>

{{-- Submit --}}
<div style="display:flex;justify-content:flex-end;gap:.75rem;margin-top:1.25rem">
    <a href="{{ route('survey.index') }}" class="sv-btn-secondary">Batal</a>
    <button type="submit" class="sv-btn-primary">
        {{ $isEdit ? 'Perbarui Survey' : 'Simpan Survey' }}
    </button>
</div>