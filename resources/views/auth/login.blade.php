@extends('layouts.auth')
@section('title', 'Masuk ke Sistem')

@section('content')
  <h1 class="auth-title">Selamat Datang</h1>
  <p class="auth-sub">Masuk dengan akun yang telah diberikan oleh administrator.</p>

  @if($errors->any())
    <div class="alert alert-danger">
      <span>✕</span> {{ $errors->first() }}
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="form-group">
      <label class="form-label" for="email">Email <span class="req">*</span></label>
      <input id="email" type="email" name="email" value="{{ old('email') }}"
             class="input {{ $errors->has('email') ? 'is-error' : '' }}"
             placeholder="Email Kamu" autofocus required />
      @error('email') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div class="form-group">
      <label class="form-label" for="password">
        Password <span class="req">*</span>
      </label>
      <div style="position:relative">
        <input id="password" type="password" name="password"
               class="input {{ $errors->has('password') ? 'is-error' : '' }}"
               placeholder="••••••••" required style="padding-right:40px"/>
        <button type="button" id="toggle-pw"
          style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-3);padding:4px">
          <svg id="eye-icon" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
          </svg>
        </button>
      </div>
      @error('password') <div class="form-error">{{ $message }}</div> @enderror
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:22px">
      <label style="display:flex;align-items:center;gap:7px;font-size:.83rem;color:var(--text-2);cursor:pointer">
        <input type="checkbox" name="remember" id="remember" style="accent-color:var(--blue)" />
        Ingat saya
      </label>
    </div>

    <button type="submit" class="btn btn-primary btn-lg w-full" id="login-btn">
      <span id="login-text">Masuk</span>
      <span id="login-spinner" class="spinner" style="display:none;width:16px;height:16px;border-color:rgba(255,255,255,.3);border-top-color:#fff"></span>
    </button>

  </form>

  <p style="text-align:center;font-size:.76rem;color:var(--text-3);margin-top:24px">
    Lupa password? Hubungi administrator sistem.
  </p>
@endsection

@push('scripts')
<script>
  document.getElementById('toggle-pw')?.addEventListener('click', function() {
    const pw = document.getElementById('password')
    pw.type = pw.type === 'password' ? 'text' : 'password'
  })
  document.querySelector('form')?.addEventListener('submit', function() {
    document.getElementById('login-text').textContent = 'Memproses...'
    document.getElementById('login-spinner').style.display = 'inline-block'
    document.getElementById('login-btn').disabled = true
  })
</script>
@endpush