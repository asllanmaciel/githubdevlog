@php
  $report = App\Support\GitHubProgramReadiness::report();
@endphp

<x-filament-panels::page>
  <style>
    .ghp{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:20px;background:rgba(16,23,32,.92);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 92% 8%,rgba(80,184,255,.18),transparent 34%),radial-gradient(circle at 8% 10%,rgba(105,227,154,.14),transparent 28%),rgba(16,23,32,.92)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(36px,5vw,62px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.68;margin:14px 0 0;max-width:940px}
    .metrics{display:grid;grid-template-columns:1.3fr repeat(2,1fr);gap:12px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:18px;background:#0b1118;padding:16px}.value{font-size:36px;font-weight:950;letter-spacing:-.04em}.label{color:var(--muted);font-size:13px}
    .grid{display:grid;grid-template-columns:1fr 430px;gap:16px;align-items:start}.rowx{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:15px;margin-bottom:10px;display:grid;grid-template-columns:auto 1fr auto;gap:13px;align-items:start}.rowx.done{border-color:rgba(105,227,154,.42);background:linear-gradient(90deg,rgba(105,227,154,.09),#0b1118)}
    .mark{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950;color:var(--yellow)}.done .mark{background:var(--green);border-color:var(--green);color:#071018}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;white-space:nowrap}
    .evidence{border:1px solid rgba(80,184,255,.28);background:rgba(80,184,255,.06);border-radius:16px;padding:14px;margin-bottom:10px}.bar{height:12px;border-radius:999px;background:#071018;border:1px solid var(--line);overflow:hidden;margin-top:12px}.fill{height:100%;background:linear-gradient(90deg,var(--blue),var(--green));width:{{ $report['percent'] }}%}
    @media(max-width:1100px){.grid,.metrics{grid-template-columns:1fr}}
  </style>

  <div class="ghp">
    <section class="hero">
      <div class="kicker">GitHub Developer Program</div>
      <h1 class="title">Pacote de submissao para apresentar o DevLog AI ao ecossistema GitHub.</h1>
      <p class="lead">Este painel organiza a narrativa e as evidencias que precisamos para defender o produto: dor real, integracao com GitHub, seguranca, isolamento, demo funcional, suporte e modelo SaaS.</p>
      <div class="bar"><div class="fill"></div></div>
    </section>

    <section class="metrics">
      <div class="metric"><div class="value">{{ $report['percent'] }}%</div><div class="label">prontidao da submissao</div></div>
      <div class="metric"><div class="value">{{ $report['summary']['done'] }}/{{ $report['summary']['total'] }}</div><div class="label">criterios cobertos</div></div>
      <div class="metric"><div class="value">{{ $report['summary']['missing'] }}</div><div class="label">lacunas atuais</div></div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Criterios de produto</div>
        @foreach ($report['checks'] as $check)
          <div class="rowx {{ $check['done'] ? 'done' : '' }}">
            <div class="mark">{{ $check['done'] ? 'ok' : '!' }}</div>
            <div><strong>{{ $check['title'] }}</strong><div class="label">{{ $check['detail'] }}</div></div>
            <span class="pill">{{ $check['area'] }}</span>
          </div>
        @endforeach
      </div>

      <aside class="card">
        <div class="kicker">Evidencias para preparar</div>
        @foreach ($report['evidence'] as $item)
          <div class="evidence">
            <strong>{{ $item['title'] }}</strong>
            <div class="label">{{ $item['detail'] }}</div>
          </div>
        @endforeach
      </aside>
    </section>
  </div>
</x-filament-panels::page>
