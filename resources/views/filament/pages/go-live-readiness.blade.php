@php
  $report = App\Support\GoLiveReadiness::report();
@endphp

<x-filament-panels::page>
  <style>
    .golive{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:20px;background:rgba(16,23,32,.92);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 86% 10%,rgba(105,227,154,.18),transparent 32%),radial-gradient(circle at 12% 0,rgba(80,184,255,.2),transparent 30%),rgba(16,23,32,.92)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(36px,5vw,64px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.68;margin:14px 0 0;max-width:920px}
    .metrics{display:grid;grid-template-columns:1.2fr repeat(3,1fr);gap:12px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:18px;background:#0b1118;padding:16px;position:relative;overflow:hidden}.metric:after{content:"";position:absolute;inset:auto -20px -35px auto;width:100px;height:100px;border-radius:50%;background:rgba(80,184,255,.08)}
    .value{font-size:36px;font-weight:950;letter-spacing:-.04em}.label{color:var(--muted);font-size:13px}.grid{display:grid;grid-template-columns:1fr 420px;gap:16px;align-items:start}
    .check{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:15px;margin-bottom:10px;display:grid;grid-template-columns:auto 1fr auto;gap:13px;align-items:start}.check.done{border-color:rgba(105,227,154,.42);background:linear-gradient(90deg,rgba(105,227,154,.09),#0b1118)}
    .mark{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950;color:var(--yellow)}.done .mark{background:var(--green);border-color:var(--green);color:#071018}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;white-space:nowrap}.action{margin-top:7px;color:#b7e4ff;font-size:13px}
    .bar{height:12px;border-radius:999px;background:#071018;border:1px solid var(--line);overflow:hidden;margin-top:12px}.fill{height:100%;background:linear-gradient(90deg,var(--blue),var(--green));width:{{ $report['percent'] }}%}.blocker{border:1px solid rgba(255,209,102,.32);background:rgba(255,209,102,.06);border-radius:16px;padding:14px;margin-bottom:10px}
    @media(max-width:1100px){.grid,.metrics{grid-template-columns:1fr}}
  </style>

  <div class="golive">
    <section class="hero">
      <div class="kicker">Launch oficial</div>
      <h1 class="title">Painel de decisao para colocar o DevLog AI no ar.</h1>
      <p class="lead">Aqui ficam os criterios que separam um beta funcional de uma operacao SaaS vendavel: dominio, GitHub App, pagamentos, e-mail, fila, suporte, documentacao e confianca.</p>
      <div class="bar"><div class="fill"></div></div>
    </section>

    <section class="metrics">
      <div class="metric"><div class="value">{{ $report['percent'] }}%</div><div class="label">prontidao para go-live</div></div>
      <div class="metric"><div class="value">{{ $report['summary']['done'] }}/{{ $report['summary']['total'] }}</div><div class="label">criterios atendidos</div></div>
      <div class="metric"><div class="value">{{ $report['summary']['blockers'] }}</div><div class="label">bloqueadores atuais</div></div>
      <div class="metric"><div class="value">{{ $report['ready'] ? 'sim' : 'nao' }}</div><div class="label">liberado para lancar</div></div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Checklist executivo</div>
        @foreach ($report['checks'] as $check)
          <div class="check {{ $check['done'] ? 'done' : '' }}">
            <div class="mark">{{ $check['done'] ? 'ok' : '!' }}</div>
            <div>
              <strong>{{ $check['title'] }}</strong>
              <div class="label">{{ $check['detail'] }}</div>
              <div class="action">Proximo passo: {{ $check['nextAction'] }}</div>
            </div>
            <span class="pill">{{ $check['area'] }}</span>
          </div>
        @endforeach
      </div>

      <aside class="card">
        <div class="kicker">Bloqueadores de lancamento</div>
        @forelse ($report['blockers'] as $blocker)
          <div class="blocker">
            <strong>{{ $blocker['title'] }}</strong>
            <div class="label">{{ $blocker['detail'] }}</div>
            <div class="action">{{ $blocker['nextAction'] }}</div>
          </div>
        @empty
          <div class="check done">
            <div class="mark">ok</div>
            <div><strong>Nenhum bloqueador critico</strong><div class="label">O produto esta apto para preparar release publico.</div></div>
            <span class="pill">Launch</span>
          </div>
        @endforelse
      </aside>
    </section>
  </div>
</x-filament-panels::page>
