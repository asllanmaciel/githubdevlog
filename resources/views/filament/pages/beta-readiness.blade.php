@php
  $report = App\Support\BetaReadiness::report();
@endphp

<x-filament-panels::page>
  <style>
    .beta{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--danger:#ff6b6b;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.9);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 88% 8%,rgba(80,184,255,.18),transparent 34%),rgba(16,23,32,.9)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,58px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.value{font-size:34px;font-weight:950}.label{color:var(--muted);font-size:13px}.grid{display:grid;grid-template-columns:1fr 420px;gap:16px;align-items:start}.rowx{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:13px;margin-bottom:10px;display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center}.rowx.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.dot{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950}.done .dot{background:var(--green);border-color:var(--green);color:#071018}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}@media(max-width:1050px){.grid,.metrics{grid-template-columns:1fr}}
  </style>

  <div class="beta">
    <section class="hero">
      <div class="kicker">Launch local</div>
      <h1 class="title">O que nao depende de producao precisa estar fechado aqui.</h1>
      <p class="lead">Este painel separa prontidao local/beta do que depende de dominio, GitHub App oficial, Mercado Pago producao e e-mail real.</p>
    </section>

    <section class="metrics">
      <div class="metric"><div class="value">{{ $report['percent'] }}%</div><div class="label">prontidao local</div></div>
      <div class="metric"><div class="value">{{ $report['metrics']['workspaces'] }}</div><div class="label">workspaces</div></div>
      <div class="metric"><div class="value">{{ $report['metrics']['plans'] }}</div><div class="label">planos</div></div>
      <div class="metric"><div class="value">{{ $report['metrics']['articles'] }}</div><div class="label">artigos publicados</div></div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Checklist local/beta</div>
        @foreach ($report['checks'] as $check)
          <div class="rowx {{ $check['done'] ? 'done' : '' }}">
            <div class="dot">{{ $check['done'] ? 'ok' : '!' }}</div>
            <div><strong>{{ $check['title'] }}</strong><div class="label">{{ $check['detail'] }}</div></div>
            <span class="pill">{{ $check['area'] }}</span>
          </div>
        @endforeach
      </div>

      <aside class="card">
        <div class="kicker">Depende de ambiente externo</div>
        @foreach ($report['external'] as $item)
          <div class="rowx {{ $item['done'] ? 'done' : '' }}">
            <div class="dot">{{ $item['done'] ? 'ok' : '!' }}</div>
            <div><strong>{{ $item['title'] }}</strong><div class="label">{{ $item['detail'] }}</div></div>
            <span class="pill">externo</span>
          </div>
        @endforeach
      </aside>
    </section>
  </div>
</x-filament-panels::page>