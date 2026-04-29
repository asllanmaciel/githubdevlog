@php
  $report = App\Support\IncidentResponse::report();
@endphp

<x-filament-panels::page>
  <style>
    .incident{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--danger:#ff6b6b;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 88% 12%,rgba(255,209,102,.14),transparent 34%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,60px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}.metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.value{font-size:34px;font-weight:950}.label{color:var(--muted);font-size:13px}.grid{display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start}.check,.incident-row{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px;margin-bottom:10px;display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center}.check.done{border-color:rgba(105,227,154,.35);background:rgba(105,227,154,.06)}.dot{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950}.done .dot{background:var(--green);border-color:var(--green);color:#071018}.danger{color:var(--danger)}.warn{color:var(--yellow)}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}@media(max-width:1050px){.grid,.metrics,.check,.incident-row{grid-template-columns:1fr}}
  </style>

  <div class="incident">
    <section class="hero">
      <div class="kicker">Operacao em tempo de lancamento</div>
      <h1 class="title">Antes do usuario reclamar, o admin enxerga.</h1>
      <p class="lead">Resumo de filas, billing, webhooks invalidos, suporte e acoes sensiveis. Este painel e o primeiro lugar para olhar durante demo, beta e lancamento.</p>
    </section>

    <section class="metrics">
      <div class="metric"><div class="value">{{ $report['healthy'] ? 'OK' : '!' }}</div><div class="label">status operacional</div></div>
      <div class="metric"><div class="value">{{ $report['incidents']->count() }}</div><div class="label">incidentes ativos</div></div>
      <div class="metric"><div class="value">{{ $report['metrics']['open_tickets'] }}</div><div class="label">chamados abertos</div></div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Checks</div>
        @foreach ($report['checks'] as $check)
          <div class="check {{ $check['done'] ? 'done' : '' }}">
            <div class="dot">{{ $check['done'] ? 'ok' : '!' }}</div>
            <div><strong>{{ $check['title'] }}</strong><div class="label">{{ $check['detail'] }}</div></div>
            <span class="pill">{{ $check['area'] }}</span>
          </div>
        @endforeach
      </div>

      <aside class="card">
        <div class="kicker">Incidentes</div>
        @forelse ($report['incidents'] as $incident)
          <div class="incident-row">
            <div class="dot">!</div>
            <div><strong>{{ $incident['title'] }}</strong><div class="label">{{ $incident['detail'] }}</div><code>{{ $incident['command'] }}</code></div>
            <span class="pill">{{ $incident['severity'] }}</span>
          </div>
        @empty
          <p class="label">Nenhum incidente ativo pelos criterios atuais.</p>
        @endforelse

        <div class="kicker" style="margin-top:18px">CLI</div>
        <pre class="cmd">php artisan devlog:incident-check
php artisan devlog:incident-check --json</pre>
      </aside>
    </section>
  </div>
</x-filament-panels::page>