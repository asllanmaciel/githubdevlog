@php
  $report = App\Support\ProductionEnvironmentReadiness::report();
@endphp

<x-filament-panels::page>
  <style>
    .envcheck{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:20px;background:rgba(16,23,32,.92);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 90% 10%,rgba(80,184,255,.18),transparent 34%),rgba(16,23,32,.92)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,60px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.68;margin:14px 0 0;max-width:920px}
    .score{font-size:58px;font-weight:950;letter-spacing:-.07em}.rowx{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:14px;margin-bottom:10px;display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:start}.rowx.done{border-color:rgba(105,227,154,.42);background:rgba(105,227,154,.07)}
    .mark{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950;color:var(--yellow)}.done .mark{background:var(--green);border-color:var(--green);color:#071018}.label{color:var(--muted);font-size:13px}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;white-space:nowrap}
  </style>

  <div class="envcheck">
    <section class="hero">
      <div class="kicker">Produção</div>
      <div class="score">{{ $report['percent'] }}%</div>
      <h1 class="title">Checklist técnico de ambiente.</h1>
      <p class="lead">Acompanhe variáveis críticas para domínio, e-mail, filas, GitHub App e Mercado Pago. Valores sensíveis são mascarados automaticamente.</p>
    </section>

    <section class="card">
      <div class="kicker">Variáveis críticas</div>
      @foreach ($report['checks'] as $check)
        <div class="rowx {{ $check['done'] ? 'done' : '' }}">
          <div class="mark">{{ $check['done'] ? 'ok' : '!' }}</div>
          <div>
            <strong>{{ $check['key'] }}</strong>
            <div class="label">{{ $check['purpose'] }}</div>
            <div class="label">Valor: {{ $check['display'] }}</div>
          </div>
          <span class="pill">{{ $check['state'] }}</span>
        </div>
      @endforeach
    </section>
  </div>
</x-filament-panels::page>
