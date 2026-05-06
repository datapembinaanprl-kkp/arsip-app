@php $isEdit = isset($document) && $document !== null; @endphp

{{-- ── Validation Errors ────────────────────────────────────── --}}
@if($errors->any())
    <div class="doc-alert doc-alert-error">
        <strong>Mohon perbaiki kesalahan berikut:</strong>
        <ul style="margin:.5rem 0 0 1.25rem;padding:0">
            @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="doc-form-grid">

    {{-- Judul --}}
    <div class="doc-field doc-field-full">
        <label class="doc-label" for="judul">
            Judul Dokumen <span class="doc-req">*</span>
        </label>
        <input type="text" id="judul" name="judul"
               class="doc-input @error('judul') doc-input-error @enderror"
               value="{{ old('judul', $isEdit ? $document->judul : '') }}"
               placeholder="Judul dokumen yang akan dikerjakan"
               required>
        @error('judul')
            <span class="doc-field-error">{{ $message }}</span>
        @enderror
    </div>

    {{-- Nomor Dokumen --}}
    <div class="doc-field">
        <label class="doc-label" for="nomor_dokumen">Nomor Dokumen</label>
        <input type="text" id="nomor_dokumen" name="nomor_dokumen"
               class="doc-input @error('nomor_dokumen') doc-input-error @enderror"
               value="{{ old('nomor_dokumen', $isEdit ? $document->nomor_dokumen : '') }}"
               placeholder="cth. DOK/2024/001">
        <span class="doc-hint">Kosongkan jika belum ada nomor resmi.</span>
        @error('nomor_dokumen')
            <span class="doc-field-error">{{ $message }}</span>
        @enderror
    </div>

    {{-- Deadline --}}
    <div class="doc-field">
        <label class="doc-label" for="deadline">Deadline</label>
        <input type="date" id="deadline" name="deadline"
               class="doc-input @error('deadline') doc-input-error @enderror"
               value="{{ old('deadline', $isEdit ? $document->deadline?->format('Y-m-d') : '') }}">
        @error('deadline')
            <span class="doc-field-error">{{ $message }}</span>
        @enderror
    </div>

    {{-- Assignee --}}
    <div class="doc-field doc-field-full">
        <label class="doc-label" for="assignee_id">
            Staff yang Mengerjakan <span class="doc-req">*</span>
        </label>
        <select id="assignee_id" name="assignee_id"
                class="doc-input @error('assignee_id') doc-input-error @enderror"
                required>
            <option value="">— Pilih Staff —</option>
            @foreach($staffList as $staff)
                <option value="{{ $staff->id }}"
                    {{ old('assignee_id', $isEdit ? $document->assignee_id : '') == $staff->id ? 'selected' : '' }}>
                    {{ $staff->name }}
                </option>
            @endforeach
        </select>
        @error('assignee_id')
            <span class="doc-field-error">{{ $message }}</span>
        @enderror
    </div>

    {{-- Catatan --}}
    <div class="doc-field doc-field-full">
        <label class="doc-label" for="catatan">Catatan / Instruksi</label>
        <textarea id="catatan" name="catatan"
                  class="doc-input doc-textarea @error('catatan') doc-input-error @enderror"
                  rows="4"
                  placeholder="Instruksi, catatan, atau informasi tambahan untuk staff...">{{ old('catatan', $isEdit ? $document->catatan : '') }}</textarea>
        @error('catatan')
            <span class="doc-field-error">{{ $message }}</span>
        @enderror
    </div>

    {{-- Info alasan revisi (tampil hanya saat edit dengan status revisi) --}}
    @if($isEdit && $document->status === 'revisi' && $document->alasan_revisi)
    <div class="doc-field doc-field-full">
        <label class="doc-label">Alasan Revisi dari Direktur</label>
        <div class="doc-revisi-box">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor"
                 viewBox="0 0 16 16" style="flex-shrink:0;margin-top:.1rem">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
            </svg>
            <span>{{ $document->alasan_revisi }}</span>
        </div>
    </div>
    @endif

</div>

{{-- ── Form Footer ──────────────────────────────────────────── --}}
<div class="doc-form-footer">
    <a href="{{ route('documents.index') }}" class="doc-btn-secondary">Batal</a>
    <button type="submit" class="doc-btn-primary">
        {{ $isEdit ? 'Perbarui Dokumen' : 'Buat Dokumen' }}
    </button>
</div>