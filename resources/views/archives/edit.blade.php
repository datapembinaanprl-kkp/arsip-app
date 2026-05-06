@extends('layouts.app')
@section('title', 'Edit Dokumen')

@section('content')

<div style="margin-bottom:16px">
  <a href="{{ route('admin.users') }}" style="font-size:.83rem;color:var(--text-3);text-decoration:none">← Kembali</a>
</div>

<div class="page-header">
    <h1 class="page-title">Edit dokumen</h1>
    <p class="page-sub">{{ $archive->name }} &mdash; </p>
</div>

<div class="card" style="max-width:520px">

  @if($errors->any())
    <div style="padding:12px 16px;border-radius:8px;background:#FEE2E2;border:1px solid #fecaca;margin-bottom:20px">
      <strong style="font-size:.84rem;color:#991b1b">Perbaiki kesalahan berikut:</strong>
      <ul style="margin-top:6px;padding-left:16px">
        @foreach($errors->all() as $e)
          <li style="font-size:.82rem;color:#991b1b">{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @can('dokumen.upload')
  <a href="{{ route('archives.create') }}" class="btn btn-primary">
    + Unggah
  </a>
  @endcan


    

  <form method="POST" action="{{ route('archives.update', $archive) }}" enctype="multipart/form-data">
    @csrf 
    @method('PUT')

    <div style="margin-bottom":14px>
        <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
      Judul Dokumen <span style="color:#DC2626">*</span>
        </label>
            <input type="text" name="title"
           value="{{ old('title', $archive->title) }}" required
           style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('title') ? '#DC2626' : '#E4E3DF' }};border-radius:8px;font-size:.87rem;outline:none;font-family:inherit" />

    @error('title')
    <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
    @enderror


    </div>
      <div style="margin-bottom:14px">
        <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
      Kategori
        </label>
        <input type="text" name="kategori"
           value="{{ old('kategori', $archive->kategori) }}"
           style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('kategori') ? '#DC2626' : '#E4E3DF' }};border-radius:8px;font-size:.87rem;outline:none;font-family:inherit" />

    @error('kategori')
    <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
    @enderror
    </div>

    <div style="margin-bottom:14px">
        <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
    Deskripsi
        </label>
    <textarea name="description" rows="4"
      style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('description') ? '#DC2626' : '#E4E3DF' }};border-radius:8px;font-size:.87rem;outline:none;font-family:inherit">{{ old('description', $archive->description) }}</textarea>

    @error('description')
      <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
    @enderror
    </div>
  <div style="margin-bottom:14px">
    <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
      File Dokumen
      <span style="font-weight:400;color:#9C9A92">(kosongkan jika tidak diubah)</span>
    </label>

    <input type="file" name="file"
           style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('file') ? '#DC2626' : '#E4E3DF' }};border-radius:8px;font-size:.87rem;outline:none;font-family:inherit" />

    @error('file')
      <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
    @enderror

    @if($archive->file)
      <div style="margin-top:6px;font-size:.75rem;color:#5C5B57">
        File saat ini:
        <a href="{{ asset('storage/' . $archive->file) }}" target="_blank"
           style="color:#1C64F2;text-decoration:none;font-weight:500">
          Lihat File
        </a>
      </div>
    @endif
  </div>
  <div style="display:flex;gap:10px;margin-top:20px">
    <button type="submit"
      style="padding:9px 20px;background:#1C64F2;color:#fff;border:none;border-radius:8px;font-size:.87rem;font-weight:600;cursor:pointer;font-family:inherit">
      Update Dokumen
    </button>

    <a href="{{ route('archives.index') }}"
       style="padding:9px 20px;background:#fff;color:#5C5B57;border:1px solid #E4E3DF;border-radius:8px;font-size:.87rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center">
      Batal
    </a>
  </div>

  </form>
</div>
@endsection
