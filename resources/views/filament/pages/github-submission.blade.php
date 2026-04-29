@php
  $appUrl = config('app.url');
  $githubConfig = config('services.github_app');
  $launch = App\Support\LaunchReadiness::report();
  $security = App\Support\SecurityPosture::report();
  $health = App\Support\SystemHealth::report();
  $production = App\Support\ProductionEnvironment::report();

  $materials = collect([
    ['group' => 'Publico', 'title' => 'Landing publica', 'done' => Route::has('home'), 'detail' => url('/')],
    ['group' => 'Publico', 'title' => 'Pagina de integracao GitHub', 'done' => Route::has('github.landing') || Route::has('home'), 'detail' => url('/github')],
    ['group' => 'Publico', 'title' => 'Docs de usuarios', 'done' => Route::has('docs.users'), 'detail' => url('/docs/usuarios')],
    ['group' => 'Legal', 'title' => 'Privacidade publicada', 'done' => Route::has('privacy'), 'detail' => url('/privacy')],
    ['group' => 'Legal', 'title' => 'Termos publicados', 'done' => Route::has('terms'), 'detail' => url('/terms')],
    ['group' => 'Produto', 'title' => 'Centro de demo', 'done' => true, 'detail' => url('/admin/demo-center')],
    ['group' => 'Produto', 'title' => 'Gate strict de lancamento', 'done' => true, 'detail' => url('/admin/launch-gate')],
    ['group' => 'Produto', 'title' => 'Assets de submissao', 'done' => true, 'detail' => url('/admin/submission-assets')],
    ['group' => 'Produto', 'title' => 'Seed demo operacional', 'done' => file_exists(app_path('Console/Commands/DevlogSeedDemo.php')), 'detail' => 'php artisan devlog:seed-demo'],
    ['group' => 'Tecnico', 'title' => 'Readiness acima de 70%', 'done' => $launch['percent'] >= 70, 'detail' => $launch['percent'].'%'],
    ['group' => 'Tecnico', 'title' => 'Seguranca acima de 75%', 'done' => $security['percent'] >= 75, 'detail' => $security['percent'].'%'],
    ['group' => 'Tecnico', 'title' => 'Health OK', 'done' => $health['ok'], 'detail' => $health['ok'] ? 'OK' : 'Verificar status'],
    ['group' => 'Producao', 'title' => 'Ambiente de producao validado', 'done' => $production['ready'], 'detail' => $production['percent'].'%'],
    ['group' => 'GitHub App', 'title' => 'App ID configurado', 'done' => filled($githubConfig['app_id'] ?? null), 'detail' => filled($githubConfig['app_id'] ?? null) ? 'Configurado' : 'GITHUB_APP_ID pendente'],
    ['group' => 'GitHub App', 'title' => 'Webhook GitHub App', 'done' => Route::has('webhooks.github-app'), 'detail' => url('/webhooks/github-app')],
    ['group' => 'GitHub App', 'title' => 'Callback OAuth', 'done' => Route::has('github.callback'), 'detail' => url('/github/callback')],
  ]);

  $screenshots = collect([
    ['title' => 'Landing com proposta de valor', 'why' => 'Mostra dor, publico e CTA para devs.'],
    ['title' => 'Dashboard do usuario', 'why' => 'Mostra workspace, endpoint, eventos e onboarding.'],
    ['title' => 'Evento GitHub validado', 'why' => 'Prova assinatura HMAC, delivery id e payload organizado.'],
    ['title' => 'Notas e tarefas em webhook', 'why' => 'Mostra colaboracao e valor alem de log bruto.'],
    ['title' => 'Admin Launch Gate', 'why' => 'Mostra maturidade operacional para release.'],
    ['title' => 'Admin Security Center', 'why' => 'Mostra postura de seguranca e governanca.'],
    ['title' => 'Billing / planos', 'why' => 'Mostra viabilidade SaaS e monetizacao.'],
  ]);

  $permissions = collect([
    ['name' => 'Repository metadata', 'level' => 'Read-only', 'reason' => 'Identificar repositorio, owner, branch padrao e contexto do evento.'],
    ['name' => 'Webhooks', 'level' => 'Read-only/event delivery', 'reason' => 'Receber eventos configurados pelo usuario ou pela instalacao do app.'],
    ['name' => 'Contents', 'level' => 'Read-only opcional', 'reason' => 'Reservado para evolucao de resumo de arquivos alterados sem escrever no repositorio.'],
    ['name' => 'Pull requests', 'level' => 'Read-only opcional', 'reason' => 'Exibir contexto de PR em eventos pull_request.'],
    ['name' => 'Issues', 'level' => 'Read-only opcional', 'reason' => 'Exibir contexto quando eventos de issue forem habilitados.'],
  ]);

  $answers = collect([
    ['q' => 'What does your app do?', 'a' => 'GitHub DevLog AI is a private webhook inbox for GitHub integrations. It validates GitHub webhook signatures, stores sanitized payloads inside isolated workspaces and gives developers a readable timeline to debug push, pull_request, workflow_run and issue events.'],
    ['q' => 'Who is it for?', 'a' => 'It is built for developers, SaaS teams and agencies that create GitHub Apps, automations or webhook-based workflows and need a safer way to inspect delivery history without exposing local logs or secrets.'],
    ['q' => 'How does it integrate with GitHub?', 'a' => 'Users can configure a repository webhook with a workspace URL and secret, or install the GitHub App flow. The platform validates X-Hub-Signature-256, maps events to the correct workspace and displays delivery metadata and payload context.'],
    ['q' => 'How do you protect user data?', 'a' => 'Each workspace is isolated, webhook secrets can be rotated, signatures are validated before trust, sensitive payload fields are sanitized and admin security/readiness checks monitor production posture.'],
  ]);

  $done = $materials->where('done', true)->count();
  $percent = round(($done / max($materials->count(), 1)) * 100);
