@php
  $report = App\Support\AiProductReadiness::report();
  $usage = $report['usage'];
@endphp

<x-filament-panels::page>
  <style>
    .ai{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;--red:#ff6b6b;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:20px;background:rgba(16,23,32,.92);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 78% 0,rgba(80,184,255,.22),transparent 34%),radial-gradient(circle at 8% 10%,rgba(105,227,154,.18),transparent 30%),rgba(16,23,32,.92)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(36px,5vw,66px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.68;margin:14px 0 0;max-width:900px}
    .score{font-size:72px;font-weight:950;letter-spacing:-.08em}.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}.metric,.check{border:1px solid var(--line);border-radius:18px;background:#0b1118;padding:16px}.value{font-size:34px;font-weight:950;letter-spacing:-.05em}.label{color:var(--muted);font-size:13px}.grid{display:grid;grid-template-columns:1fr 420px;gap:16px;align-items:start}.check{display:grid;grid-template-columns:auto 1fr;gap:12px;margin-bottom:10px}.check.done{border-color:rgba(105,227,154,.42);background:rgba(105,227,154,.06)}.mark{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);color:var(--yellow);font-weight:950}.done .mark{background:var(--green);border-color:var(--green);color:#071018}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;display:inline-flex}.bar{height:12px;border-radius:999px;background:#071018;border:1px solid var(--line);overflow:hidden;margin-top:16px}.fill{height:100%;background:linear-gradient(90deg,var(--blue),var(--green))}
    @media(max-width:1100px){.metrics,.grid{grid-template-columns:1fr}}
  </style>

  <div class="ai">
    <section class="hero">
      <div class="kicker">AI dentro do produto</div>
      <div class="score">{{ $report['percent'] }}%</div>
      <h1 class="title">O DevLog AI agora tem inteligência aplicada ao webhook.</h1>
      <p class="lead">Este painel acompanha a maturidade da camada AI: persistência, provider local, ação no dashboard, auditoria, documentação e cobertura de eventos analisados.</p>
      <div class="bar"><div class="fill" style="width: {{ $report['percent'] }}%"></div></div>
    </section>

    <section class="metrics">
      <div class="metric"><div class="value">{{ $usage['total_events'] }}</div><div class="label">eventos no ambiente</div></div>
      <div class="metric"><div class="value">{{ $usage['analyzed_events'] }}</div><div class="label">eventos com análise AI</div></div>
      <div class="metric"><div class="value">{{ $usage['coverage'] }}%</div><div class="label">cobertura de análise</div></div>
      <div class="metric"><div class="value">{{ $report['ready'] ? 'sim' : 'não' }}</div><div class="label">camada AI estrutural pronta</div></div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Checklist AI</div>
        @foreach ($report['checks'] as $check)
          <div class="check {{ $check['done'] ? 'done' : '' }}">
            <div class="mark">{{ $check['done'] ? 'ok' : '!' }}</div>
            <div>
              <strong>{{ $check['title'] }}</strong>
              <div class="label">{{ $check['detail'] }}</div>
            </div>
          </div>
        @endforeach
      </div>

      <aside class="card">
        <div class="kicker">Próximas evoluções</div>
        @foreach ($report['next_steps'] as $step)
          <div class="check">
            <div class="mark">→</div>
            <div class="label">{{ $step }}</div>
          </div>
        @endforeach
        <div class="kicker" style="margin-top:16px">Distribuição de risco</div>
        @forelse ($usage['risk_distribution'] as $risk => $total)
          <div class="d-flex justify-content-between gap-2 mb-2"><span class="pill">{{ $risk }}</span><strong>{{ $total }}</strong></div>
        @empty
          <div class="label">Ainda não há eventos analisados para distribuir por risco.</div>
        @endforelse
      </aside>
    </section>
  </div>
</x-filament-panels::page>