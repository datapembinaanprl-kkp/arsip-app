@extends('layouts.app')
@section('title', 'Unggah Dokumen')

@section('content')

<div style="margin-bottom:16px">
  <a href="{{ route('archives.index') }}" style="font-size:.83rem;color:var(--text-3);text-decoration:none">← Kembali</a>
</div>

<div class="page-header">
  <h1 class="page-title">Unggah Dokumen</h1>
</div>

<div class="card" style="max-width:600px">
  @if($errors->any())
    <div class="alert alert-danger" style="margin-bottom:16px">
      @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('archives.store') }}" enctype="multipart/form-data">
    @csrf

    <div style="margin-bottom:16px">
      <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px">
        Judul <span style="color:red">*</span>
      </label>
      <input type="text" name="title" value="{{ old('title') }}" required
             style="width:100%;padding:9px 13px;border:1.5px solid #E4E3DF;border-radius:8px;font-size:.87rem;outline:none" />
    </div>

    <div style="margin-bottom:16px">
      <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px">
        File <span style="color:red">*</span>
      </label>
      <input type="file" name="file" required
             style="width:100%;padding:9px 13px;border:1.5px solid #E4E3DF;border-radius:8px;font-size:.87rem" />
      <p style="font-size:.74rem;color:var(--text-3);margin-top:4px">Maks. 2MB</p>
    </div>

    <div style="margin-bottom:20px">
      <label style="display:block;font-size:.82rem;font-weight:600;margin-bottom:6px">Keterangan</label>
      <textarea name="description" rows="3"
                style="width:100%;padding:9px 13px;border:1.5px solid #E4E3DF;border-radius:8px;font-size:.87rem;outline:none;resize:vertical">{{ old('description') }}</textarea>
    </div>

    <div style="display:flex;gap:10px">
      <button type="submit" class="btn btn-primary">Simpan</button>
      <a href="{{ route('archives.index') }}" class="btn btn-secondary">Batal</a>
    </div>
  </form>
</div>

@endsection