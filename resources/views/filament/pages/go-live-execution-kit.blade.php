@php
  $report = App\Support\GoLiveExecutionKit::report();
@endphp

<x-filament-panels::page>
  <style>
    .kit{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:20px;background:rgba(16,23,32,.92);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 78% 0,rgba(105,227,154,.2),transparent 32%),radial-gradient(circle at 10% 10%,rgba(80,184,255,.2),transparent 32%),rgba(16,23,32,.92)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(36px,5vw,66px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.68;margin:14px 0 0;max-width:920px}.score{font-size:70px;font-weight:950;letter-spacing:-.08em}.grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;display:inline-flex}.pill.pronto{color:var(--green);border-color:rgba(105,227,154,.45)}.pill.externo,.pill.pendente{color:var(--yellow);border-color:rgba(255,209,102,.45)}.steps{margin:14px 0 0;padding-left:18px;color:var(--muted);line-height:1.7}.steps li{margin-bottom:7px}.bar{height:12px;border-radius:999px;background:#071018;border:1px solid var(--line);overflow:hidden;margin-top:16px}.fill{height:100%;background:linear-gradient(90deg,var(--blue),var(--green))}
    @media(max-width:1000px){.grid{grid-template-columns:1fr}}
  </style>

  <div class="kit">
    <section class="hero">
      <div class="kicker">Execução final</div>
      <div class="score">{{ $report['percent'] }}%</div>
      <h1 class="title">Kit operacional para tirar o DevLog AI do ambiente local.</h1>
      <p class="lead">O roadmap local está praticamente fechado. Este painel organiza o que o super admin precisa executar no ambiente real: domínio, GitHub App, Mercado Pago produção e screenshots finais.</p>
      <div class="bar"><div class="fill" style="width: {{ $report['percent'] }}%"></div></div>
    </section>

    <section class="grid">
      @foreach ($report['groups'] as $group)
        <article class="card">
          <div class="d-flex justify-content-between gap-2 align-items-start">
            <div>
              <div class="kicker">{{ $group['title'] }}</div>
              <h2 class="h4">{{ $group['objective'] }}</h2>
            </div>
            <span class="pill {{ $group['status'] }}">{{ $group['status'] }}</span>
          </div>
          <ol class="steps">
            @foreach ($group['steps'] as $step)
              <li>{{ $step }}</li>
            @endforeach
          </ol>
        </article>
      @endforeach
    </section>
  </div>
</x-filament-panels::page>