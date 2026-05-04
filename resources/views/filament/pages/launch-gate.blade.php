@php
  use App\Support\GoLiveReadiness;
  use App\Support\OverallLaunchReadiness;
  use App\Support\ProductionEnvironment;
  use App\Support\SystemHealth;

  $health = SystemHealth::report();
  $goLive = GoLiveReadiness::report();
  $overall = OverallLaunchReadiness::report();
  $production = ProductionEnvironment::report();
  $externalBlockers = $goLive['external_blockers'];
  $localBlockers = $goLive['local_blockers'];
  $readyLocal = $goLive['local_percent'] >= 95 && $localBlockers->isEmpty();
  $readyPublic = $goLive['ready'] && $health['ok'] && $production['ready'];

  $icons = [
    'rocket' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.5 16.5 3 21l4.5-1.5"></path><path d="M9 15 5 11l6-6c3-3 7-3 8-2 1 1 1 5-2 8l-6 6-4-4"></path><path d="M14 6h.01"></path><path d="m13 11 5 5"></path></svg>',
    'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>',
    'warn' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.3 3.9 2.6 17.5A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.5L13.7 3.9a2 2 0 0 0-3.4 0Z"></path></svg>',
    'server' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v6H4z"></path><path d="M4 14h16v6H4z"></path><path d="M8 7h.01"></path><path d="M8 17h.01"></path></svg>',
    'mail' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v12H4z"></path><path d="m4 7 8 6 8-6"></path></svg>',
    'run' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m8 5 11 7-11 7V5Z"></path><path d="M4 5v14"></path></svg>',
  ];

  $recommendedSteps = [
    ['icon' => 'server', 'title' => 'Fixar dominio e HTTPS', 'detail' => 'Definir APP_URL final e testar rotas publicas.'],
    ['icon' => 'check', 'title' => 'Manter GitHub App real saudavel', 'detail' => 'Callback, webhook, permissoes minimas e secret rotacionavel.'],
    ['icon' => 'check', 'title' => 'Virar billing producao', 'detail' => 'Mercado Pago producao com webhook assinado e teste de baixo valor.'],
    ['icon' => 'mail', 'title' => 'Ativar e-mail e fila', 'detail' => 'Convites, suporte, notificacoes e workers fora de sync.'],
    ['icon' => 'run', 'title' => 'Rodar preflight strict', 'detail' => 'Depois disso, ampliar beta publico ou preparar Marketplace.'],
  ];
@endphp

