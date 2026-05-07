<aside class="sidebar" id="main-sidebar">
  <div class="sidebar-logo">
    <div class="logo-mark">🗂</div>
    <div class="logo-name">Sistem Arsip</div>
    <div class="logo-sub">Pemerintah</div>
  </div>

  <nav class="sidebar-nav">

    {{-- Dashboard --}}
    <a href="{{ route('dashboard') }}"
       data-tooltip="Dashboard"
       class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <span class="nav-icon">🏠</span>
      <span>Dashboard</span>
    </a>

    {{-- Dokumen --}}
    <div class="nav-label">Dokumen</div>
    <a href="{{ route('archives.index') }}"
       data-tooltip="Semua Arsip"
       class="nav-item {{ request()->routeIs('archives.index') ? 'active' : '' }}">
      <span class="nav-icon">📂</span>
      <span>Semua Arsip</span>
    </a>

    @can('dokumen.upload')
    <a href="{{ route('archives.create') }}"
       data-tooltip="Unggah Dokumen"
       class="nav-item {{ request()->routeIs('archives.create') ? 'active' : '' }}">
      <span class="nav-icon">📤</span>
      <span>Unggah Dokumen</span>
    </a>
    @endcan

    {{-- Dokumen ditolak --}}
    @if(auth()->user()->hasRole('staf'))
      @php $ditolakCount = \App\Models\Archive::ditolak()->where('user_id', auth()->id())->count(); @endphp
      @if($ditolakCount > 0)
      <a href="{{ route('archives.index', ['status' => 'ditolak']) }}"
         data-tooltip="Perlu Revisi"
         class="nav-item" style="color:var(--red)">
        <span class="nav-icon">⚠</span>
        <span>Perlu Revisi</span>
        <span class="nav-badge">{{ $ditolakCount }}</span>
      </a>
      @endif
    @endif

    {{-- Manajemen Dokumen --}}
    <div class="nav-label">Manajemen Dokumen</div>
    <a href="{{ route('documents.index') }}"
       data-tooltip="Daftar Dokumen"
       class="nav-item {{ request()->routeIs('documents.*') ? 'active' : '' }}">
      <span class="nav-icon">📋</span>
      <span>Daftar Dokumen</span>
    </a>

    <a href="{{ route('documents.create') }}"
       data-tooltip="Buat Dokumen"
       class="nav-item {{ request()->routeIs('documents.create') ? 'active' : '' }}">
      <span class="nav-icon">➕</span>
      <span>Buat Dokumen</span>
    </a>

    {{-- Kanban — direktur --}}
    @role('direktur')
    <a href="{{ route('kanban.index') }}"
       data-tooltip="Papan Kanban"
       class="nav-item {{ request()->routeIs('kanban.*') ? 'active' : '' }}">
      <span class="nav-icon">📈</span>
      <span>Papan Kanban</span>
    </a>
    @endrole

    {{-- Survey --}}
    <div class="nav-label">Lainnya</div>
    <a href="{{ route('survey.index') }}"
   class="nav-item {{ request()->routeIs('survey.index', 'survey.show', 'survey.create', 'survey.results') ? 'active' : '' }}">
    <span class="nav-icon">📊</span> Survey
  </a>
    </a>

    {{-- GIS --}}
    @hasanyrole('admin|direktur')
    <a href="{{ route('gis.index') }}"
    data-tooltip="GIS Dashboard"
    class="nav-item {{ request()->routeIs('gis.*') ? 'active' : '' }}">
    <span class="nav-icon">🗺️</span>
    <span>GIS Dashboard</span>
    </a>
    @endhasanyrole

    {{-- Struktur Organisasi --}}
    <a href="{{ route('organizational-structure.index') }}"
       data-tooltip="Struktur Organisasi"
       class="nav-item {{ request()->routeIs('organizational-structure.*') ? 'active' : '' }}">
      <span class="nav-icon">👤</span>
      <span>Struktur Organisasi</span>
    </a>

    {{-- BMN --}}
    <a href="{{ route('assets.index') }}"
       data-tooltip="Aset BMN"
       class="nav-item {{ request()->routeIs('assets.*') ? 'active' : '' }}">
      <span class="nav-icon">🏛</span>
      <span>Aset BMN</span>
    </a>

    {{-- Laporan --}}
    @can('laporan.lihat')
    <div class="nav-label">Laporan</div>
    <a href="#"
       data-tooltip="Progres Dokumen"
       class="nav-item">
      <span class="nav-icon">📊</span>
      <span>Progres Dokumen</span>
    </a>
    @endcan

    {{-- Admin --}}
    @hasrole('admin')
    <div class="nav-label">Administrasi</div>
    <a href="{{ route('admin.users') }}"
       data-tooltip="Kelola Pengguna"
       class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
      <span class="nav-icon">👥</span>
      <span>Kelola Pengguna</span>
    </a>
    @endhasrole

  </nav>

  {{-- User info --}}
  <div class="sidebar-user">
    <div class="user-card">
      <div class="user-avatar">
        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
      </div>
      <div class="user-info">
        <div class="user-name">{{ auth()->user()->name }}</div>
        <div class="user-role">{{ auth()->user()->role_label }}</div>
      </div>
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" title="Keluar" class="btn-logout">
          <svg width="14" height="14" fill="none" stroke="currentColor"
               stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
          </svg>
        </button>
      </form>
    </div>
  </div>
</aside>