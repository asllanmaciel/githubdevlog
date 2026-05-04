@php
  $githubConfig = config('services.github_app');
  $appUrl = config('app.url');
  $hasHttps = str_starts_with((string) $appUrl, 'https://');
  $roadmapDone = \App\Models\RoadmapItem::where('status', 'done')->count();
  $roadmapTotal = max(\App\Models\RoadmapItem::count(), 1);
  $roadmapPercent = round(($roadmapDone / $roadmapTotal) * 100);
  $eventsCount = \App\Models\WebhookEvent::count();
  $validEvents = \App\Models\WebhookEvent::where('signature_valid', true)->count();
  $workspacesCount = \App\Models\Workspace::count();
  $plansCount = \App\Models\BillingPlan::where('active', true)->count();
  $installationsCount = \App\Models\GithubInstallation::count();
  $setup = \App\Support\GitHubAppSetup::report();
  $supportReady = class_exists(\App\Models\SupportTicket::class);

  $checks = collect([
    ['area' => 'Produto publico', 'title' => 'Landing page de integracao GitHub', 'done' => true, 'detail' => url('/github')],
    ['area' => 'Produto publico', 'title' => 'Politica de privacidade publicada', 'done' => true, 'detail' => url('/privacy')],
    ['area' => 'Produto publico', 'title' => 'Termos de uso publicados', 'done' => true, 'detail' => url('/terms')],
    ['area' => 'Seguranca', 'title' => 'Webhooks com assinatura HMAC validada', 'done' => $validEvents > 0, 'detail' => $validEvents.' evento(s) validado(s)'],
    ['area' => 'Seguranca', 'title' => 'Payloads sanitizados antes do armazenamento', 'done' => true, 'detail' => 'Segredos e tokens sao mascarados'],
    ['area' => 'SaaS', 'title' => 'Workspaces privados e isolados', 'done' => $workspacesCount > 0, 'detail' => $workspacesCount.' workspace(s)'],
    ['area' => 'SaaS', 'title' => 'Planos e limites mensais configurados', 'done' => $plansCount > 0, 'detail' => $plansCount.' plano(s) ativo(s)'],
    ['area' => 'Suporte', 'title' => 'Canal de suporte dentro do produto', 'done' => $supportReady, 'detail' => 'Tickets no admin'],
    ['area' => 'GitHub App', 'title' => 'Rota de instalacao do GitHub App', 'done' => Route::has('github.install') && Route::has('github.callback'), 'detail' => url('/github/install')],
    ['area' => 'GitHub App', 'title' => 'Webhook oficial do GitHub App', 'done' => Route::has('webhooks.github-app'), 'detail' => url('/webhooks/github-app')],
    ['area' => 'GitHub App', 'title' => 'Instalacao vinculada a workspace', 'done' => $installationsCount > 0, 'detail' => $installationsCount.' instalacao(oes)'],
    ['area' => 'GitHub App', 'title' => 'App ID configurado', 'done' => filled($githubConfig['app_id'] ?? null), 'detail' => $githubConfig['app_id'] ?: 'Definir GITHUB_APP_ID'],
    ['area' => 'GitHub App', 'title' => 'Client ID e Secret configurados', 'done' => filled($githubConfig['client_id'] ?? null) && filled($githubConfig['client_secret'] ?? null), 'detail' => 'OAuth do GitHub App'],
    ['area' => 'GitHub App', 'title' => 'Webhook secret do GitHub App configurado', 'done' => filled($githubConfig['webhook_secret'] ?? null), 'detail' => 'Definir GITHUB_APP_WEBHOOK_SECRET'],
    ['area' => 'GitHub App', 'title' => 'Chave privada do GitHub App definida', 'done' => filled($githubConfig['private_key_path'] ?? null), 'detail' => $githubConfig['private_key_path'] ?: 'Definir GITHUB_APP_PRIVATE_KEY_PATH'],
    ['area' => 'Infra', 'title' => 'APP_URL em HTTPS para producao', 'done' => $hasHttps, 'detail' => $appUrl],
  ]);

  $doneChecks = $checks->where('done', true)->count();
  $readinessPercent = round(($doneChecks / max($checks->count(), 1)) * 100);

  $icons = [
    'github' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 0 0-3 19c.5.1.7-.2.7-.5v-2c-2.8.6-3.4-1.2-3.4-1.2-.5-1.1-1.1-1.4-1.1-1.4-.9-.6.1-.6.1-.6 1 0 1.6 1 1.6 1 .9 1.5 2.3 1.1 2.9.8.1-.7.4-1.1.7-1.4-2.2-.2-4.5-1.1-4.5-4.9 0-1.1.4-2 1-2.7-.1-.3-.4-1.3.1-2.7 0 0 .8-.3 2.8 1a9.7 9.7 0 0 1 5 0c1.9-1.3 2.8-1 2.8-1 .5 1.4.2 2.4.1 2.7.6.7 1 1.6 1 2.7 0 3.8-2.3 4.6-4.5 4.9.4.3.7.9.7 1.8v2.7c0 .3.2.6.7.5A10 10 0 0 0 12 2Z"></path></svg>',
    'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>',
    'warn' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.3 3.9 2.6 17.5A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.5L13.7 3.9a2 2 0 0 0-3.4 0Z"></path></svg>',
    'link' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.1 0l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1"></path><path d="M14 11a5 5 0 0 0-7.1 0l-2 2A5 5 0 0 0 12 20.1l1.1-1.1"></path></svg>',
  ];
