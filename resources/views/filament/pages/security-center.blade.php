@php
  $report = \App\Support\SecurityPosture::report();
@endphp

<x-filament-panels::page>
  <style>
    .security-center{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffcf66;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{display:grid;grid-template-columns:1fr auto;gap:18px;align-items:center;margin-bottom:16px}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0}.orb{width:132px;height:132px;border-radius:36px;display:grid;place-items:center;border:1px solid rgba(105,227,154,.32);background:radial-gradient(circle at 35% 25%,rgba(105,227,154,.28),rgba(80,184,255,.12) 44%,rgba(8,16,25,.94) 74%);font-weight:950;font-size:30px}
    .bar{height:10px;border-radius:999px;background:#0b1118;border:1px solid var(--line);overflow:hidden}.bar span{display:block;height:100%;background:linear-gradient(90deg,var(--blue),var(--green));border-radius:999px}.grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.check{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px}.check.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.badge{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950;color:var(--muted)}.check.done .badge{background:var(--green);border-color:var(--green);color:#071018}.detail{color:var(--muted);font-size:13px}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}@media(max-width:900px){.hero,.grid{grid-template-columns:1fr}}
  </style>

  <div class="security-center">
    <section class="hero">
      <div>
        <div class="kicker">Security posture</div>
        <h1 class="title">Postura de seguranca para lancamento.</h1>
        <p class="lead">Checks estruturais para reduzir risco antes de liberar o DevLog AI para devs externos e para avaliacao do GitHub Developer Program.</p>
        <div class="bar" style="margin-top:18px"><span style="width:{{ $report['percent'] }}%"></span></div>
      </div>
      <div class="orb">{{ $report['percent'] }}%</div>
    </section>

    <section class="grid">
      @foreach ($report['checks'] as $check)
        <article class="check {{ $check['done'] ? 'done' : '' }}">
          <div class="badge">{{ $check['done'] ? 'ok' : '!' }}</div>
          <div><strong>{{ $check['title'] }}</strong><div class="detail">{{ $check['detail'] }}</div></div>
          <span class="pill">{{ $check['done'] ? 'Pronto' : 'Verificar' }}</span>
        </article>
      @endforeach
    </section>
  </div>
</x-filament-panels::page>
