@php
  $report = \App\Support\OperationsCenter::report();

  $icons = [
    'ops' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2v4"></path><path d="M12 18v4"></path><path d="m4.9 4.9 2.8 2.8"></path><path d="m16.3 16.3 2.8 2.8"></path><path d="M2 12h4"></path><path d="M18 12h4"></path><path d="m4.9 19.1 2.8-2.8"></path><path d="m16.3 7.7 2.8-2.8"></path><path d="M9 12a3 3 0 1 0 6 0 3 3 0 0 0-6 0Z"></path></svg>',
    'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>',
    'warn' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.3 3.9 2.6 17.5A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.5L13.7 3.9a2 2 0 0 0-3.4 0Z"></path></svg>',
    'queue' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"></path><path d="M4 12h16"></path><path d="M4 17h10"></path></svg>',
    'log' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4z"></path><path d="M8 8h8"></path><path d="M8 12h8"></path><path d="M8 16h5"></path></svg>',
    'cache' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7c0-2 4-4 8-4s8 2 8 4-4 4-8 4-8-2-8-4Z"></path><path d="M4 7v5c0 2 4 4 8 4s8-2 8-4V7"></path><path d="M4 12v5c0 2 4 4 8 4s8-2 8-4v-5"></path></svg>',
  ];
@endphp

<x-filament-panels::page>
  <style>
    .ops{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;--red:#ff6b6b;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:20px;background:rgba(16,23,32,.92);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 90% 10%,rgba(80,184,255,.18),transparent 34%),rgba(16,23,32,.92)}
    .hero-head{display:flex;gap:16px;align-items:flex-start}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}
    .title{font-size:clamp(34px,5vw,60px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}
    .lead{color:var(--muted);font-size:16px;line-height:1.68;margin:14px 0 0;max-width:920px}
    .icon{width:46px;height:46px;border-radius:16px;display:grid;place-items:center;border:1px solid rgba(80,184,255,.34);background:rgba(80,184,255,.1);color:#b7e4ff;flex:0 0 auto}.mini-icon{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);background:#071018;color:var(--blue);flex:0 0 auto}.mini-icon.ok{background:var(--green);border-color:var(--green);color:#071018}.mini-icon.warn{background:rgba(255,209,102,.1);border-color:rgba(255,209,102,.45);color:var(--yellow)}.mini-icon.danger{background:rgba(255,107,107,.1);border-color:rgba(255,107,107,.45);color:var(--red)}.icon svg,.mini-icon svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
    .grid{display:grid;grid-template-columns:1.1fr .9fr;gap:16px;margin-bottom:16px}.mini{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
    .rowx{display:flex;gap:12px;align-items:flex-start;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px;margin-bottom:10px}.rowx strong{display:block}
    .label{color:var(--muted);font-size:13px;line-height:1.55}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;display:inline-flex;margin-top:8px}
    .ok{color:var(--green)}.warn{color:var(--yellow)}.danger{color:var(--red)}
    .log{max-height:360px;overflow:auto;background:#050a10;border:1px solid var(--line);border-radius:14px;padding:14px;color:#b7e4ff;font-size:12px;white-space:pre-wrap}
    .actions{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}.action{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px;text-align:left}
    .action button{width:100%;border:0;border-radius:10px;background:linear-gradient(135deg,var(--blue),var(--green));color:#061018;font-weight:950;padding:10px;margin-top:10px}
    @media(max-width:1100px){.grid,.mini,.actions{grid-template-columns:1fr}}@media(max-width:720px){.hero-head{display:block}.hero-head .icon{margin-bottom:14px}}
  </style>

  <div class="ops">
    <section class="hero">
      <div class="hero-head">
        <div class="icon">{!! $icons['ops'] !!}</div>
        <div>
          <div class="kicker">Admin / Operacao</div>
          <h1 class="title">Centro de operacao do SaaS.</h1>
          <p class="lead">Ferramentas rapidas para acompanhar erros, migrations, filas, ambiente e caches sem entrar no terminal. Valores sensiveis nao sao exibidos.</p>
        </div>
      </div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Ambiente</div>
        <div class="mini">
          @foreach ($report['environment'] as $item)
            @php $isOk = $item['state'] === 'ok'; @endphp
            <div class="rowx">
              <div class="mini-icon {{ $isOk ? 'ok' : 'warn' }}">{!! $isOk ? $icons['check'] : $icons['warn'] !!}</div>
              <div>
                <strong>{{ $item['label'] }}</strong>
                <div class="label">{{ $item['value'] }}</div>
                <span class="pill {{ $isOk ? 'ok' : 'warn' }}">{{ $item['state'] }}</span>
              </div>
            </div>
          @endforeach
        </div>
      </div>

      <aside class="card">
        <div class="kicker">Filas</div>
        <div class="rowx"><div class="mini-icon">{!! $icons['queue'] !!}</div><div><strong>{{ $report['queues']['jobs'] ?? 'n/a' }}</strong><div class="label">jobs pendentes</div></div></div>
        <div class="rowx"><div class="mini-icon {{ ($report['queues']['failed_jobs'] ?? 0) > 0 ? 'danger' : 'ok' }}">{!! ($report['queues']['failed_jobs'] ?? 0) > 0 ? $icons['warn'] : $icons['check'] !!}</div><div><strong class="{{ ($report['queues']['failed_jobs'] ?? 0) > 0 ? 'danger' : 'ok' }}">{{ $report['queues']['failed_jobs'] ?? 'n/a' }}</strong><div class="label">jobs com falha</div></div></div>
        <div class="rowx"><div class="mini-icon">{!! $icons['queue'] !!}</div><div><strong>{{ $report['queues']['batches'] ?? 'n/a' }}</strong><div class="label">batches registrados</div></div></div>
      </aside>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Erros recentes</div>
        @if (count($report['logs']['recent_errors']) > 0)
          <div class="log">{{ implode("\n", $report['logs']['recent_errors']) }}</div>
        @else
          <div class="rowx"><div class="mini-icon ok">{!! $icons['check'] !!}</div><div><strong class="ok">Nenhum erro recente encontrado.</strong><div class="label">{{ $report['logs']['path'] }}</div></div></div>
        @endif
        <div class="label" style="margin-top:10px">Atualizado em: {{ $report['logs']['updated_at'] ?? 'sem log' }} - tamanho: {{ number_format(($report['logs']['size'] ?? 0) / 1024, 1, ',', '.') }} KB</div>
      </div>

      <aside class="card">
        <div class="kicker">Migrations</div>
        <div class="rowx"><div class="mini-icon ok">{!! $icons['check'] !!}</div><div><strong>{{ $report['migrations']['total'] }}</strong><div class="label">migrations executadas - batch {{ $report['migrations']['latest_batch'] ?? 'n/a' }}</div></div></div>
        @foreach ($report['migrations']['latest'] as $migration)
          <div class="rowx"><div class="mini-icon">{!! $icons['log'] !!}</div><div><strong>{{ $migration['migration'] ?? $migration['name'] }}</strong><div class="label">batch {{ $migration['batch'] }}</div></div></div>
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
              <div class="rowx" style="margin-bottom:0"><div class="mini-icon">{!! $icons['cache'] !!}</div><div><strong>{{ $command['label'] }}</strong><div class="label"><code>{{ $command['command'] }}</code></div></div></div>
              <button type="button" wire:click="{{ $command['method'] }}">Executar</button>
            </div>
          @endforeach
        </div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
