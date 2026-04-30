<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'GitHub DevLog AI' }}</title>
  <meta name="description" content="Capture, valide e acompanhe webhooks do GitHub em workspaces privados, com segredo por conta e painel para debugging.">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <style>
    :root {
      --bg: #090d12;
      --panel: #101720;
      --panel-2: #131d28;
      --ink: #f3f7fb;
      --muted: #9aa9b5;
      --line: #273544;
      --blue: #50b8ff;
      --green: #69e39a;
      --yellow: #ffd166;
      --orange: #ff9f5a;
      --red: #ff6b6b;
    }
    * { box-sizing: border-box; }
    body {
      margin: 0;
      background:
        radial-gradient(circle at 12% 18%, rgba(80, 184, 255, .15), transparent 32%),
        radial-gradient(circle at 84% 8%, rgba(105, 227, 154, .12), transparent 28%),
        linear-gradient(180deg, #090d12 0%, #0d131a 54%, #090d12 100%);
      color: var(--ink);
      font-family: Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, sans-serif;
    }
    a { color: inherit; text-decoration: none; }
    .wrap { max-width: 1200px; margin: 0 auto; padding: 20px; }
    .topbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 10px 0 38px; }
    .brand { display: flex; gap: 12px; align-items: center; }
    .brand img { width: 42px; height: 42px; }
    .brand strong { display: block; font-size: 18px; letter-spacing: -.02em; }
    .brand span { color: var(--muted); font-size: 13px; }
    .nav { display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
    .btnx { border: 1px solid var(--line); border-radius: 8px; background: rgba(16, 23, 32, .82); color: var(--ink); padding: 10px 14px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; }
    .btnx.primary { background: var(--blue); border-color: var(--blue); color: #071018; }
    .btnx.success { background: var(--green); border-color: var(--green); color: #071018; }
    .btnx:hover { border-color: var(--blue); color: var(--ink); }
    .btnx.primary:hover, .btnx.success:hover { color: #071018; }
    .hero { padding: 28px 0 64px; }
    .eyebrow { display: inline-flex; align-items: center; gap: 8px; border: 1px solid var(--line); border-radius: 999px; padding: 8px 12px; color: var(--green); background: rgba(105, 227, 154, .06); font-size: 13px; font-weight: 900; margin-bottom: 18px; }
    h1 { max-width: 960px; font-size: clamp(46px, 7vw, 88px); line-height: .96; letter-spacing: -.06em; font-weight: 950; margin: 0 0 22px; }
    .lead { max-width: 760px; color: var(--muted); font-size: 20px; line-height: 1.65; }
    .hero-grid { display: grid; grid-template-columns: 1.05fr .95fr; gap: 18px; align-items: end; margin-top: 36px; }
    .terminal, .panel { border: 1px solid var(--line); border-radius: 8px; background: rgba(16, 23, 32, .86); box-shadow: 0 30px 80px rgba(0, 0, 0, .36); overflow: hidden; }
    .bar { display: flex; gap: 8px; padding: 12px; border-bottom: 1px solid var(--line); background: #0c1219; }
    .dot { width: 10px; height: 10px; border-radius: 50%; background: #ff6b6b; }
    .dot:nth-child(2) { background: var(--yellow); }
    .dot:nth-child(3) { background: var(--green); }
    .terminal pre { margin: 0; padding: 18px; min-height: 310px; color: #b7e4ff; white-space: pre-wrap; background: transparent; border: 0; }
    .panel { padding: 16px; }
    .signal { border: 1px solid var(--line); border-radius: 8px; padding: 14px; margin-bottom: 10px; background: #0f171f; }
    .signal strong { display: flex; justify-content: space-between; gap: 12px; }
    .signal span { color: var(--muted); font-size: 13px; }
    .band { border-top: 1px solid var(--line); padding: 56px 0; }
    .kicker { color: var(--blue); font-size: 12px; text-transform: uppercase; letter-spacing: .14em; font-weight: 950; margin-bottom: 10px; }
    h2 { font-size: clamp(30px, 4vw, 50px); line-height: 1.05; letter-spacing: -.04em; font-weight: 950; margin-bottom: 16px; }
    .cardx { border: 1px solid var(--line); border-radius: 8px; background: rgba(16, 23, 32, .82); padding: 18px; }
    .band .cardx { height: 100%; }
    .cardx h3 { font-size: 18px; font-weight: 950; margin-bottom: 8px; }
    .cardx p { color: var(--muted); line-height: 1.65; margin: 0; }
    .creator-profile { display:grid; grid-template-columns:180px 1fr; gap:28px; align-items:center; border:1px solid var(--line); border-radius:24px; background:radial-gradient(circle at 12% 10%, rgba(80,184,255,.18), transparent 32%), linear-gradient(135deg, rgba(16,23,32,.96), rgba(10,16,22,.9)); padding:28px; box-shadow:0 28px 90px rgba(0,0,0,.28); }
    .creator-photo { width:156px; height:156px; border-radius:50%; object-fit:cover; border:3px solid rgba(105,227,154,.7); box-shadow:0 0 0 10px rgba(105,227,154,.08), 0 24px 80px rgba(80,184,255,.18); background:#0b1118; }
    .creator-badges { display:flex; flex-wrap:wrap; gap:8px; margin:16px 0; }    .steps { counter-reset: step; }
    .step { position: relative; padding-left: 54px; }
    .step:before { counter-increment: step; content: counter(step); position: absolute; left: 18px; top: 18px; width: 26px; height: 26px; border-radius: 8px; display: grid; place-items: center; background: var(--blue); color: #071018; font-weight: 950; }
    .footer { border-top: 1px solid var(--line); padding: 30px 0; color: var(--muted); }
    .muted { color: var(--muted); }
    .pill { border:1px solid var(--line); border-radius:999px; padding:5px 9px; color:var(--muted); font-size:12px; display:inline-flex; align-items:center; gap:6px; }
    input, textarea { width:100%; border:1px solid var(--line); border-radius:8px; background:#0b1118; color:var(--ink); padding:12px; }
    label { color:var(--muted); margin-bottom:6px; font-weight:750; }
    pre, code { color:#a8d7ff; font-family:ui-monospace,SFMono-Regular,Consolas,monospace; }
    pre { background:#0b1118; border:1px solid var(--line); border-radius:8px; padding:12px; white-space:pre-wrap; word-break:break-word; }
    .dashboard-hero { display:grid; grid-template-columns:1.15fr .85fr; gap:16px; align-items:stretch; margin-bottom:16px; }
    .dashboard-title { font-size:clamp(32px,5vw,58px); line-height:.98; letter-spacing:-.055em; font-weight:950; margin:0; }
    .metric-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:16px; }
    .metric { border:1px solid var(--line); border-radius:8px; background:linear-gradient(180deg, rgba(16,23,32,.92), rgba(15,23,31,.78)); padding:14px; min-height:106px; position:relative; overflow:hidden; }
    .metric:after { content:""; position:absolute; right:-28px; top:-28px; width:84px; height:84px; border-radius:50%; background:rgba(80,184,255,.1); }
    .metric-value { font-size:32px; font-weight:950; letter-spacing:-.05em; }
    .metric-label { color:var(--muted); font-size:13px; }
    .spark { height:8px; display:flex; gap:3px; align-items:end; margin-top:14px; }
    .spark span { flex:1; min-width:5px; border-radius:999px; background:linear-gradient(180deg,var(--blue),rgba(80,184,255,.22)); }
    .dashboard-grid { display:grid; grid-template-columns:360px 1fr; gap:16px; align-items:start; }
    .ops-grid { display:grid; grid-template-columns:1.15fr .85fr; gap:16px; align-items:stretch; margin-bottom:16px; }
    .control-strip { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:18px; }
    .control-card { border:1px solid var(--line); border-radius:12px; padding:12px; background:#0b1118; }
    .control-label { color:var(--muted); font-size:11px; text-transform:uppercase; letter-spacing:.1em; font-weight:900; }
    .control-value { font-size:20px; font-weight:950; letter-spacing:-.03em; margin-top:3px; }
    .health-panel { min-height:100%; display:flex; flex-direction:column; justify-content:space-between; }
    .health-orb { width:112px; height:112px; border-radius:34px; display:grid; place-items:center; background:radial-gradient(circle at 35% 25%, rgba(105,227,154,.28), rgba(80,184,255,.12) 42%, rgba(8,16,25,.94) 72%); border:1px solid rgba(105,227,154,.3); box-shadow:0 22px 60px rgba(105,227,154,.12), inset 0 0 0 8px rgba(105,227,154,.04); font-size:28px; font-weight:950; }
    .mini-board { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; margin-bottom:16px; }
    .insight-card { border:1px solid var(--line); border-radius:14px; background:linear-gradient(180deg, rgba(16,23,32,.92), rgba(11,17,24,.88)); padding:16px; min-height:160px; }
    .insight-list { display:grid; gap:10px; margin-top:12px; }
    .insight-row { display:flex; justify-content:space-between; gap:12px; align-items:center; border:1px solid rgba(39,53,68,.85); border-radius:10px; padding:10px; background:#0b1118; }
    .bar-track { height:9px; border-radius:999px; background:#081019; border:1px solid var(--line); overflow:hidden; }
    .bar-fill { display:block; height:100%; border-radius:999px; background:linear-gradient(90deg,var(--blue),var(--green)); }
    .quick-actions { display:grid; gap:10px; }
    .quick-action { display:flex; justify-content:space-between; gap:12px; align-items:center; border:1px solid var(--line); border-radius:12px; padding:12px; background:#0b1118; }
    .event-feed-head { display:flex; justify-content:space-between; gap:14px; flex-wrap:wrap; align-items:flex-start; margin-bottom:12px; }
    .config-card { position:sticky; top:16px; }
    .endpoint-box { background:#081019; border:1px solid var(--line); border-radius:8px; padding:12px; color:#a8d7ff; word-break:break-all; font-family:ui-monospace,SFMono-Regular,Consolas,monospace; font-size:13px; }
    .event-card { border:1px solid var(--line); border-radius:8px; background:linear-gradient(180deg, rgba(19,29,40,.92), rgba(13,19,26,.92)); padding:16px; margin-bottom:12px; box-shadow:0 20px 60px rgba(0,0,0,.18); }
    .event-top { display:flex; justify-content:space-between; gap:14px; flex-wrap:wrap; align-items:flex-start; }
    .event-type { display:flex; gap:12px; align-items:center; }
    .event-icon { width:42px; height:42px; border-radius:8px; display:grid; place-items:center; background:rgba(80,184,255,.12); border:1px solid rgba(80,184,255,.28); color:var(--blue); font-weight:950; }
    .event-title { font-size:18px; font-weight:950; }
    .event-subtitle { color:var(--muted); font-size:13px; margin-top:2px; }
    .event-summary { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin:14px 0; }
    .summary-cell { border:1px solid var(--line); border-radius:8px; padding:10px; background:#0b1118; min-height:74px; }
    .summary-label { color:var(--muted); font-size:11px; text-transform:uppercase; letter-spacing:.1em; font-weight:900; margin-bottom:4px; }
    .summary-value { font-weight:850; word-break:break-word; }
    .file-list { display:flex; flex-wrap:wrap; gap:6px; margin-top:8px; }
    .file-chip { border:1px solid rgba(80,184,255,.26); color:#b7e4ff; background:rgba(80,184,255,.08); border-radius:999px; padding:4px 8px; font-size:12px; }
    details.payload { margin-top:12px; }
    details.payload summary { cursor:pointer; color:var(--blue); font-weight:850; }
    details.payload pre { max-height:420px; overflow:auto; margin-top:10px; }
    .status-ok { color:var(--green); }
    .status-warn { color:var(--yellow); }
    .roadmap-board { display:grid; gap:16px; }
    .roadmap-phase { border:1px solid var(--line); border-radius:14px; background:linear-gradient(180deg, rgba(16,23,32,.94), rgba(10,15,21,.9)); padding:16px; box-shadow:0 20px 60px rgba(0,0,0,.2); position:relative; overflow:hidden; }
    .roadmap-phase:before { content:""; position:absolute; inset:0; background:radial-gradient(circle at 92% 0%, rgba(80,184,255,.14), transparent 34%), radial-gradient(circle at 10% 100%, rgba(105,227,154,.1), transparent 30%); pointer-events:none; }
    .roadmap-phase > * { position:relative; }
    .roadmap-head { display:flex; justify-content:space-between; gap:16px; align-items:flex-start; flex-wrap:wrap; margin-bottom:14px; }
    .roadmap-title { font-size:22px; font-weight:950; letter-spacing:-.03em; margin:0; }
    .roadmap-count { color:var(--muted); font-size:13px; margin-top:4px; }
    .roadmap-percent { min-width:82px; height:82px; border-radius:24px; display:grid; place-items:center; border:1px solid rgba(80,184,255,.28); background:rgba(80,184,255,.08); color:var(--ink); font-size:24px; font-weight:950; box-shadow:inset 0 0 0 6px rgba(80,184,255,.06); }
    .roadmap-progress { height:10px; border-radius:999px; background:#0b1118; border:1px solid var(--line); overflow:hidden; margin-bottom:14px; }
    .roadmap-progress span { display:block; height:100%; border-radius:999px; background:linear-gradient(90deg,var(--blue),var(--green)); box-shadow:0 0 20px rgba(80,184,255,.28); }
    .roadmap-items { display:grid; gap:10px; }
    .roadmap-item { display:grid; grid-template-columns:auto 1fr auto; gap:12px; align-items:center; border:1px solid rgba(39,53,68,.9); border-radius:12px; padding:12px; background:rgba(11,17,24,.78); }
    .roadmap-check { width:34px; height:34px; border-radius:12px; display:grid; place-items:center; border:1px solid var(--line); color:var(--muted); font-weight:950; background:#081019; }
    .roadmap-item.done { border-color:rgba(105,227,154,.38); background:rgba(105,227,154,.07); }
    .roadmap-item.done .roadmap-check { background:var(--green); border-color:var(--green); color:#071018; }
    .roadmap-item-title { font-weight:950; letter-spacing:-.01em; }
    .roadmap-item-desc { color:var(--muted); line-height:1.55; margin-top:3px; }
    .roadmap-meta { display:flex; gap:6px; flex-wrap:wrap; margin-top:8px; }
    .roadmap-meta .pill { background:rgba(8,16,25,.78); }
    .roadmap-action { white-space:nowrap; }
    @media (max-width: 700px) {
      .roadmap-item { grid-template-columns:auto 1fr; }
      .roadmap-action { grid-column:1 / -1; }
      .roadmap-action .btnx { width:100%; }
    }
    @media (max-width: 1000px) {
      .dashboard-hero, .dashboard-grid, .ops-grid { grid-template-columns:1fr; }
      .metric-grid { grid-template-columns:repeat(2,1fr); }
      .mini-board { grid-template-columns:1fr; }
      .config-card { position:static; }
    }
    @media (max-width: 900px) {
      .topbar { align-items: flex-start; flex-direction: column; }
      .hero-grid { grid-template-columns: 1fr; }
      .nav { justify-content: flex-start; }
      .event-summary { grid-template-columns:1fr; }
      .creator-profile { display:grid; grid-template-columns:180px 1fr; gap:28px; align-items:center; border:1px solid var(--line); border-radius:24px; background:radial-gradient(circle at 12% 10%, rgba(80,184,255,.18), transparent 32%), linear-gradient(135deg, rgba(16,23,32,.96), rgba(10,16,22,.9)); padding:28px; box-shadow:0 28px 90px rgba(0,0,0,.28); }
      .creator-photo { width:156px; height:156px; border-radius:50%; object-fit:cover; border:3px solid rgba(105,227,154,.7); box-shadow:0 0 0 10px rgba(105,227,154,.08), 0 24px 80px rgba(80,184,255,.18); background:#0b1118; }
      .control-strip { grid-template-columns:1fr; }
    }
      .event-control-panel { position:relative; overflow:hidden; }
    .event-filters { display:flex; gap:8px; flex-wrap:wrap; margin-top:18px; }
    .filter-chip { border:1px solid var(--line); background:#0c141d; color:var(--muted); border-radius:999px; padding:8px 12px; font-weight:850; }
    .filter-chip.active, .filter-chip:hover { color:#071018; background:var(--green); border-color:var(--green); }
    .event-card { border-left:4px solid var(--blue); }
    .event-topline { display:flex; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:14px; }
    .event-name { font-size:20px; letter-spacing:-.02em; }
    .pill.success { background:rgba(105,227,154,.14); color:var(--green); border-color:rgba(105,227,154,.38); }
    .pill.warning { background:rgba(255,209,102,.12); color:var(--yellow); border-color:rgba(255,209,102,.38); }
    .pill.soft { background:rgba(80,184,255,.1); color:#a8d7ff; }
    .event-insights { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:10px; margin:14px 0; }
    .insight { border:1px solid var(--line); border-radius:8px; background:#0b121a; padding:12px; }
    .insight span { display:block; color:var(--muted); font-size:12px; margin-bottom:4px; }
    .insight strong { font-size:16px; }
    .event-diagnostic { border:1px solid rgba(80,184,255,.28); background:rgba(80,184,255,.08); border-radius:8px; padding:12px; display:flex; gap:10px; flex-wrap:wrap; }
    .event-diagnostic span { color:var(--muted); }
    .file-strip { display:flex; gap:8px; flex-wrap:wrap; margin:12px 0; }
    .file-strip code { border:1px solid var(--line); border-radius:999px; background:#081018; padding:6px 9px; color:#b7e4ff; }
    .event-actions-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:12px; }
    .mini-panel { border:1px solid var(--line); border-radius:8px; background:#0b121a; padding:12px; }
    .mini-panel p { color:var(--muted); margin:8px 0; }
    @media (max-width: 900px) { .event-insights, .event-actions-grid { grid-template-columns:1fr; } }
  </style>
</head>
<body>
  <div class="wrap">
    <header class="topbar">
      <a class="brand" href="{{ route('home') }}">
        <img src="/logo.svg" alt="GitHub DevLog AI">
        <span><strong>GitHub DevLog AI</strong><span>Webhook inbox privado para GitHub</span></span>
      </a>
      <nav class="nav" aria-label="Menu principal">
        <a class="btnx" href="{{ route('home') }}#produto">Produto</a>
        <a class="btnx" href="{{ route('docs.api') }}">API</a>
        @auth
          @if(Auth::user()->is_super_admin)
            <a class="btnx primary" href="{{ url('/admin') }}">Admin</a>
            <a class="btnx" href="{{ route('admin.roadmap.dashboard') }}">Roadmap</a>
          @else
            <a class="btnx primary" href="{{ route('dashboard') }}">Painel</a>
          @endif
          <form method="POST" action="{{ route('logout') }}">@csrf<button class="btnx" type="submit">Sair</button></form>
        @else
          <a class="btnx" href="{{ route('login') }}">Entrar</a>
          <a class="btnx primary" href="{{ route('register') }}">Criar workspace</a>
        @endauth
      </nav>
    </header>

    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="alert alert-danger">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    {{ $slot }}
  </div>
</body>
</html>