@endphp

<x-filament-panels::page>
  <style>
    .gh-ready{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;color:var(--ink)}.gh-hero{display:grid;grid-template-columns:1.1fr .9fr;gap:16px;margin-bottom:16px}.gh-card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18);position:relative;overflow:hidden}.gh-card:after{content:"";position:absolute;right:-50px;top:-50px;width:150px;height:150px;border-radius:50%;background:rgba(80,184,255,.12)}.gh-card>*{position:relative}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(32px,4.8vw,62px);line-height:.96;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0}.orb{width:128px;height:128px;border-radius:36px;display:grid;place-items:center;background:radial-gradient(circle at 35% 25%,rgba(105,227,154,.28),rgba(80,184,255,.13) 42%,rgba(8,16,25,.94) 72%);border:1px solid rgba(105,227,154,.3);font-size:32px;font-weight:950}.progress{height:10px;border-radius:999px;background:#0b1118;border:1px solid var(--line);overflow:hidden}.progress span{display:block;height:100%;background:linear-gradient(90deg,var(--blue),var(--green));border-radius:999px}.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.value{font-size:32px;font-weight:950;letter-spacing:-.04em}.label{color:var(--muted);font-size:13px}.grid{display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start}.check-list{display:grid;gap:10px}.check{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}.check.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.mini-icon{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);background:#071018;color:var(--blue);flex:0 0 auto}.mini-icon.ok{background:var(--green);border-color:var(--green);color:#071018}.mini-icon.warn{color:var(--yellow);border-color:rgba(255,209,102,.45);background:rgba(255,209,102,.08)}.mini-icon svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}.mini-icon.github svg{fill:currentColor;stroke:none}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.setup-list{display:grid;gap:10px;margin:14px 0}.setup-item{display:grid;grid-template-columns:auto 1fr;gap:10px;align-items:start;border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:12px}.setup-item.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.setup-item code{display:block;margin-top:6px;word-break:break-all;color:#b7e4ff}.copy-grid{display:grid;gap:10px;margin:14px 0}.copy-row{border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:12px}.copy-row strong{display:block}.copy-row code{display:block;margin-top:6px;word-break:break-all;color:#b7e4ff}.actions{display:grid;gap:10px}.action{display:flex;align-items:center;gap:10px;border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:12px;text-decoration:none;color:var(--ink);font-weight:850}.action.primary{background:var(--blue);border-color:var(--blue);color:#071018}@media(max-width:1100px){.gh-hero,.grid{grid-template-columns:1fr}.metrics{grid-template-columns:repeat(2,1fr)}}@media(max-width:720px){.check{grid-template-columns:auto 1fr}.check .pill{grid-column:1/-1}.metrics{grid-template-columns:1fr}}
  </style>

  <div class="gh-ready">
    <section class="gh-hero">
      <div class="gh-card">
        <div class="kicker">GitHub Developer Program</div>
        <h1 class="title">Prontidao para lancamento oficial.</h1>
        <p class="lead">Checklist vivo para transformar o DevLog AI em uma integracao apresentavel ao ecossistema GitHub: produto publico, seguranca, SaaS, suporte, GitHub App e infraestrutura de producao.</p>
      </div>
      <div class="gh-card" style="display:flex;justify-content:space-between;gap:18px;align-items:center">
        <div>
          <div class="kicker">Readiness score</div>
          <div class="value">{{ $readinessPercent }}%</div>
          <div class="label">{{ $doneChecks }} de {{ $checks->count() }} checks prontos</div>
          <div class="progress" style="margin-top:18px"><span style="width:{{ $readinessPercent }}%"></span></div>
        </div>
        <div class="orb">{{ $readinessPercent }}%</div>
      </div>
    </section>

    <section class="metrics">
      <div class="metric"><div class="value">{{ $roadmapPercent }}%</div><div class="label">roadmap concluido</div></div>
      <div class="metric"><div class="value">{{ $eventsCount }}</div><div class="label">webhooks capturados</div></div>
      <div class="metric"><div class="value">{{ $validEvents }}</div><div class="label">assinaturas validadas</div></div>
      <div class="metric"><div class="value">{{ $installationsCount }}</div><div class="label">instalacoes GitHub App</div></div>
    </section>

    <section class="grid">
      <div class="gh-card">
        <div class="kicker">Checklist operacional</div>
        <div class="check-list">
          @foreach ($checks->groupBy('area') as $area => $items)
            <div class="label" style="margin-top:8px;text-transform:uppercase;letter-spacing:.12em;font-weight:950">{{ $area }}</div>
            @foreach ($items as $item)
              <div class="check {{ $item['done'] ? 'done' : '' }}">
                <div class="mini-icon {{ $item['done'] ? 'ok' : 'warn' }}">{!! $item['done'] ? $icons['check'] : $icons['warn'] !!}</div>
                <div>
                  <strong>{{ $item['title'] }}</strong>
                  <div class="label">{{ $item['detail'] }}</div>
                </div>
                <span class="pill">{{ $item['done'] ? 'Pronto' : 'Pendente' }}</span>
              </div>
            @endforeach
          @endforeach
        </div>
      </div>

      <aside class="gh-card">
        <div class="kicker">Setup GitHub App</div>
        <div class="metric" style="margin-bottom:14px">
          <div class="value">{{ $setup['percent'] }}%</div>
          <div class="label">setup oficial GitHub App</div>
        </div>
        <div class="setup-list">
          @foreach ($setup['steps'] as $step)
            <div class="setup-item {{ $step['done'] ? 'done' : '' }}">
              <div class="mini-icon {{ $step['done'] ? 'ok' : 'warn' }}">{!! $step['done'] ? $icons['check'] : $icons['warn'] !!}</div>
              <div>
                <strong>{{ $step['done'] ? 'OK' : 'Pendente' }} - {{ $step['title'] }}</strong>
                <div class="label">{{ $step['detail'] }}</div>
              </div>
            </div>
          @endforeach
        </div>

        <div class="kicker" style="margin-top:18px">Valores para colar no GitHub</div>
        <div class="copy-row">
          <strong>Nome</strong>
          <code>{{ $setup['app_profile']['name'] }}</code>
          <div class="label">{{ $setup['app_profile']['description'] }}</div>
        </div>
        <div class="copy-grid">
          @foreach ($setup['urls'] as $url)
            <div class="copy-row">
              <strong>{{ $url['label'] }}</strong>
              <code>{{ $url['value'] }}</code>
              <div class="label">{{ $url['description'] }}</div>
            </div>
          @endforeach
        </div>

        <div class="kicker" style="margin-top:18px">Variaveis .env</div>
        <div class="copy-row">
          <strong>Snippet sugerido</strong>
          <code style="white-space:pre-wrap">{{ $setup['env_snippet'] }}</code>
        </div>
        <div class="setup-list">
          @foreach ($setup['env'] as $env)
            <div class="setup-item {{ $env['done'] ? 'done' : '' }}">
              <div class="mini-icon {{ $env['done'] ? 'ok' : 'warn' }}">{!! $env['done'] ? $icons['check'] : $icons['warn'] !!}</div>
              <div>
                <strong>{{ $env['done'] ? 'OK' : 'Pendente' }} - {{ $env['key'] }}</strong>
                <code>{{ $env['value'] }}</code>
                <div class="label">{{ $env['description'] }}</div>
              </div>
            </div>
          @endforeach
        </div>

        <div class="kicker" style="margin-top:18px">Proximas acoes</div>
        <div class="actions">
          <a class="action primary" href="{{ route('github.install') }}"><span class="mini-icon github">{!! $icons['github'] !!}</span>Testar instalacao GitHub App</a>
          <a class="action" href="{{ url('/github') }}" target="_blank"><span class="mini-icon">{!! $icons['link'] !!}</span>Revisar pagina publica GitHub</a>
          <a class="action" href="{{ url('/admin/roadmap') }}"><span class="mini-icon">{!! $icons['check'] !!}</span>Abrir roadmap visual</a>
        </div>
        <div class="label" style="margin-top:16px;line-height:1.6">Developer Program concluido. O proximo marco e manter a demo ponta a ponta saudavel e preparar a listagem opcional do GitHub App no Marketplace, sem expor secrets em prints ou videos.</div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>

