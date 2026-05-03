@php
  $report = \App\Support\ProductMetrics::report();
@endphp

<x-filament-panels::page>
  <style>
    .pm{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.9);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 92% 0%,rgba(80,184,255,.16),transparent 34%),rgba(16,23,32,.9)}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}.metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.value{font-size:34px;font-weight:950;letter-spacing:-.04em}.label{color:var(--muted);font-size:13px;line-height:1.5}.layout{display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start}.funnel{display:grid;gap:10px}.row{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}.top{display:flex;justify-content:space-between;gap:12px;align-items:center}.bar{height:10px;border:1px solid var(--line);background:#071018;border-radius:999px;overflow:hidden;margin-top:10px}.bar span{display:block;height:100%;background:linear-gradient(90deg,var(--blue),var(--green));border-radius:999px}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.pill.ok{background:var(--green);border-color:var(--green);color:#061018;font-weight:950}.pill.warn{background:var(--yellow);border-color:var(--yellow);color:#061018;font-weight:950}@media(max-width:1000px){.metrics,.layout{grid-template-columns:1fr}}
  </style>

  <div class="pm">
    <section class="hero">
      <div class="kicker">Produto / Decisao</div>
      <h1 class="title">Metricas acionaveis do SaaS.</h1>
      <p class="lead">Uma leitura curta de funil, ativacao, receita, webhooks validados, billing e riscos operacionais para decidir o proximo passo sem garimpar telas separadas.</p>
    </section>

    <section class="metrics">
      @foreach ($report['metrics'] as $metric)
        <div class="metric">
          <div class="kicker">{{ $metric['label'] }}</div>
          <div class="value">{{ $metric['value'] }}</div>
          <div class="label">{{ $metric['detail'] }}</div>
        </div>
      @endforeach
    </section>

    <section class="layout">
      <div class="card">
        <div class="kicker">Funil</div>
        <div class="funnel">
          @foreach ($report['funnel'] as $step)
            <div class="row">
              <div class="top">
                <strong>{{ $step['label'] }}</strong>
                <span class="pill">{{ $step['count'] }} · {{ $step['percent'] }}%</span>
              </div>
              <div class="bar"><span style="width:{{ $step['percent'] }}%"></span></div>
            </div>
          @endforeach
        </div>
      </div>

      <aside class="card">
        <div class="kicker">Riscos</div>
        <div class="funnel">
          @foreach ($report['risks'] as $risk)
            <div class="row">
              <div class="top">
                <div>
                  <strong>{{ $risk['label'] }}</strong>
                  <div class="label">{{ $risk['value'] }} ocorrência(s)</div>
                </div>
                <span class="pill {{ $risk['state'] === 'ok' ? 'ok' : 'warn' }}">{{ $risk['state'] }}</span>
              </div>
            </div>
          @endforeach
        </div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
