@php
  $report = App\Support\WebhookRetention::report();
@endphp

<x-filament-panels::page>
  <style>
    .retention{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 90% 12%,rgba(80,184,255,.16),transparent 34%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,60px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}.metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.value{font-size:36px;font-weight:950;letter-spacing:-.04em}.label{color:var(--muted);font-size:13px}.grid{display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start}.row{display:grid;grid-template-columns:1.1fr .8fr .5fr .5fr .5fr;gap:10px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px;margin-bottom:10px}.row.alert{border-color:rgba(255,209,102,.42);background:rgba(255,209,102,.07)}.pill{display:inline-flex;border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}@media(max-width:1100px){.grid,.metrics{grid-template-columns:1fr}.row{grid-template-columns:1fr}.row strong{display:block}}
  </style>

  <div class="retention">
    <section class="hero">
      <div class="kicker">Privacidade e custo operacional</div>
      <h1 class="title">Retencao de payloads sob controle.</h1>
      <p class="lead">Webhooks podem conter dados sensiveis. Esta tela mostra quanto tempo cada workspace preserva payloads, quantos eventos ja venceram a janela do plano e qual comando executa a limpeza segura.</p>
    </section>

    <section class="metrics">
      <div class="metric"><div class="value">{{ $report['total_events'] }}</div><div class="label">eventos armazenados</div></div>
      <div class="metric"><div class="value">{{ $report['expired_events'] }}</div><div class="label">eventos fora da retencao</div></div>
      <div class="metric"><div class="value">{{ $report['plans']->count() }}</div><div class="label">planos com regra de retencao</div></div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Workspaces</div>
        @forelse ($report['rows'] as $row)
          <div class="row {{ $row['expired_events'] > 0 ? 'alert' : '' }}">
            <div><strong>{{ $row['workspace'] }}</strong><div class="label">{{ $row['plan'] }}</div></div>
            <div><span class="pill">{{ $row['retention_days'] }} dias</span></div>
            <div><strong>{{ $row['total_events'] }}</strong><div class="label">total</div></div>
            <div><strong>{{ $row['expired_events'] }}</strong><div class="label">vencidos</div></div>
            <div><div class="label">corte</div><strong>{{ $row['cutoff']->format('d/m/Y') }}</strong></div>
          </div>
        @empty
          <p class="label">Nenhum workspace encontrado.</p>
        @endforelse
      </div>

      <aside class="card">
        <div class="kicker">Comandos</div>
        <pre class="cmd">php artisan devlog:prune-webhook-events --dry-run
php artisan devlog:prune-webhook-events
php artisan devlog:prune-webhook-events --json</pre>
        <div class="label" style="line-height:1.65">A rotina agendada executa diariamente as 02:30. Use dry-run antes de qualquer limpeza manual.</div>

        <div class="kicker" style="margin-top:18px">Planos</div>
        @foreach ($report['plans'] as $plan)
          <div class="row" style="grid-template-columns:1fr auto">
            <strong>{{ $plan->name }}</strong>
            <span class="pill">{{ $plan->event_retention_days ?: 30 }} dias</span>
          </div>
        @endforeach
      </aside>
    </section>
  </div>
</x-filament-panels::page>