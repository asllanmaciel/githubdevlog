@php
  $report = App\Support\CommunicationReadiness::report();
@endphp

<x-filament-panels::page>
  <style>
    .comm{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--danger:#ff6b6b;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.9);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 88% 8%,rgba(105,227,154,.16),transparent 34%),rgba(16,23,32,.9)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,58px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.value{font-size:34px;font-weight:950}.label{color:var(--muted);font-size:13px}.grid{display:grid;grid-template-columns:1fr 390px;gap:16px;align-items:start}.check,.step{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:13px;margin-bottom:10px;display:grid;grid-template-columns:auto 1fr;gap:12px;align-items:center}.check.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.dot{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950}.done .dot{background:var(--green);border-color:var(--green);color:#071018}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}@media(max-width:1050px){.grid,.metrics{grid-template-columns:1fr}}
  </style>

  <div class="comm">
    <section class="hero">
      <div class="kicker">E-mail, convites e comunicacao</div>
      <h1 class="title">Sem e-mail confiavel, SaaS nao onboarding.</h1>
      <p class="lead">Este painel mostra se o produto esta pronto para enviar convites de equipe e mensagens transacionais com remetente, URL publica e fallback operacional.</p>
    </section>

    <section class="metrics">
      <div class="metric"><div class="value">{{ $report['percent'] }}%</div><div class="label">prontidao</div></div>
      <div class="metric"><div class="value">{{ $report['metrics']['pending_invites'] }}</div><div class="label">convites pendentes</div></div>
      <div class="metric"><div class="value">{{ $report['metrics']['failed_deliveries'] }}</div><div class="label">falhas de envio</div></div>
      <div class="metric"><div class="value">{{ $report['metrics']['mailer'] }}</div><div class="label">mailer</div></div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Checks</div>
        @foreach ($report['checks'] as $check)
          <div class="check {{ $check['done'] ? 'done' : '' }}">
            <div class="dot">{{ $check['done'] ? 'ok' : '!' }}</div>
            <div><strong>{{ $check['title'] }}</strong><div class="label">{{ $check['detail'] }} · {{ $check['area'] }}</div></div>
          </div>
        @endforeach
      </div>
      <aside class="card">
        <div class="kicker">Proximos passos</div>
        @foreach ($report['next_steps'] as $step)
          <div class="step"><div class="dot">→</div><div>{{ $step }}</div></div>
        @endforeach
        <div class="kicker" style="margin-top:18px">CLI</div>
        <pre class="cmd">php artisan devlog:communication-check
php artisan devlog:communication-check --json</pre>
      </aside>
    </section>
  </div>
</x-filament-panels::page>