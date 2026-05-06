@extends('layouts.app')
@section('title', 'Edit Pengguna')

@section('content')

<div style="margin-bottom:16px">
  <a href="{{ route('admin.users') }}" style="font-size:.83rem;color:var(--text-3);text-decoration:none">← Kembali</a>
</div>

<div class="page-header">
  <div>
    <h1 class="page-title">Edit Pengguna</h1>
    <p class="page-sub">{{ $user->name }} &mdash; {{ $user->email }}</p>
  </div>
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

  <form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf @method('PUT')

    <div style="margin-bottom:14px">
      <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
        Nama Lengkap <span style="color:#DC2626">*</span>
      </label>
      <input type="text" name="name" value="{{ old('name', $user->name) }}" required
             style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('name') ? '#DC2626' : '#E4E3DF' }};border-radius:8px;font-size:.87rem;outline:none;font-family:inherit" />
      @error('name')
        <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
      @enderror
    </div>

    <div style="margin-bottom:14px">
      <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
        Email <span style="color:#DC2626">*</span>
      </label>
      <input type="email" name="email" value="{{ old('email', $user->email) }}" required
             style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('email') ? '#DC2626' : '#E4E3DF' }};border-radius:8px;font-size:.87rem;outline:none;font-family:inherit" />
      @error('email')
        <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
      @enderror
    </div>

    <div style="margin-bottom:14px">
      <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
        Password Baru
        <span style="font-weight:400;color:#9C9A92">(kosongkan jika tidak diubah)</span>
      </label>
      <input type="password" name="password" minlength="8"
             style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('password') ? '#DC2626' : '#E4E3DF' }};border-radius:8px;font-size:.87rem;outline:none;font-family:inherit"
             placeholder="Min. 8 karakter" />
      @error('password')
        <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
      @enderror
    </div>

    <div style="margin-bottom:14px">
      <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
        Konfirmasi Password Baru
      </label>
      <input type="password" name="password_confirmation"
             style="width:100%;padding:9px 13px;border:1.5px solid #E4E3DF;border-radius:8px;font-size:.87rem;outline:none;font-family:inherit"
             placeholder="Ulangi password baru" />
    </div>

    <div style="margin-bottom:24px">
      <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
        Role <span style="color:#DC2626">*</span>
      </label>
      <select name="role" required
              style="width:100%;padding:9px 13px;border:1.5px solid #E4E3DF;border-radius:8px;font-size:.87rem;outline:none;font-family:inherit;background:#fff;cursor:pointer">
        <option value="">— Pilih Role —</option>
        @foreach($roles as $role)
          <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
            {{ ucfirst($role->name) }}
          </option>
        @endforeach
      </select>
      @error('role')
        <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
      @enderror
    </div>

    <div style="display:flex;gap:10px">
      <button type="submit"
        style="padding:9px 20px;background:#1C64F2;color:#fff;border:none;border-radius:8px;font-size:.87rem;font-weight:600;cursor:pointer;font-family:inherit">
        Simpan Perubahan
      </button>
      <a href="{{ route('admin.users') }}"
         style="padding:9px 20px;background:#fff;color:#5C5B57;border:1px solid #E4E3DF;border-radius:8px;font-size:.87rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center">
        Batal
      </a>
    </div>

  </form>
</div>

@endsection