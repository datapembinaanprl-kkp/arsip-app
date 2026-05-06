{{-- Shared form partial used by both create and edit --}}

@php $isEdit = isset($member) && $member !== null; @endphp

{{-- ── Validation Summary ───────────────────────────────────── --}}
@if($errors->any())
    <div class="os-alert os-alert-error">
        <strong>Mohon perbaiki kesalahan berikut:</strong>
        <ul class="os-error-list">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="os-form-grid">

    {{-- Name --}}
    <div class="os-field os-field-full">
        <label class="os-label" for="name">Nama Lengkap <span class="os-required">*</span></label>
        <input
            type="text"
            id="name"
            name="name"
            class="os-input @error('name') os-input-error @enderror"
            value="{{ old('name', $isEdit ? $member->name : '') }}"
            placeholder="Contoh Budi Santoso"
            required
        >
        @error('name')<span class="os-field-error">{{ $message }}</span>@enderror
    </div>

    {{-- Jabatan --}}
    <div class="os-field">
        <label class="os-label" for="position"> Jabatan <span class="os-required">*</span></label>
        <input
            type="text"
            id="position"
            name="position"
            class="os-input @error('position') os-input-error @enderror"
            value="{{ old('position', $isEdit ? $member->position : '') }}"
            placeholder="contoh Staff"
            required
        >
        @error('position')<span class="os-field-error">{{ $message }}</span>@enderror
    </div>

    {{-- Sort Order --}}
    <div class="os-field">
        <label class="os-label" for="order">Urutan Tampil</label>
        <input
            type="number"
            id="order"
            name="order"
            class="os-input @error('order') os-input-error @enderror"
            value="{{ old('order', $isEdit ? $member->order : 0) }}"
            min="0"
        >
        <span class="os-hint">Angka lebih kecil tampil lebih awal dalam level yang sama.</span>
        @error('order')<span class="os-field-error">{{ $message }}</span>@enderror
    </div>

    {{-- Parent / Reports To --}}
    <div class="os-field os-field-full">
        <label class="os-label" for="parent_id">Melapor Kepada (Atasan)</label>
        <select id="parent_id" name="parent_id" class="os-input @error('parent_id') os-input-error @enderror">
            <option value="">— Tidak Ada (Level Teratas / Root) —</option>
            @foreach($parents as $parent)
                <option value="{{ $parent->id }}"
                    {{ old('parent_id', $isEdit ? $member->parent_id : '') == $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }} — {{ $parent->position }}
                </option>
            @endforeach
        </select>
        @error('parent_id')<span class="os-field-error">{{ $message }}</span>@enderror
    </div>

    {{-- Photo Upload --}}
    <div class="os-field os-field-full">
            <label class="os-label" for="photo">
                 Foto Profil {!! !$isEdit ? '
            <span class="os-required">*</span>' : '' !!}
        </label>
    </div>

        {{-- Preview existing photo on edit --}}
        @if($isEdit && $member->photo)
            <div class="os-photo-preview-wrap">
                <img id="photo-preview" src="{{ $member->photo_url }}" alt="Current photo" class="os-photo-preview">
                <span class="os-hint">Upload foto baru untuk mengganti yang lama.</span>  {{-- edit mode --}}
            </div>
        @else
            <img id="photo-preview" src="" alt="" class="os-photo-preview" style="display:none">
        @endif

        <div class="os-file-input-wrap">
            <input
                type="file"
                id="photo"
                name="photo"
                class="os-file-input @error('photo') os-input-error @enderror"
                accept="image/jpg,image/jpeg,image/png,image/webp"
                {{ !$isEdit ? 'required' : '' }}
            >
            <label for="photo" class="os-file-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"/><path d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"/></svg>
                Choose Photo
            </label>
            <span id="file-name" class="os-file-name">Belum ada file dipilih</span>
        </div>
        <span class="os-hint">JPG, PNG, atau WebP — maks. 2MB</span>
        @error('photo')<span class="os-field-error">{{ $message }}</span>@enderror
    </div>

</div>

{{-- ── Submit ───────────────────────────────────────────────── --}}
<div class="os-form-footer">
    <a href="{{ route('organizational-structure.index') }}" class="os-btn-secondary">Batal</a>
    <button type="submit" class="os-btn-primary">
        {{ $isEdit ? 'Perbarui Anggota' : 'Tambah Anggota' }}
</button>
</div>