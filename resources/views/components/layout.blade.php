<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title ?? 'GitHub DevLog AI' }}</title>
  @php
    $isDashboardShell = request()->routeIs('dashboard') || request()->routeIs('dashboard.event');
  @endphp
  <meta name="description" content="Capture, valide e acompanhe webhooks do GitHub em workspaces privados, com segredo por conta e painel para debugging.">
  @php
    $analytics = config('devlog.analytics', []);
    $gtmId = $analytics['google_tag_manager_id'] ?? null;
    $gaId = $analytics['google_analytics_id'] ?? null;
    $metaPixelId = $analytics['meta_pixel_id'] ?? null;
    $hotjarId = $analytics['hotjar_id'] ?? null;
    $clarityId = $analytics['clarity_id'] ?? null;
    $plausibleDomain = $analytics['plausible_domain'] ?? null;
  @endphp
  @if (filled($gtmId))
    <script>
      (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $gtmId }}');
    </script>
  @endif
  @if (filled($gaId))
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '{{ $gaId }}');
    </script>
  @endif
  @if (filled($metaPixelId))
    <script>
      !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', '{{ $metaPixelId }}');
      fbq('track', 'PageView');
    </script>
  @endif
  @if (filled($hotjarId))
    <script>
      (function(h,o,t,j,a,r){h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};h._hjSettings={hjid:{{ $hotjarId }},hjsv:6};a=o.getElementsByTagName('head')[0];r=o.createElement('script');r.async=1;r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;a.appendChild(r);})(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
  @endif
  @if (filled($clarityId))
    <script>
      (function(c,l,a,r,i,t,y){c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};t=l.createElement(r);t.async=1;t.src='https://www.clarity.ms/tag/'+i;y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);})(window,document,'clarity','script','{{ $clarityId }}');
    </script>
  @endif
  @if (filled($plausibleDomain))
    <script defer data-domain="{{ $plausibleDomain }}" src="https://plausible.io/js/script.js"></script>
  @endif
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="icon" href="/favicon-32x32.png" sizes="32x32" type="image/png">
  <link rel="icon" href="/favicon-16x16.png" sizes="16x16" type="image/png">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="manifest" href="/site.webmanifest">
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
    .btnx.quiet { background:rgba(8,16,25,.56); border-color:rgba(72,101,128,.7); color:var(--ink); }
    .btn-icon { width:22px; height:22px; border-radius:8px; display:inline-grid; place-items:center; background:rgba(255,255,255,.08); font-size:11px; font-weight:950; }
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
    .control-strip { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-top:18px; }
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
    .bar-fill.warn { background:linear-gradient(90deg,var(--yellow),var(--orange)); }
    .bar-fill.danger { background:linear-gradient(90deg,var(--orange),var(--red)); }
    .quick-actions { display:grid; gap:10px; }
    .quick-action { display:flex; justify-content:space-between; gap:12px; align-items:center; border:1px solid var(--line); border-radius:12px; padding:12px; background:#0b1118; }
    .event-feed-head { display:flex; justify-content:space-between; gap:14px; flex-wrap:wrap; align-items:flex-start; margin-bottom:12px; }
    .event-feed-actions { display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end; align-items:center; }
    .config-card { position:sticky; top:16px; }
    .endpoint-box { background:#081019; border:1px solid var(--line); border-radius:8px; padding:12px; color:#a8d7ff; word-break:break-all; font-family:ui-monospace,SFMono-Regular,Consolas,monospace; font-size:13px; }
    .event-card { border:1px solid var(--line); border-radius:8px; background:linear-gradient(180deg, rgba(19,29,40,.92), rgba(13,19,26,.92)); padding:16px; margin-bottom:12px; box-shadow:0 20px 60px rgba(0,0,0,.18); }
    .event-top { display:flex; justify-content:space-between; gap:14px; flex-wrap:wrap; align-items:flex-start; }
    .event-type { display:flex; gap:12px; align-items:center; }
    .event-icon { width:52px; height:52px; border-radius:14px; display:grid; place-items:center; background:rgba(80,184,255,.12); border:1px solid rgba(80,184,255,.28); color:var(--blue); font-weight:950; box-shadow:inset 0 0 0 1px rgba(255,255,255,.04); }
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
    .event-card-compact { padding:14px; transition:transform .18s ease, border-color .18s ease, box-shadow .18s ease; }
    .event-card-compact:hover { transform:translateY(-2px); border-color:rgba(80,184,255,.72); box-shadow:0 28px 90px rgba(80,184,255,.12), 0 24px 80px rgba(0,0,0,.24); }
    .event-card-full { border-left-width:6px; }
    .event-topline { display:flex; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:14px; }
    .event-name { font-size:20px; letter-spacing:-.02em; }
    .pill.success { background:rgba(105,227,154,.14); color:var(--green); border-color:rgba(105,227,154,.38); }
    .pill.warning { background:rgba(255,209,102,.12); color:var(--yellow); border-color:rgba(255,209,102,.38); }
    .pill.soft { background:rgba(80,184,255,.1); color:#a8d7ff; }
    .event-insights { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:10px; margin:14px 0; }
    .insight { border:1px solid var(--line); border-radius:8px; background:#0b121a; padding:12px; }
    .insight-glyph { width:38px; height:38px; border-radius:14px; display:grid; place-items:center; color:#a8d7ff; background:rgba(80,184,255,.1); border:1px solid rgba(80,184,255,.26); font-size:11px; font-weight:950; flex:0 0 auto; }
    .insight span { display:block; color:var(--muted); font-size:12px; margin-bottom:4px; }
    .insight strong { font-size:16px; }
    .event-diagnostic { border:1px solid rgba(80,184,255,.28); background:rgba(80,184,255,.08); border-radius:8px; padding:12px; display:flex; gap:12px; align-items:flex-start; }
    .event-diagnostic span { color:var(--muted); }
    .section-orb { width:42px; height:42px; border-radius:50%; display:grid; place-items:center; background:linear-gradient(135deg, rgba(80,184,255,.22), rgba(20,42,74,.84)); border:1px solid rgba(80,184,255,.28); color:#a8d7ff; font-size:11px; font-weight:950; flex:0 0 auto; }
    .event-compact-footer { display:flex; justify-content:space-between; gap:12px; flex-wrap:wrap; align-items:center; margin-top:12px; padding-top:12px; border-top:1px solid rgba(39,53,68,.78); }
    .event-mini-stack { display:flex; gap:8px; flex-wrap:wrap; }
    .event-detail-shell { display:grid; gap:16px; }
    .event-detail-hero { display:flex; justify-content:space-between; gap:20px; align-items:flex-end; overflow:hidden; position:relative; }
    .event-detail-hero:before { content:""; position:absolute; right:-80px; top:-90px; width:240px; height:240px; border-radius:80px; background:radial-gradient(circle, rgba(80,184,255,.2), transparent 64%); pointer-events:none; }
    .event-detail-hero > * { position:relative; }
    .event-detail-actions { display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
    .file-strip { display:flex; gap:8px; flex-wrap:wrap; margin:12px 0; }
    .file-strip code { border:1px solid var(--line); border-radius:999px; background:#081018; padding:6px 9px; color:#b7e4ff; }
    .event-actions-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:12px; }
    .mini-panel { border:1px solid var(--line); border-radius:8px; background:#0b121a; padding:12px; }
    .mini-panel p { color:var(--muted); margin:8px 0; }
    @media (max-width: 900px) { .event-insights, .event-actions-grid { grid-template-columns:1fr; } .event-detail-hero { align-items:flex-start; flex-direction:column; } .event-detail-actions, .event-detail-actions .btnx { width:100%; } }
    body.app-dashboard {
      background:
        radial-gradient(circle at 0% 0%, rgba(80, 184, 255, .12), transparent 34%),
        radial-gradient(circle at 100% 0%, rgba(105, 227, 154, .1), transparent 28%),
        linear-gradient(120deg, #071018 0%, #09131b 48%, #07150f 100%);
    }
    body.app-dashboard .wrap {
      max-width: none;
      width: 100%;
      min-height: 100vh;
      padding: 0;
    }
    .app-shell { display:grid; grid-template-columns:292px minmax(0,1fr); min-height:100vh; }
    .app-sidebar {
      position:sticky;
      top:0;
      height:100vh;
      padding:18px 14px;
      border-right:1px solid rgba(39,53,68,.92);
      background:
        radial-gradient(circle at 18% 0%, rgba(80,184,255,.16), transparent 32%),
        linear-gradient(180deg, rgba(8,16,25,.98), rgba(5,11,17,.96));
      box-shadow:22px 0 80px rgba(0,0,0,.26);
      overflow:auto;
      z-index:6;
    }
    .app-sidebar .brand { width:100%; padding-bottom:16px; margin-bottom:16px; border-bottom:1px solid rgba(39,53,68,.86); }
    .app-sidebar .brand img { width:42px; height:42px; filter:drop-shadow(0 14px 28px rgba(80,184,255,.22)); }
    .app-user-card {
      border:1px solid rgba(56,77,96,.92);
      border-radius:16px;
      padding:14px;
      margin-bottom:16px;
      background:linear-gradient(135deg, rgba(80,184,255,.1), rgba(105,227,154,.06)), #08111a;
    }
    .app-avatar {
      width:42px;
      height:42px;
      border-radius:14px;
      display:grid;
      place-items:center;
      color:#061018;
      font-weight:950;
      background:linear-gradient(135deg,var(--blue),var(--green));
      box-shadow:0 14px 34px rgba(80,184,255,.18);
      flex:0 0 auto;
    }
    .app-user-name { font-weight:950; line-height:1.15; }
    .app-user-email { color:var(--muted); font-size:12px; word-break:break-all; margin-top:3px; }
    .app-menu-label { color:var(--muted); font-size:11px; letter-spacing:.14em; text-transform:uppercase; font-weight:950; margin:18px 0 8px; }
    .app-menu { display:grid; gap:8px; }
    .app-menu a,
    .app-menu button {
      width:100%;
      border:1px solid rgba(39,53,68,.92);
      border-radius:12px;
      background:rgba(11,17,24,.76);
      color:var(--ink);
      padding:11px 12px;
      display:flex;
      justify-content:space-between;
      align-items:center;
      gap:10px;
      font-weight:850;
      text-align:left;
    }
    .app-menu a:hover,
    .app-menu button:hover { border-color:var(--blue); color:var(--ink); }
    .app-menu a.active { color:#061018; border-color:transparent; background:linear-gradient(135deg,var(--blue),var(--green)); }
    .app-menu .hint { color:inherit; opacity:.72; font-size:12px; font-weight:900; }
    .app-sidebar form { margin:0; }
    .app-content { min-width:0; }
    .app-mobilebar { display:none; }
    body.app-dashboard main {
      width: min(100% - 36px, 1420px);
      padding: 28px 0 54px;
      margin: 0 auto;
    }
    body.app-dashboard .dashboard-hero { grid-template-columns: minmax(0, 1.45fr) minmax(340px, .72fr); gap: 18px; }
    body.app-dashboard .dashboard-title { max-width: 980px; font-size: clamp(42px, 4.2vw, 72px); }
    body.app-dashboard .cardx,
    body.app-dashboard .metric,
    body.app-dashboard .event-card,
    body.app-dashboard .control-card,
    body.app-dashboard .insight-card {
      border-radius: 16px;
      background: linear-gradient(180deg, rgba(15, 25, 35, .94), rgba(8, 15, 22, .9));
      border-color: rgba(56, 77, 96, .92);
      box-shadow: 0 24px 80px rgba(0, 0, 0, .22);
    }
    body.app-dashboard .metric-grid { grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    body.app-dashboard .dashboard-grid { grid-template-columns: 410px minmax(0, 1fr); gap: 18px; }
    body.app-dashboard .config-card { top: 28px; }
    body.app-dashboard .event-feed-head {
      border-radius: 16px;
      padding: 18px;
      background: linear-gradient(135deg, rgba(80,184,255,.08), rgba(105,227,154,.05)), rgba(8, 15, 22, .74);
      border: 1px solid rgba(56, 77, 96, .8);
    }
    body.app-dashboard .alert { width: min(100% - 36px, 1420px); margin: 18px auto 0; border-radius: 14px; }
    body.app-dashboard .event-detail-shell { display:grid; gap:16px; }
    body.app-dashboard .event-detail-hero {
      position:relative;
      overflow:hidden;
      min-height:230px;
      padding:28px;
      display:flex;
      justify-content:space-between;
      align-items:flex-end;
      gap:22px;
      border-color:rgba(80,184,255,.34) !important;
      background:
        radial-gradient(circle at 88% 12%, rgba(80,184,255,.28), transparent 34%),
        radial-gradient(circle at 8% 100%, rgba(105,227,154,.16), transparent 34%),
        linear-gradient(135deg, rgba(9,18,28,.98), rgba(11,31,43,.9) 48%, rgba(6,16,18,.98)) !important;
    }
    body.app-dashboard .event-detail-hero:after {
      content:"";
      position:absolute;
      left:28px;
      bottom:0;
      width:54%;
      height:4px;
      background:linear-gradient(90deg,var(--blue),var(--green),transparent);
      border-radius:999px;
    }
    body.app-dashboard .event-detail-hero > * { position:relative; z-index:1; }
    body.app-dashboard .event-detail-actions { display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
    body.app-dashboard .event-backlink { display:inline-flex; align-items:center; gap:8px; color:var(--blue); font-size:12px; text-transform:uppercase; letter-spacing:.13em; font-weight:950; margin-bottom:14px; }
    body.app-dashboard .event-hero-pills { display:flex; gap:8px; flex-wrap:wrap; margin-top:18px; }
    body.app-dashboard .event-command-strip { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; }
    body.app-dashboard .event-command-card {
      border:1px solid rgba(67,96,121,.88);
      border-radius:18px;
      padding:22px;
      background:linear-gradient(180deg, rgba(15,25,35,.9), rgba(5,11,17,.92));
      box-shadow:0 18px 60px rgba(0,0,0,.18);
      position:relative;
      overflow:hidden;
    }
    body.app-dashboard .event-command-card:after { content:""; position:absolute; right:18px; top:50%; width:34px; height:34px; margin-top:-17px; border-radius:50%; background:linear-gradient(135deg, rgba(80,184,255,.3), rgba(105,227,154,.22)); box-shadow:0 0 28px rgba(80,184,255,.18); }
    body.app-dashboard .event-command-card span {
      display:block;
      color:var(--muted);
      font-size:11px;
      text-transform:uppercase;
      letter-spacing:.13em;
      font-weight:950;
    }
    body.app-dashboard .event-command-card strong {
      display:block;
      font-size:24px;
      line-height:1.1;
      letter-spacing:-.04em;
      margin-top:8px;
    }
    body.app-dashboard .event-command-card small {
      display:block;
      color:var(--muted);
      margin-top:8px;
      overflow:hidden;
      text-overflow:ellipsis;
      white-space:nowrap;
    }
    body.app-dashboard .event-card-full {
      border-left-width:0;
      padding:22px;
      position:relative;
      overflow:hidden;
      background:linear-gradient(145deg, rgba(13,24,34,.98), rgba(5,12,18,.96)) !important;
    }
    body.app-dashboard .event-card-full:before {
      content:"";
      position:absolute;
      inset:0;
      background:
        radial-gradient(circle at 0% 0%, rgba(80,184,255,.12), transparent 30%),
        radial-gradient(circle at 100% 18%, rgba(105,227,154,.09), transparent 28%);
      pointer-events:none;
    }
    body.app-dashboard .event-card-full > * { position:relative; z-index:1; }
    body.app-dashboard .event-card-full .event-topline {
      padding:20px;
      border:1px solid rgba(80,184,255,.24);
      border-radius:18px;
      background:linear-gradient(135deg, rgba(80,184,255,.1), rgba(105,227,154,.04));
      margin-bottom:18px;
    }
    body.app-dashboard .event-card-full .event-insights { gap:14px; margin:16px 0; }
    body.app-dashboard .event-card-full .insight {
      min-height:92px;
      border-radius:16px;
      background:rgba(3,9,14,.72);
      border-color:rgba(67,96,121,.88);
      display:flex;
      align-items:center;
      gap:14px;
    }
    body.app-dashboard .event-card-full .insight strong { font-size:22px; letter-spacing:-.035em; }
    body.app-dashboard .event-card-full .event-diagnostic {
      border-radius:18px;
      padding:18px;
      background:linear-gradient(135deg, rgba(80,184,255,.08), rgba(8,16,25,.84));
    }
    body.app-dashboard .event-card-full .ai-diagnostic {
      display:block;
      border-color:rgba(80,184,255,.48);
      background:linear-gradient(145deg, rgba(80,184,255,.16), rgba(105,227,154,.06) 46%, rgba(6,13,20,.9));
      box-shadow:inset 0 0 0 1px rgba(255,255,255,.025), 0 24px 70px rgba(80,184,255,.1);
    }
    body.app-dashboard .event-card-full .ai-diagnostic strong { font-size:20px; letter-spacing:-.02em; }
    body.app-dashboard .event-card-full .ai-diagnostic > .d-flex > .pill { display:none; }
    body.app-dashboard .event-card-full .ai-diagnostic .pill { margin-top:0; }
    body.app-dashboard .ai-meta-line { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:14px; color:var(--muted); font-size:13px; }
    body.app-dashboard .ai-meta-line span:not(.pill) { padding-left:10px; border-left:1px solid rgba(80,184,255,.22); }
    body.app-dashboard .event-card-full .ai-diagnostic ul { columns:2; column-gap:42px; padding-left:0; list-style:none; margin-top:18px !important; }
    body.app-dashboard .event-card-full .ai-diagnostic li { position:relative; break-inside:avoid; padding-left:28px; margin-bottom:10px; }
    body.app-dashboard .event-card-full .ai-diagnostic li:before { content:""; position:absolute; left:0; top:.3em; width:15px; height:15px; border-radius:50%; border:1px solid var(--blue); background:radial-gradient(circle, var(--blue) 0 28%, transparent 32%); }
    body.app-dashboard .ai-action-bar { display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-top:18px; padding-top:18px; border-top:1px solid rgba(80,184,255,.16); }
    body.app-dashboard .ai-action-bar form { margin:0; }
    body.app-dashboard .event-card-full .event-actions-grid { gap:16px; margin-top:16px; }
    body.app-dashboard .event-card-full .mini-panel {
      border-radius:18px;
      padding:20px;
      background:rgba(3,9,14,.76);
      border-color:rgba(67,96,121,.82);
    }
    body.app-dashboard .mini-panel-head { display:flex; justify-content:space-between; gap:12px; align-items:center; margin-bottom:12px; }
    body.app-dashboard .mini-panel-head strong { font-size:18px; }
    body.app-dashboard .event-card-full textarea,
    body.app-dashboard .event-card-full input { background:rgba(2,8,13,.78); border-color:rgba(67,96,121,.72); }
    @media (max-width: 1100px) {
      .app-shell { display:block; }
      .app-sidebar { display:none; }
      .app-mobilebar {
        position:sticky;
        top:0;
        z-index:7;
        display:flex;
        justify-content:space-between;
        align-items:center;
        gap:12px;
        padding:12px 14px;
        border-bottom:1px solid rgba(39,53,68,.92);
        background:rgba(8,16,25,.96);
        backdrop-filter:blur(12px);
      }
      body.app-dashboard main { padding:20px 14px 42px; width:100%; }
      body.app-dashboard .event-detail-hero { align-items:flex-start; flex-direction:column; min-height:auto; }
      body.app-dashboard .event-detail-actions, body.app-dashboard .event-detail-actions .btnx { width:100%; }
      body.app-dashboard .event-command-strip { grid-template-columns:1fr; }
      body.app-dashboard .event-card-full .ai-diagnostic ul { columns:1; }
      body.app-dashboard .event-card-full .insight { align-items:flex-start; }
    }
  </style>
</head>
<body class="{{ $isDashboardShell ? 'app-dashboard' : '' }}">
  @if (filled($gtmId))
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  @endif
  @if (filled($metaPixelId))
    <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id={{ $metaPixelId }}&ev=PageView&noscript=1" alt=""></noscript>
  @endif
  <div class="wrap">
    @if ($isDashboardShell)
      @php
        $dashboardUser = auth()->user();
        $dashboardWorkspace = $dashboardUser?->workspaces()->first();
        $dashboardRole = $dashboardWorkspace ? \App\Support\WorkspaceAccess::currentRole($dashboardUser, $dashboardWorkspace) : null;
        $initials = collect(explode(' ', trim((string) ($dashboardUser?->name ?: $dashboardUser?->email))))
          ->filter()
          ->take(2)
          ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
          ->join('');
      @endphp
      <div class="app-shell">
        <aside class="app-sidebar" aria-label="Menu do workspace">
          <a class="brand" href="{{ route('dashboard') }}">
            <img src="/logo-mark.png" alt="GitHub DevLog AI">
            <span><strong>GitHub DevLog AI</strong><span>Painel do workspace</span></span>
          </a>

          <div class="app-user-card">
            <div class="d-flex gap-3 align-items-center">
              <div class="app-avatar">{{ $initials ?: 'DV' }}</div>
              <div>
                <div class="app-user-name">{{ $dashboardUser?->name }}</div>
                <div class="app-user-email">{{ $dashboardUser?->email }}</div>
              </div>
            </div>
            <div class="mt-3 d-flex gap-2 flex-wrap">
              <span class="pill">{{ $dashboardWorkspace?->name ?? 'Sem workspace' }}</span>
              <span class="pill">{{ $dashboardRole ?? 'membro' }}</span>
            </div>
          </div>

          <div class="app-menu-label">Operação</div>
          <nav class="app-menu">
            <a class="{{ request()->routeIs('dashboard') && (request()->route('section') === null || request()->route('section') === 'overview') ? 'active' : '' }}" href="{{ route('dashboard') }}"><span>Visão geral</span><span class="hint">Home</span></a>
            <a class="{{ request()->route('section') === 'events' || request()->routeIs('dashboard.event') ? 'active' : '' }}" href="{{ route('dashboard', ['section' => 'events']) }}"><span>Eventos</span><span class="hint">Webhook</span></a>
            <a class="{{ request()->route('section') === 'github' ? 'active' : '' }}" href="{{ route('dashboard', ['section' => 'github']) }}"><span>GitHub App e endpoint</span><span class="hint">Setup</span></a>
            <a class="{{ request()->route('section') === 'ai' ? 'active' : '' }}" href="{{ route('dashboard', ['section' => 'ai']) }}"><span>AI do workspace</span><span class="hint">Análise</span></a>
            <a class="{{ request()->route('section') === 'team' ? 'active' : '' }}" href="{{ route('dashboard', ['section' => 'team']) }}"><span>Equipe e permissões</span><span class="hint">Acesso</span></a>
            <a class="{{ request()->route('section') === 'billing' ? 'active' : '' }}" href="{{ route('dashboard', ['section' => 'billing']) }}"><span>Uso, plano e alertas</span><span class="hint">Billing</span></a>
            <a href="{{ route('support') }}"><span>Suporte</span><span class="hint">SLA</span></a>
          </nav>

          <div class="app-menu-label">Conta</div>
          <nav class="app-menu">
            <a href="{{ route('home') }}"><span>Site público</span><span class="hint">Abrir</span></a>
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="submit"><span>Sair</span><span class="hint">Logout</span></button>
            </form>
          </nav>
        </aside>

        <div class="app-content">
          <div class="app-mobilebar">
            <a class="brand" href="{{ route('dashboard') }}">
              <img src="/logo-mark.png" alt="GitHub DevLog AI">
              <span><strong>GitHub DevLog AI</strong><span>{{ $dashboardUser?->email }}</span></span>
            </a>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="btnx" type="submit">Sair</button></form>
          </div>
    @else
      <header class="topbar">
        <a class="brand" href="{{ route('home') }}">
          <img src="/logo-mark.png" alt="GitHub DevLog AI">
          <span><strong>GitHub DevLog AI</strong><span>Webhook inbox privado para GitHub</span></span>
        </a>
        <nav class="nav" aria-label="Menu principal">
          <a class="btnx" href="{{ route('home') }}#produto">Produto</a>
          <a class="btnx" href="{{ route('docs.api') }}">API</a>
          <a class="btnx" href="{{ route('trust') }}">Confiança</a>
          <a class="btnx" href="{{ route('faq') }}">FAQ</a>
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
    @endif

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

    @if ($isDashboardShell)
        </div>
      </div>
    @endif
  </div>
</body>
</html>
