@php
  $report = \App\Support\OperationsCenter::report();
@endphp

<x-filament-panels::page>
  <style>
    .ops{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;--red:#ff6b6b;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:20px;background:rgba(16,23,32,.92);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 90% 10%,rgba(80,184,255,.18),transparent 34%),rgba(16,23,32,.92)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}
    .title{font-size:clamp(34px,5vw,60px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}
    .lead{color:var(--muted);font-size:16px;line-height:1.68;margin:14px 0 0;max-width:920px}
    .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px;margin-bottom:16px}.mini{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
    .rowx{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px;margin-bottom:10px}.rowx strong{display:block}
    .label{color:var(--muted);font-size:13px;line-height:1.55}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;display:inline-flex}
    .ok{color:var(--green)}.warn{color:var(--yellow)}.danger{color:var(--red)}
    .log{max-height:360px;overflow:auto;background:#050a10;border:1px solid var(--line);border-radius:14px;padding:14px;color:#b7e4ff;font-size:12px;white-space:pre-wrap}
    .actions{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}.action{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px;text-align:left}
    .action button{width:100%;border:0;border-radius:10px;background:linear-gradient(135deg,var(--blue),var(--green));color:#061018;font-weight:950;padding:10px;margin-top:10px}
    @media(max-width:1100px){.grid,.mini,.actions{grid-template-columns:1fr}}
  </style>

  <div class="ops">
    <section class="hero">
      <div class="kicker">Admin / Operação</div>
      <h1 class="title">Centro de operação do SaaS.</h1>
      <p class="lead">Ferramentas rápidas para acompanhar erros, migrations, filas, ambiente e caches sem entrar no terminal. Valores sensíveis não são exibidos.</p>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Ambiente</div>
        <div class="mini">
          @foreach ($report['environment'] as $item)
            <div class="rowx">
              <strong>{{ $item['label'] }}</strong>
              <div class="label">{{ $item['value'] }}</div>
              <span class="pill {{ $item['state'] === 'ok' ? 'ok' : ($item['state'] === 'atenção' ? 'warn' : '') }}">{{ $item['state'] }}</span>
            </div>
          @endforeach
        </div>
      </div>

      <aside class="card">
        <div class="kicker">Filas</div>
        <div class="rowx"><strong>{{ $report['queues']['jobs'] ?? 'n/a' }}</strong><div class="label">jobs pendentes</div></div>
        <div class="rowx"><strong class="{{ ($report['queues']['failed_jobs'] ?? 0) > 0 ? 'danger' : 'ok' }}">{{ $report['queues']['failed_jobs'] ?? 'n/a' }}</strong><div class="label">jobs com falha</div></div>
        <div class="rowx"><strong>{{ $report['queues']['batches'] ?? 'n/a' }}</strong><div class="label">batches registrados</div></div>
      </aside>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Erros recentes</div>
        @if (count($report['logs']['recent_errors']) > 0)
          <div class="log">{{ implode("\n", $report['logs']['recent_errors']) }}</div>
        @else
          <div class="rowx"><strong class="ok">Nenhum erro recente encontrado.</strong><div class="label">{{ $report['logs']['path'] }}</div></div>
        @endif
        <div class="label" style="margin-top:10px">Atualizado em: {{ $report['logs']['updated_at'] ?? 'sem log' }} · tamanho: {{ number_format(($report['logs']['size'] ?? 0) / 1024, 1, ',', '.') }} KB</div>
      </div>

      <aside class="card">
        <div class="kicker">Migrations</div>
        <div class="rowx"><strong>{{ $report['migrations']['total'] }}</strong><div class="label">migrations executadas · batch {{ $report['migrations']['latest_batch'] ?? 'n/a' }}</div></div>
        @foreach ($report['migrations']['latest'] as $migration)
          <div class="rowx"><strong>{{ $migration['migration'] ?? $migration['name'] }}</strong><div class="label">batch {{ $migration['batch'] }}</div></div>
        @endforeach
      </aside>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Tail do log</div>
        <div class="log">{{ implode("\n", $report['logs']['tail']) ?: 'Log vazio.' }}</div>
      </div>

      <aside class="card">
        <div class="kicker">Cache e bootstrap</div>
        <div class="actions">
          @foreach ($report['cache_commands'] as $command)
            <div class="action">
              <strong>{{ $command['label'] }}</strong>
              <div class="label"><code>{{ $command['command'] }}</code></div>
              <button type="button" wire:click="{{ $command['method'] }}">Executar</button>
            </div>
          @endforeach
        </div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
