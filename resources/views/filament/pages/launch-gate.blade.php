@php
  use App\Support\GoLiveReadiness;
  use App\Support\OverallLaunchReadiness;
  use App\Support\ProductionEnvironment;
  use App\Support\SecurityPosture;
  use App\Support\SystemHealth;

  $health = SystemHealth::report();
  $security = SecurityPosture::report();
  $goLive = GoLiveReadiness::report();
  $overall = OverallLaunchReadiness::report();
  $production = ProductionEnvironment::report();
  $externalBlockers = $goLive['external_blockers'];
  $localBlockers = $goLive['local_blockers'];
  $readyLocal = $goLive['local_percent'] >= 95 && $localBlockers->isEmpty();
  $readyPublic = $goLive['ready'] && $health['ok'] && $production['ready'];
@endphp

<x-filament-panels::page>
  <style>
    .gate{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--danger:#ff6b6b;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:22px;background:rgba(16,23,32,.88);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}
    .hero{margin-bottom:16px;position:relative;overflow:hidden;background:radial-gradient(circle at 80% 0%,rgba(80,184,255,.18),transparent 36%),linear-gradient(135deg,rgba(16,23,32,.96),rgba(9,14,20,.9))}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.7;margin:14px 0 0;max-width:900px}
    .grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:16px}.two{display:grid;grid-template-columns:1fr 420px;gap:16px}.metric{font-size:38px;font-weight:950;letter-spacing:-.05em}.muted{color:var(--muted);font-size:13px}.ok{color:var(--green)}.bad{color:var(--danger)}.warn{color:var(--yellow)}
    .status{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--line);border-radius:999px;padding:8px 12px;font-weight:900;margin-right:8px;margin-top:12px}.status.ready{background:rgba(105,227,154,.1);border-color:rgba(105,227,154,.45);color:var(--green)}.status.blocked{background:rgba(255,107,107,.1);border-color:rgba(255,107,107,.45);color:var(--danger)}.status.warn{background:rgba(255,209,102,.1);border-color:rgba(255,209,102,.45);color:var(--yellow)}
    .item{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px;margin-bottom:10px}.item.blocker{border-color:rgba(255,107,107,.38);background:rgba(255,107,107,.06)}.item.done{border-color:rgba(105,227,154,.35);background:rgba(105,227,154,.06)}.item strong{display:block;margin-bottom:4px}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}.steps{display:grid;gap:10px}.step{display:grid;grid-template-columns:auto 1fr;gap:12px;align-items:start}.num{width:34px;height:34px;border-radius:12px;background:rgba(80,184,255,.12);border:1px solid rgba(80,184,255,.35);display:grid;place-items:center;color:#b7e4ff;font-weight:950}@media(max-width:1100px){.grid,.two{grid-template-columns:1fr}}
  </style>

  <div class="gate">
    <section class="hero">
      <div class="kicker">Go-live final</div>
      <h1 class="title">O código está quase lá. O lançamento oficial depende do mundo real.</h1>
      <p class="lead">Este gate separa o que já foi resolvido no produto do que precisa ser ativado fora do código: domínio, GitHub App oficial, Mercado Pago produção, e-mail e worker. É a tela para decidir com segurança quando abrir para devs externos.</p>
      <span class="status {{ $readyLocal ? 'ready' : 'warn' }}">Local: {{ $readyLocal ? 'pronto' : 'revisar' }}</span>
      <span class="status {{ $readyPublic ? 'ready' : 'blocked' }}">Público: {{ $readyPublic ? 'liberado' : 'bloqueado' }}</span>
    </section>

    <section class="grid">
      <div class="card"><div class="kicker">Launch geral</div><div class="metric {{ $overall['percent'] >= 90 ? 'ok' : 'warn' }}">{{ $overall['percent'] }}%</div><div class="muted">Soma de beta, go-live, GitHub Program, evidências e roadmap.</div></div>
      <div class="card"><div class="kicker">Local</div><div class="metric {{ $goLive['local_percent'] >= 95 ? 'ok' : 'warn' }}">{{ $goLive['local_percent'] }}%</div><div class="muted">Itens que dependem só do app e configuração local.</div></div>
      <div class="card"><div class="kicker">Externo</div><div class="metric {{ $goLive['external_percent'] >= 100 ? 'ok' : 'bad' }}">{{ $goLive['external_percent'] }}%</div><div class="muted">Infra, credenciais e serviços finais.</div></div>
      <div class="card"><div class="kicker">Saúde</div><div class="metric {{ $health['ok'] ? 'ok' : 'bad' }}">{{ $health['ok'] ? 'OK' : '!' }}</div><div class="muted">Banco, cache, rotas e storage.</div></div>
    </section>

    <section class="two">
      <div class="card">
        <div class="kicker">Bloqueadores externos para lançamento oficial</div>
        @forelse ($externalBlockers as $blocker)
          <div class="item blocker"><strong>{{ $blocker['title'] }}</strong><span class="muted">{{ $blocker['nextAction'] }}</span><br><span class="muted">{{ $blocker['detail'] }}</span></div>
        @empty
          <div class="item done"><strong>Nenhum bloqueador externo ativo</strong><span class="muted">O produto pode avançar para release público e submissão final.</span></div>
        @endforelse

        <div class="kicker" style="margin-top:18px">Pendências locais</div>
        @forelse ($localBlockers as $blocker)
          <div class="item blocker"><strong>{{ $blocker['title'] }}</strong><span class="muted">{{ $blocker['nextAction'] }}</span></div>
        @empty
          <div class="item done"><strong>Sem pendências locais críticas</strong><span class="muted">O restante é ativação operacional e go-live externo.</span></div>
        @endforelse
      </div>

      <aside class="card">
        <div class="kicker">Ordem recomendada</div>
        <div class="steps">
          <div class="step"><div class="num">1</div><div><strong>Fixar domínio e HTTPS</strong><div class="muted">Definir APP_URL final e testar rotas públicas.</div></div></div>
          <div class="step"><div class="num">2</div><div><strong>Criar GitHub App real</strong><div class="muted">Configurar callback, webhook, permissões mínimas e secret.</div></div></div>
          <div class="step"><div class="num">3</div><div><strong>Virar billing produção</strong><div class="muted">Mercado Pago produção com webhook assinado e teste de baixo valor.</div></div></div>
          <div class="step"><div class="num">4</div><div><strong>Ativar e-mail e fila</strong><div class="muted">Convites, suporte, notificações e workers fora de sync.</div></div></div>
          <div class="step"><div class="num">5</div><div><strong>Rodar preflight strict</strong><div class="muted">Só então submeter no GitHub Developer Program.</div></div></div>
        </div>
        <pre class="cmd" style="margin-top:14px">php artisan devlog:go-live-check --json</pre>
      </aside>
    </section>
  </div>
</x-filament-panels::page>