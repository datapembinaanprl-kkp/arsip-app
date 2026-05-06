<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>@yield('title', 'Masuk') — Sistem Arsip</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
  <style>
    body { background: var(--bg); }
    .auth-shell {
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1fr 440px 1fr;
      align-items: center;
      padding: 40px 20px;
    }
    .auth-card {
      grid-column: 2;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius-xl);
      padding: 40px;
      box-shadow: var(--shadow-lg);
    }
    .auth-logo {
      display: flex; align-items: center; gap: 12px;
      margin-bottom: 32px;
    }
    .auth-logo .mark {
      width: 44px; height: 44px;
      background: var(--blue); border-radius: var(--radius-sm);
      display: flex; align-items: center; justify-content: center;
      font-size: 22px;
    }
    .auth-logo .name  { font-size: 1.1rem; font-weight: 700; color: var(--text-1); }
    .auth-logo .sub   { font-size: .78rem; color: var(--text-3); margin-top: 1px; }
    .auth-title { font-size: 1.35rem; font-weight: 700; color: var(--text-1); margin-bottom: 4px; }
    .auth-sub   { font-size: .87rem; color: var(--text-3); margin-bottom: 28px; }
    @media (max-width: 600px) {
      .auth-shell { grid-template-columns: 1fr; }
      .auth-card { grid-column: 1; }
    }
  </style>
</head>
<body>
  <div class="auth-shell">
    <div class="auth-card">
      <div class="auth-logo">
        <div class="mark">🗂</div>
        <div>
          <div class="name">Sistem Arsip Digital</div>
          <div class="sub">Sistem Database</div>
        </div>
      </div>
      @yield('content')
    </div>
  </div>
</body>
</html>