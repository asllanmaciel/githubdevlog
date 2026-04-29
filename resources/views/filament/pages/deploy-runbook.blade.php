@php
  $appUrl = config('app.url');
  $isHttps = str_starts_with((string) $appUrl, 'https://');
  $checks = [
    ['title' => 'APP_ENV production', 'done' => app()->environment('production'), 'detail' => app()->environment()],
    ['title' => 'APP_DEBUG desativado', 'done' => ! config('app.debug'), 'detail' => config('app.debug') ? 'debug ativo' : 'debug inativo'],
    ['title' => 'APP_URL HTTPS', 'done' => $isHttps, 'detail' => $appUrl],
    ['title' => 'Mercado Pago configurado', 'done' => filled(config('services.mercado_pago.access_token')), 'detail' => config('services.mercado_pago.environment')],
    ['title' => 'Webhook secret Mercado Pago', 'done' => filled(config('services.mercado_pago.webhook_secret')), 'detail' => 'MERCADO_PAGO_WEBHOOK_SECRET'],
    ['title' => 'GitHub App webhook URL', 'done' => filled(config('services.github_app.webhook_url')), 'detail' => config('services.github_app.webhook_url') ?: 'pendente'],
  ];
@endphp

<x-filament-panels::page>
  <style>
    .deploy-runbook{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}
    .hero{margin-bottom:16px}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}
    .title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0}
    .grid{display:grid;grid-template-columns:1fr 380px;gap:16px}.steps{display:grid;gap:12px}.step{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px}.step strong{display:block;margin-bottom:6px}.step code{color:#b7e4ff}.check{display:grid;grid-template-columns:auto 1fr auto;gap:10px;align-items:center;border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:10px}.check.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.badge{width:30px;height:30px;border-radius:10px;display:grid;place-items:center;border:1px solid var(--line);font-size:12px}.check.done .badge{background:var(--green);border-color:var(--green);color:#071018}.muted{color:var(--muted);font-size:13px}.pill{border:1px solid var(--line);border-radius:999px;padding:4px 8px;color:var(--muted);font-size:12px}@media(max-width:1100px){.grid{grid-template-columns:1fr}}
  </style>

  <div class="deploy-runbook">
    <section class="hero">
      <div class="kicker">Infra / HostGator</div>
      <h1 class="title">Runbook para publicar sem improviso.</h1>
      <p class="lead">Procedimento operacional para levar o DevLog AI do ambiente local para um dominio HTTPS com MySQL, Mercado Pago e GitHub App configurados.</p>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Passos essenciais</div>
        <div class="steps">
          <div class="step"><strong>1. Preparar producao</strong><span class="muted">Criar banco MySQL, apontar dominio, ativar HTTPS e definir document root para <code>public</code>.</span></div>
          <div class="step"><strong>2. Configurar .env</strong><span class="muted">APP_ENV=production, APP_DEBUG=false, APP_URL final, MySQL, Mercado Pago producao e GitHub App oficial.</span></div>
          <div class="step"><strong>3. Build e upload</strong><span class="muted">Rodar composer install --no-dev, npm run build e enviar arquivos sem .git, node_modules ou .env local.</span></div>
          <div class="step"><strong>4. Pos-deploy</strong><span class="muted">Executar migrations, storage:link, optimize/cache e configurar cron/queue quando disponivel.</span></div>
          <div class="step"><strong>5. Preflight</strong><span class="muted">Rodar <code>php artisan devlog:preflight</code> para diagnostico e <code>php artisan devlog:preflight --strict</code> antes de lancar oficialmente.</span></div>
          <div class="step"><strong>6. Smoke test</strong><span class="muted">Testar /health, login, dashboard, webhook GitHub, webhook Mercado Pago e Centro de Lancamento.</span></div>
        </div>
      </div>

      <aside class="card">
        <div class="kicker">Checks do ambiente atual</div>
        <div class="steps">
          @foreach ($checks as $check)
            <div class="check {{ $check['done'] ? 'done' : '' }}">
              <div class="badge">{{ $check['done'] ? 'ok' : '' }}</div>
              <div><strong>{{ $check['title'] }}</strong><div class="muted">{{ $check['detail'] }}</div></div>
              <span class="pill">{{ $check['done'] ? 'Pronto' : 'Pendente' }}</span>
            </div>
          @endforeach
        </div>
        <div class="muted" style="margin-top:14px">Documento completo no repositorio: <code>docs/deploy-hostgator.md</code></div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
