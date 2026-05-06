@extends('layouts.app')
@section('title', 'Kelola Pengguna')

@section('content')

<div class="page-header">
  <div>
    <h1 class="page-title">Kelola Pengguna</h1>
    <p class="page-sub">{{ $users->total() }} pengguna terdaftar</p>
  </div>
  <a href="{{ route('admin.users.create') }}" class="btn btn-primary">+ Tambah Pengguna</a>
</div>

@if(session('success'))
  <div class="alert alert-success" style="margin-bottom:16px">✓ {{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="alert alert-danger" style="margin-bottom:16px">✕ {{ session('error') }}</div>
@endif

{{-- Filter --}}
<form method="GET" action="{{ route('admin.users') }}" class="card" style="margin-bottom:16px">
  <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
    <input type="text" name="q" value="{{ request('q') }}" class="input"
           placeholder="Cari nama atau email..." style="flex:1;min-width:180px" />
    <select name="role" class="input" style="width:auto">
      <option value="">Semua Role</option>
      @foreach($roles as $role)
        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
          {{ ucfirst($role->name) }}
        </option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-primary">Cari</button>
    @if(request()->hasAny(['q','role']))
      <a href="{{ route('admin.users') }}" class="btn btn-secondary">Reset</a>
    @endif
  </div>
</form>

{{-- Tabel --}}
<div class="card" style="padding:0;overflow:hidden">
  @if($users->isEmpty())
    <div style="text-align:center;padding:48px;color:var(--text-3)">
      <div style="font-size:2.5rem;margin-bottom:10px">👤</div>
      <p style="font-weight:600;color:var(--text-1)">Tidak ada pengguna ditemukan</p>
    </div>
  @else
  <div style="overflow-x:auto">
    <table style="width:100%;border-collapse:collapse">
      <thead style="background:var(--surface-2)">
        <tr>
          <th style="padding:10px 16px;text-align:left;font-size:.74rem;color:var(--text-3);font-weight:600">Pengguna</th>
          <th style="padding:10px 16px;text-align:left;font-size:.74rem;color:var(--text-3);font-weight:600">Role</th>
          <th style="padding:10px 16px;text-align:left;font-size:.74rem;color:var(--text-3);font-weight:600">Status</th>
          <th style="padding:10px 16px;text-align:left;font-size:.74rem;color:var(--text-3);font-weight:600">Terdaftar</th>
          <th style="padding:10px 16px;text-align:right;font-size:.74rem;color:var(--text-3);font-weight:600">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $user)
        <tr style="border-bottom:1px solid var(--border)">

          <td style="padding:12px 16px">
            <div style="display:flex;align-items:center;gap:10px">
              <div style="width:34px;height:34px;border-radius:50%;background:#EBF2FF;color:#1C64F2;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;flex-shrink:0">
                {{ strtoupper(substr($user->name, 0, 2)) }}
              </div>
              <div>
                <div style="font-size:.87rem;font-weight:500;color:var(--text-1)">{{ $user->name }}</div>
                <div style="font-size:.74rem;color:var(--text-3)">{{ $user->email }}</div>
              </div>
            </div>
          </td>

          <td style="padding:12px 16px">
            @php $roleName = $user->getRoleNames()->first() ?? '-' @endphp
            @php
              $roleColor = match($roleName) {
                'admin'      => 'background:#FEE2E2;color:#991b1b',
                'direktur'   => 'background:#F5F3FF;color:#5b21b6',
                'supervisor' => 'background:#EBF2FF;color:#1e40af',
                'staf'       => 'background:#E6F7F2;color:#065f46',
                default      => 'background:#F2F1EF;color:#5C5B57',
              };
            @endphp
            <span style="padding:2px 10px;border-radius:99px;font-size:.74rem;font-weight:600;{{ $roleColor }}">
              {{ ucfirst($roleName) }}
            </span>
          </td>

          <td style="padding:12px 16px">
            @if($user->is_active ?? true)
              <span style="padding:2px 10px;border-radius:99px;font-size:.74rem;font-weight:600;background:#E6F7F2;color:#0D9E6A">Aktif</span>
            @else
              <span style="padding:2px 10px;border-radius:99px;font-size:.74rem;font-weight:600;background:#F2F1EF;color:#9C9A92">Nonaktif</span>
            @endif
          </td>

          <td style="padding:12px 16px;font-size:.8rem;color:var(--text-3)">
            {{ $user->created_at->format('d/m/Y') }}
          </td>

          <td style="padding:12px 16px;text-align:right">
            <div style="display:flex;justify-content:flex-end;gap:6px">

              <a href="{{ route('admin.users.edit', $user) }}"
                 style="padding:5px 12px;border-radius:8px;font-size:.78rem;font-weight:600;background:#fff;border:1px solid #E4E3DF;color:#5C5B57;text-decoration:none">
                Edit
              </a>

              @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="display:inline">
                  @csrf @method('PATCH')
                  <button type="submit"
                    onclick="return confirm('{{ ($user->is_active ?? true) ? 'Nonaktifkan' : 'Aktifkan' }} pengguna ini?')"
                    style="padding:5px 12px;border-radius:8px;font-size:.78rem;font-weight:600;border:1px solid #E4E3DF;cursor:pointer;background:#fff;color:{{ ($user->is_active ?? true) ? '#D97706' : '#0D9E6A' }}">
                    {{ ($user->is_active ?? true) ? 'Nonaktifkan' : 'Aktifkan' }}
                  </button>
                </form>

                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline"
                      onsubmit="return confirm('Hapus pengguna {{ addslashes($user->name) }}?')">
                  @csrf @method('DELETE')
                  <button type="submit"
                    style="padding:5px 12px;border-radius:8px;font-size:.78rem;font-weight:600;border:1px solid #fecaca;cursor:pointer;background:#FEE2E2;color:#DC2626">
                    Hapus
                  </button>
                </form>
              @else
                <span style="font-size:.74rem;color:var(--text-3);padding:5px 8px">(Anda)</span>
              @endif

            </div>
          </td>

        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  @if($users->hasPages())
  <div style="padding:14px 20px;border-top:1px solid #E4E3DF;display:flex;align-items:center;justify-content:space-between">
    <span style="font-size:.8rem;color:var(--text-3)">
      {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }}
    </span>
    {{ $users->appends(request()->query())->links() }}
  </div>
  @endif

  @endif
</div>

@endsection