@extends('layouts.app')
@section('title', 'Tambah Pengguna')

@section('content')

<div style="margin-bottom:16px">
  <a href="{{ route('admin.users') }}" style="font-size:.83rem;color:var(--text-3);text-decoration:none">← Kembali</a>
</div>

<div class="page-header">
  <div>
    <h1 class="page-title">Tambah Pengguna</h1>
    <p class="page-sub">Buat akun baru dan tentukan role-nya</p>
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

  <form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    <div style="margin-bottom:14px">
      <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
        Nama Lengkap <span style="color:#DC2626">*</span>
      </label>
      <input type="text" name="name" value="{{ old('name') }}" required
             style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('name') ? '#DC2626' : '#E4E3DF' }};border-radius:8px;font-size:.87rem;outline:none;font-family:inherit"
             placeholder="Contoh: Budi Santoso" />
      @error('name')
        <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
      @enderror
    </div>

    <div style="margin-bottom:14px">
      <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
        Email <span style="color:#DC2626">*</span>
      </label>
      <input type="email" name="email" value="{{ old('email') }}" required
             style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('email') ? '#DC2626' : '#E4E3DF' }};border-radius:8px;font-size:.87rem;outline:none;font-family:inherit"
             placeholder="contoh@pemkot.id" />
      @error('email')
        <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
      @enderror
    </div>

    <div style="margin-bottom:14px">
      <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
        Password <span style="color:#DC2626">*</span>
      </label>
      <input type="password" name="password" required minlength="8"
             style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('password') ? '#DC2626' : '#E4E3DF' }};border-radius:8px;font-size:.87rem;outline:none;font-family:inherit"
             placeholder="Minimal 8 karakter" />
      @error('password')
        <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
      @enderror
    </div>

    <div style="margin-bottom:14px">
      <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
        Konfirmasi Password <span style="color:#DC2626">*</span>
      </label>
      <input type="password" name="password_confirmation" required
             style="width:100%;padding:9px 13px;border:1.5px solid #E4E3DF;border-radius:8px;font-size:.87rem;outline:none;font-family:inherit"
             placeholder="Ulangi password" />
    </div>

    <div style="margin-bottom:24px">
      <label style="display:block;font-size:.82rem;font-weight:600;color:#1A1917;margin-bottom:6px">
        Role <span style="color:#DC2626">*</span>
      </label>
      <select name="role" required
              style="width:100%;padding:9px 13px;border:1.5px solid {{ $errors->has('role') ? '#DC2626' : '#E4E3DF' }};border-radius:8px;font-size:.87rem;outline:none;font-family:inherit;background:#fff;cursor:pointer">
        <option value="">— Pilih Role —</option>
        @foreach($roles as $role)
          <option value="{{ $role->name }}" {{ old('role') == $role->name ? 'selected' : '' }}>
            {{ ucfirst($role->name) }} —
            @switch($role->name)
              @case('admin')      Akses penuh @break
              @case('staf')       Upload & lihat dokumen sendiri @break
              @case('supervisor') + Dapat menolak dokumen @break
              @case('direktur')   + Laporan & hapus dokumen @break
              @default            {{ $role->name }}
            @endswitch
          </option>
        @endforeach
      </select>
      @error('role')
        <div style="font-size:.75rem;color:#DC2626;margin-top:4px">{{ $message }}</div>
      @enderror

      <div style="margin-top:10px;padding:12px;background:#F2F1EF;border-radius:8px;font-size:.77rem;color:#5C5B57;line-height:1.8">
        <strong>Keterangan role:</strong><br>
        🔴 <strong>Admin</strong> — Kelola pengguna & semua dokumen<br>
        🟣 <strong>Direktur</strong> — Lihat laporan, hapus dokumen<br>
        🔵 <strong>Supervisor</strong> — Dapat menolak dokumen staf<br>
        🟢 <strong>Staf</strong> — Upload & lihat dokumen sendiri
      </div>
    </div>

    <div style="display:flex;gap:10px">
      <button type="submit"
        style="padding:9px 20px;background:#1C64F2;color:#fff;border:none;border-radius:8px;font-size:.87rem;font-weight:600;cursor:pointer;font-family:inherit">
        Tambah Pengguna
      </button>
      <a href="{{ route('admin.users') }}"
         style="padding:9px 20px;background:#fff;color:#5C5B57;border:1px solid #E4E3DF;border-radius:8px;font-size:.87rem;font-weight:600;text-decoration:none;display:inline-flex;align-items:center">
        Batal
      </a>
    </div>

  </form>
</div>

@endsection