<x-filament-panels::page>
  <style>
    .gate{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--danger:#ff6b6b;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:22px;background:rgba(16,23,32,.88);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}
    .hero{margin-bottom:16px;position:relative;overflow:hidden;background:radial-gradient(circle at 80% 0%,rgba(80,184,255,.18),transparent 36%),linear-gradient(135deg,rgba(16,23,32,.96),rgba(9,14,20,.9))}
    .hero-head{display:flex;gap:16px;align-items:flex-start}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.7;margin:14px 0 0;max-width:900px}
    .icon{width:46px;height:46px;border-radius:16px;display:grid;place-items:center;border:1px solid rgba(80,184,255,.34);background:rgba(80,184,255,.1);color:#b7e4ff;flex:0 0 auto}.mini-icon{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);background:#071018;color:var(--blue);flex:0 0 auto}.mini-icon.ok{background:var(--green);border-color:var(--green);color:#071018}.mini-icon.bad{background:rgba(255,107,107,.1);border-color:rgba(255,107,107,.45);color:var(--danger)}.mini-icon.warn{background:rgba(255,209,102,.1);border-color:rgba(255,209,102,.45);color:var(--yellow)}.icon svg,.mini-icon svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
    .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:16px}.two{display:grid;grid-template-columns:1fr 420px;gap:16px}.metric-card{display:flex;gap:12px;align-items:flex-start}.metric{font-size:38px;font-weight:950;letter-spacing:-.05em}.muted{color:var(--muted);font-size:13px}.ok{color:var(--green)}.bad{color:var(--danger)}.warn{color:var(--yellow)}
    .status{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--line);border-radius:999px;padding:8px 12px;font-weight:900;margin-right:8px;margin-top:12px}.status.ready{background:rgba(105,227,154,.1);border-color:rgba(105,227,154,.45);color:var(--green)}.status.blocked{background:rgba(255,107,107,.1);border-color:rgba(255,107,107,.45);color:var(--danger)}.status.warn{background:rgba(255,209,102,.1);border-color:rgba(255,209,102,.45);color:var(--yellow)}
    .item{display:flex;gap:12px;align-items:flex-start;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px;margin-bottom:10px}.item.blocker{border-color:rgba(255,107,107,.38);background:rgba(255,107,107,.06)}.item.done{border-color:rgba(105,227,154,.35);background:rgba(105,227,154,.06)}.item strong{display:block;margin-bottom:4px}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}.steps{display:grid;gap:10px}.step{display:grid;grid-template-columns:auto 1fr;gap:12px;align-items:start}.num{width:34px;height:34px;border-radius:12px;background:rgba(80,184,255,.12);border:1px solid rgba(80,184,255,.35);display:grid;place-items:center;color:#b7e4ff;font-weight:950}
    @media(max-width:1100px){.grid,.two{grid-template-columns:1fr}}@media(max-width:720px){.hero-head{display:block}.hero-head .icon{margin-bottom:14px}}
  </style>

  <div class="gate">
    <section class="hero">
      <div class="hero-head">
        <div class="icon">{!! $icons['rocket'] !!}</div>
        <div>
          <div class="kicker">Go-live final</div>
          <h1 class="title">O codigo esta quase la. O lancamento oficial depende do mundo real.</h1>
          <p class="lead">Este gate separa o que ja foi resolvido no produto do que precisa ser ativado fora do codigo: dominio, GitHub App oficial, Mercado Pago producao, e-mail e worker. E a tela para decidir com seguranca quando abrir para devs externos.</p>
          <span class="status {{ $readyLocal ? 'ready' : 'warn' }}">Local: {{ $readyLocal ? 'pronto' : 'revisar' }}</span>
          <span class="status {{ $readyPublic ? 'ready' : 'blocked' }}">Publico: {{ $readyPublic ? 'liberado' : 'bloqueado' }}</span>
        </div>
      </div>
    </section>

    <section class="grid">
      <div class="card metric-card"><div class="mini-icon {{ $overall['percent'] >= 90 ? 'ok' : 'warn' }}">{!! $overall['percent'] >= 90 ? $icons['check'] : $icons['warn'] !!}</div><div><div class="kicker">Launch geral</div><div class="metric {{ $overall['percent'] >= 90 ? 'ok' : 'warn' }}">{{ $overall['percent'] }}%</div><div class="muted">Soma de beta, go-live, GitHub Program, evidencias e roadmap.</div></div></div>
      <div class="card metric-card"><div class="mini-icon {{ $goLive['local_percent'] >= 95 ? 'ok' : 'warn' }}">{!! $goLive['local_percent'] >= 95 ? $icons['check'] : $icons['warn'] !!}</div><div><div class="kicker">Local</div><div class="metric {{ $goLive['local_percent'] >= 95 ? 'ok' : 'warn' }}">{{ $goLive['local_percent'] }}%</div><div class="muted">Itens que dependem so do app e configuracao local.</div></div></div>
      <div class="card metric-card"><div class="mini-icon {{ $goLive['external_percent'] >= 100 ? 'ok' : 'bad' }}">{!! $goLive['external_percent'] >= 100 ? $icons['check'] : $icons['warn'] !!}</div><div><div class="kicker">Externo</div><div class="metric {{ $goLive['external_percent'] >= 100 ? 'ok' : 'bad' }}">{{ $goLive['external_percent'] }}%</div><div class="muted">Infra, credenciais e servicos finais.</div></div></div>
      <div class="card metric-card"><div class="mini-icon {{ $health['ok'] ? 'ok' : 'bad' }}">{!! $health['ok'] ? $icons['check'] : $icons['warn'] !!}</div><div><div class="kicker">Saude</div><div class="metric {{ $health['ok'] ? 'ok' : 'bad' }}">{{ $health['ok'] ? 'OK' : '!' }}</div><div class="muted">Banco, cache, rotas e storage.</div></div></div>
    </section>

    <section class="two">
      <div class="card">
        <div class="kicker">Bloqueadores externos para lancamento oficial</div>
        @forelse ($externalBlockers as $blocker)
          <div class="item blocker"><div class="mini-icon bad">{!! $icons['warn'] !!}</div><div><strong>{{ $blocker['title'] }}</strong><span class="muted">{{ $blocker['nextAction'] }}</span><br><span class="muted">{{ $blocker['detail'] }}</span></div></div>
        @empty
          <div class="item done"><div class="mini-icon ok">{!! $icons['check'] !!}</div><div><strong>Nenhum bloqueador externo ativo</strong><span class="muted">O produto pode avancar para beta publico controlado e preparacao de Marketplace.</span></div></div>
        @endforelse

        <div class="kicker" style="margin-top:18px">Pendencias locais</div>
        @forelse ($localBlockers as $blocker)
          <div class="item blocker"><div class="mini-icon bad">{!! $icons['warn'] !!}</div><div><strong>{{ $blocker['title'] }}</strong><span class="muted">{{ $blocker['nextAction'] }}</span></div></div>
        @empty
          <div class="item done"><div class="mini-icon ok">{!! $icons['check'] !!}</div><div><strong>Sem pendencias locais criticas</strong><span class="muted">O restante e ativacao operacional, revisao final e divulgacao gradual.</span></div></div>
        @endforelse
      </div>

      <aside class="card">
        <div class="kicker">Ordem recomendada</div>
        <div class="steps">
          @foreach ($recommendedSteps as $index => $step)
            <div class="step">
              <div class="num">{{ $index + 1 }}</div>
              <div>
                <div class="mini-icon" style="margin-bottom:8px">{!! $icons[$step['icon']] !!}</div>
                <strong>{{ $step['title'] }}</strong>
                <div class="muted">{{ $step['detail'] }}</div>
              </div>
            </div>
          @endforeach
        </div>
        <pre class="cmd" style="margin-top:14px">php artisan devlog:go-live-check --json</pre>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