@endphp

<x-filament-panels::page>
  <style>
    .submission{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;--danger:#ff6b6b;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{display:grid;grid-template-columns:1fr auto;gap:18px;align-items:center;margin-bottom:16px;background:radial-gradient(circle at 80% 14%,rgba(80,184,255,.18),transparent 28%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,64px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}.orb{width:132px;height:132px;border-radius:36px;display:grid;place-items:center;border:1px solid rgba(105,227,154,.32);background:radial-gradient(circle at 35% 25%,rgba(105,227,154,.28),rgba(80,184,255,.12) 44%,rgba(8,16,25,.94) 74%);font-weight:950;font-size:30px}
    .grid{display:grid;grid-template-columns:1fr 430px;gap:16px;margin-bottom:16px}.wide{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:16px}.stack{display:grid;gap:12px}.block{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px}.block strong{display:block;margin-bottom:8px}.block p{color:var(--muted);line-height:1.65;margin:0}.check{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}.check.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.badge{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950;color:var(--muted)}.check.done .badge{background:var(--green);border-color:var(--green);color:#071018}.detail{color:var(--muted);font-size:13px;line-height:1.55}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}.group-title{color:var(--green);font-size:12px;text-transform:uppercase;letter-spacing:.12em;font-weight:950;margin:10px 0 0}@media(max-width:1100px){.hero,.grid,.wide{grid-template-columns:1fr}}@media(max-width:720px){.check{grid-template-columns:auto 1fr}.check .pill{grid-column:1/-1}.orb{width:100px;height:100px}}
  </style>

  <div class="submission">
    <section class="hero">
      <div>
        <div class="kicker">GitHub Developer Program</div>
        <h1 class="title">Submission pack para vender a integracao.</h1>
        <p class="lead">Copy, evidencias, permissoes, screenshots e respostas prontas para apresentar o DevLog AI como uma ferramenta GitHub segura, util e pronta para devs.</p>
      </div>
      <div class="orb">{{ $percent }}%</div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Narrativa principal</div>
        <div class="stack">
          <div class="block"><strong>One-liner</strong><p>GitHub DevLog AI is a private webhook inbox for GitHub developers, with signature validation, sanitized payload history and workspace-based debugging.</p></div>
          <div class="block"><strong>Problema</strong><p>Debugging GitHub webhooks is still noisy: developers need to know if GitHub delivered the event, which payload arrived, whether the signature is valid and how to share that context with a team.</p></div>
          <div class="block"><strong>Solucao</strong><p>The product gives every developer or team a private workspace, a GitHub webhook endpoint, a secret, delivery history, HMAC validation, notes, tasks and support workflows around real GitHub events.</p></div>
          <div class="block"><strong>Diferencial</strong><p>Unlike generic request bins, DevLog AI is GitHub-aware, SaaS-ready and privacy-first: events are isolated by workspace, secrets can be rotated and payloads are prepared for operational debugging.</p></div>
        </div>
      </div>

      <aside class="card">
        <div class="kicker">Materiais obrigatorios</div>
        <div class="stack">
          @foreach ($materials->groupBy('group') as $group => $items)
            <div class="group-title">{{ $group }}</div>
            @foreach ($items as $item)
              <div class="check {{ $item['done'] ? 'done' : '' }}">
                <div class="badge">{{ $item['done'] ? 'ok' : '!' }}</div>
                <div><strong>{{ $item['title'] }}</strong><div class="detail">{{ $item['detail'] }}</div></div>
                <span class="pill">{{ $item['done'] ? 'Pronto' : 'Pendente' }}</span>
              </div>
            @endforeach
          @endforeach
        </div>
      </aside>
    </section>

    <section class="wide">
      <div class="card">
        <div class="kicker">Permissoes GitHub sugeridas</div>
        <div class="stack">
          @foreach ($permissions as $permission)
            <div class="block"><strong>{{ $permission['name'] }} · {{ $permission['level'] }}</strong><p>{{ $permission['reason'] }}</p></div>
          @endforeach
        </div>
      </div>

      <div class="card">
        <div class="kicker">Screenshots para submissao</div>
        <div class="stack">
          @foreach ($screenshots as $shot)
            <div class="block"><strong>{{ $shot['title'] }}</strong><p>{{ $shot['why'] }}</p></div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Respostas prontas</div>
        <div class="stack">
          @foreach ($answers as $answer)
            <div class="block"><strong>{{ $answer['q'] }}</strong><p>{{ $answer['a'] }}</p></div>
          @endforeach
        </div>
      </div>

      <aside class="card">
        <div class="kicker">Comandos antes de gravar demo</div>
        <pre class="cmd">php artisan devlog:seed-demo
php artisan devlog:seed-submission-assets
php artisan devlog:preflight --strict
php artisan optimize:clear</pre>
        <div class="kicker" style="margin-top:18px">URLs para conferir</div>
        <pre class="cmd">{{ url('/') }}
{{ url('/dashboard') }}
{{ url('/admin/github-readiness') }}
{{ url('/admin/github-submission') }}
{{ url('/admin/submission-assets') }}
{{ url('/admin/launch-gate') }}</pre>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
