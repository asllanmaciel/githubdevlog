@php
  $launch = App\Support\LaunchReadiness::report();
  $production = App\Support\ProductionEnvironment::report();
  $security = App\Support\SecurityPosture::report();
  $health = App\Support\SystemHealth::report();
  $launchBlockers = $launch['blockers'];
  $productionPending = $production['required_pending'];
  $externalKeys = collect([
    'GITHUB_APP_ID' => 'GitHub Developer Settings > GitHub Apps > App ID',
    'GITHUB_APP_CLIENT_ID' => 'GitHub App > General > Client ID',
    'GITHUB_APP_CLIENT_SECRET' => 'GitHub App > General > Generate client secret',
    'GITHUB_APP_WEBHOOK_SECRET' => 'Definir secret forte no GitHub App e repetir no .env',
    'GITHUB_APP_PRIVATE_KEY_PATH' => 'Baixar private key do GitHub App e salvar fora de public/',
    'MERCADO_PAGO_ENVIRONMENT' => 'Trocar de sandbox para production apenas no ambiente final',
  ]);
@endphp

<x-filament-panels::page>
  <style>
    .blockers{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;--danger:#ff6b6b;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 84% 12%,rgba(255,107,107,.14),transparent 30%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}.grid{display:grid;grid-template-columns:1fr 430px;gap:16px}.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:16px}.metric,.item{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.value{font-size:38px;font-weight:950;letter-spacing:-.05em}.muted{color:var(--muted);font-size:13px;line-height:1.55}.item{margin-bottom:12px}.item.block{border-color:rgba(255,107,107,.42)}.item.ok{border-color:rgba(105,227,154,.42);background:rgba(105,227,154,.06)}.item strong{display:block;margin-bottom:5px}.pill{display:inline-flex;border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;margin:6px 6px 0 0}.pill.danger{background:var(--danger);border-color:var(--danger);color:#071018;font-weight:950}.pill.ok{background:var(--green);border-color:var(--green);color:#071018;font-weight:950}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}@media(max-width:1100px){.grid,.metrics{grid-template-columns:1fr}}
  </style>

  <div class="blockers">
    <section class="hero">
      <div class="kicker">Launch unblock plan</div>
      <h1 class="title">O que ainda segura o lancamento oficial.</h1>
      <p class="lead">Esta tela separa bloqueios que conseguimos resolver no projeto daqueles que dependem de credenciais reais, dominio final e instalacao oficial do GitHub App.</p>
    </section>

    <section class="metrics">
      <div class="metric"><div class="kicker">Health</div><div class="value">{{ $health['ok'] ? 'OK' : '!' }}</div><div class="muted">Estado tecnico geral.</div></div>
      <div class="metric"><div class="kicker">Seguranca</div><div class="value">{{ $security['percent'] }}%</div><div class="muted">Minimo atual: 75%.</div></div>
      <div class="metric"><div class="kicker">Readiness</div><div class="value">{{ $launch['percent'] }}%</div><div class="muted">{{ $launchBlockers->count() }} bloqueador(es).</div></div>
      <div class="metric"><div class="kicker">Producao</div><div class="value">{{ $production['percent'] }}%</div><div class="muted">{{ $productionPending->count() }} pendencia(s).</div></div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Bloqueadores atuais</div>
        @forelse ($launchBlockers as $blocker)
          <div class="item block">
            <strong>{{ $blocker['title'] }}</strong>
            <div class="muted">{{ $blocker['detail'] }}</div>
            <span class="pill danger">Obrigatorio</span>
          </div>
        @empty
          <div class="item ok"><strong>Nenhum bloqueador de readiness</strong><div class="muted">O readiness do produto esta liberado.</div><span class="pill ok">Pronto</span></div>
        @endforelse

        <div class="kicker" style="margin-top:18px">Pendencias de producao</div>
        @forelse ($productionPending as $pending)
          <div class="item block">
            <strong>{{ $pending['title'] }}</strong>
            <div class="muted">{{ $pending['detail'] }}</div>
            <span class="pill danger">Producao</span>
          </div>
        @empty
          <div class="item ok"><strong>Ambiente de producao pronto</strong><div class="muted">Nenhuma pendencia obrigatoria.</div><span class="pill ok">Pronto</span></div>
        @endforelse
      </div>

      <aside class="card">
        <div class="kicker">Credenciais externas</div>
        @foreach ($externalKeys as $key => $where)
          <div class="item">
            <strong>{{ $key }}</strong>
            <div class="muted">{{ $where }}</div>
          </div>
        @endforeach

        <div class="kicker" style="margin-top:18px">Ordem recomendada</div>
        <pre class="cmd">1. Criar GitHub App real
2. Configurar callback e webhook HTTPS
3. Preencher GITHUB_APP_* no .env
4. Instalar o app em um repositorio real
5. Trocar Mercado Pago para production no deploy final
6. Rodar php artisan devlog:preflight --strict</pre>
      </aside>
    </section>
  </div>
</x-filament-panels::page>