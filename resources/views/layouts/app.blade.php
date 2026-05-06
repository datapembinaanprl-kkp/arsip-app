<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Sistem Arsip') — Sistem Arsip Database</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
  @stack('styles')
</head>
<body>

{{-- Overlay backdrop untuk mobile drawer --}}
<div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

<div class="app-shell">

  @include('layouts.sidebar')

  {{-- ─── Main area ─── --}}
  <div class="main-wrap">

    <header class="topbar">
      {{-- Hamburger: muncul di mobile & tablet --}}
      <button class="topbar-hamburger" id="sidebar-toggle"
              onclick="toggleSidebar()" aria-label="Toggle menu">
        <svg width="20" height="20" fill="none" stroke="currentColor"
             stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      {{-- Judul halaman di topbar (mobile) --}}
      <span class="topbar-page-title">@yield('title', 'Sistem Arsip')</span>

      <div class="topbar-spacer"></div>

      <div class="topbar-actions">
        <span class="role-badge role-{{ auth()->user()->getRoleNames()->first() }}">
          {{ auth()->user()->role_label }}
        </span>
        <span class="topbar-date text-sm text-muted">
          {{ now()->isoFormat('D MMM YYYY') }}
        </span>
      </div>
    </header>

    <main class="page-body">
      {{-- Flash messages --}}
      @if(session('success'))
        <div class="alert alert-success">✓ {{ session('success') }}</div>
      @endif
      @if(session('error'))
        <div class="alert alert-danger">✕ {{ session('error') }}</div>
      @endif

      @yield('content')
    </main>

  </div>
</div>

<script>
window.CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

// ── Sidebar toggle ─────────────────────────────────────────────
const sidebar  = document.querySelector('.sidebar');
const overlay  = document.getElementById('sidebar-overlay');

function toggleSidebar() {
  const isOpen = sidebar.classList.contains('open');
  isOpen ? closeSidebar() : openSidebar();
}

function openSidebar() {
  sidebar.classList.add('open');
  overlay.classList.add('visible');
  document.body.style.overflow = 'hidden'; // Prevent scroll behind drawer
}

function closeSidebar() {
  sidebar.classList.remove('open');
  overlay.classList.remove('visible');
  document.body.style.overflow = '';
}

// Tutup sidebar saat resize ke desktop
window.addEventListener('resize', () => {
  if (window.innerWidth >= 1024) closeSidebar();
});

// Tutup dengan keyboard Escape
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') closeSidebar();
});
</script>

@stack('scripts')
</body>
</html>