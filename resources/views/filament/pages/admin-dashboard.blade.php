@php
  $launch = \App\Support\LaunchReadiness::report();
  $goLive = \App\Support\GoLiveExecutionKit::report();
  $githubProgram = \App\Support\GitHubProgramReadiness::report();
  $security = \App\Support\SecurityPosture::report();
  $health = \App\Support\SystemHealth::report();
  $hardening = \App\Support\WebhookDeliveryHardening::report();

  $usersCount = \App\Models\User::count();
  $workspacesCount = \App\Models\Workspace::count();
  $eventsCount = \App\Models\WebhookEvent::count();
  $validEvents = \App\Models\WebhookEvent::where('signature_valid', true)->count();
  $installationsCount = \App\Models\GithubInstallation::count();
  $ticketsOpen = \App\Models\SupportTicket::where('status', 'open')->count();
  $plansCount = \App\Models\BillingPlan::count();
  $activeSubscriptions = \App\Models\WorkspaceSubscription::where('status', 'active')->with('plan')->get();
  $pendingSubscriptions = \App\Models\WorkspaceSubscription::where('status', 'pending')->count();
  $canceledSubscriptions = \App\Models\WorkspaceSubscription::where('status', 'canceled')->count();
  $mrrCents = $activeSubscriptions->sum(fn ($subscription) => (int) ($subscription->plan?->price_cents ?? 0));
  $mrr = 'R$ '.number_format($mrrCents / 100, 2, ',', '.');
  $billingEventsToday = \App\Models\BillingEvent::whereDate('created_at', now()->toDateString())->count();
  $billingRisk = \App\Models\BillingEvent::whereIn('status', ['pending_lookup', 'unmatched'])->count();
  $recentBillingEvents = \App\Models\BillingEvent::latest()->limit(4)->get();
  $roadmap = \App\Models\RoadmapItem::all();
  $roadmapDone = $roadmap->where('status', 'done')->count();
  $roadmapPercent = round(($roadmapDone / max($roadmap->count(), 1)) * 100);
  $eventTypes = \App\Models\WebhookEvent::selectRaw('event_name, count(*) as total')->groupBy('event_name')->orderByDesc('total')->limit(6)->get();
  $maxEventType = max((int) ($eventTypes->max('total') ?? 1), 1);
  $recentTickets = \App\Models\SupportTicket::latest()->limit(4)->get();

  $releaseLabel = $launch['percent'] >= 95 && $githubProgram['percent'] === 100 && $hardening['rejected'] === 0
    ? 'Beta publico controlado'
    : 'Preparacao de release';

  $decision = $launch['percent'] >= 95 && $githubProgram['percent'] === 100
    ? 'Pode divulgar com controle'
    : 'Segurar divulgacao ampla';

  $nextActions = collect([
    ['title' => 'Deployar painel Marketplace', 'detail' => 'Levar /admin/github-marketplace para producao e revisar a listagem do App.', 'href' => url('/admin/github-marketplace'), 'level' => 'primary'],
    ['title' => 'Rotacionar secrets expostos em prints', 'detail' => 'Trocar secret do GitHub App se algum valor apareceu em evidencia, chat ou video.', 'href' => url('/admin/security-center'), 'level' => 'danger'],
    ['title' => 'Rodar teste real pos-deploy', 'detail' => 'Gerar um push e confirmar evento novo no inbox, detalhe e hardening.', 'href' => url('/dashboard/events'), 'level' => 'normal'],
    ['title' => 'Preparar mensagem de beta', 'detail' => 'Divulgar como beta para devs que auditam e depuram webhooks GitHub.', 'href' => url('/admin/github-submission'), 'level' => 'normal'],
  ]);

  $quickGroups = [
    'Go-live' => [
      ['label' => 'Launch gate', 'href' => url('/admin/launch-gate')],
      ['label' => 'Prontidao GitHub', 'href' => url('/admin/github-readiness')],
      ['label' => 'Marketplace', 'href' => url('/admin/github-marketplace')],
      ['label' => 'Submissao GitHub', 'href' => url('/admin/github-submission')],
    ],
    'Operacao' => [
      ['label' => 'Status sistema', 'href' => url('/admin/system-status')],
      ['label' => 'Seguranca', 'href' => url('/admin/security-center')],
      ['label' => 'Hardening webhooks', 'href' => url('/admin/webhook-hardening')],
      ['label' => 'Eventos webhook', 'href' => url('/admin/webhook-events')],
    ],
    'SaaS' => [
      ['label' => 'Assinaturas', 'href' => url('/admin/workspace-subscriptions')],
      ['label' => 'Eventos cobranca', 'href' => url('/admin/billing-events')],
      ['label' => 'Suporte', 'href' => url('/admin/support-tickets')],
      ['label' => 'Roadmap', 'href' => url('/admin/roadmap')],
    ],
    'Publico' => [
      ['label' => 'Pagina GitHub', 'href' => url('/github')],
      ['label' => 'Contato', 'href' => url('/contact')],
      ['label' => 'Privacidade', 'href' => url('/privacy')],
      ['label' => 'Termos', 'href' => url('/terms')],
    ],
  ];
@endphp

<x-filament-panels::page>
  <style>
    .admin-cockpit{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--panel:#101720;--blue:#50b8ff;--green:#69e39a;--yellow:#ffcf66;--danger:#ff6b6b;color:var(--ink)}
    .hero,.card,.metric{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);box-shadow:0 22px 60px rgba(0,0,0,.18)}
    .hero{display:grid;grid-template-columns:1fr 360px;gap:18px;align-items:stretch;padding:22px;margin-bottom:16px;background:radial-gradient(circle at 82% 8%,rgba(105,227,154,.18),transparent 30%),linear-gradient(135deg,rgba(16,23,32,.96),rgba(9,14,20,.9))}
    .card{padding:18px}.metric{padding:16px;min-height:118px;background:linear-gradient(180deg,rgba(16,23,32,.94),rgba(12,18,25,.9))}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}
    .title{font-size:clamp(34px,4.7vw,64px);line-height:.94;letter-spacing:-.055em;font-weight:950;margin:0;color:var(--ink)}
    .lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:920px}
    .decision{display:grid;gap:12px}.decision-main{font-size:30px;line-height:1;font-weight:950;letter-spacing:-.04em}.decision-label{color:var(--green);font-weight:950}.decision-copy{color:var(--muted);line-height:1.6}
    .pill-row{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}.pill{border:1px solid var(--line);border-radius:999px;padding:7px 10px;color:var(--muted);font-size:12px;font-weight:850}.pill.ok{color:var(--green);border-color:rgba(105,227,154,.4);background:rgba(105,227,154,.08)}.pill.warn{color:var(--yellow);border-color:rgba(255,207,102,.42);background:rgba(255,207,102,.08)}
    .metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}.metric-value{font-size:34px;font-weight:950;letter-spacing:-.05em;color:var(--ink)}.metric-label{color:var(--muted);font-size:13px;line-height:1.45}.ok{color:var(--green)}.risk{color:var(--yellow)}.bad{color:var(--danger)}
    .layout{display:grid;grid-template-columns:1fr 390px;gap:16px;margin-bottom:16px}.wide{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}.stack{display:grid;gap:10px}
    .action{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:13px;text-decoration:none;color:var(--ink);display:block}.action strong{display:block;margin-bottom:5px}.action span{color:var(--muted);font-size:13px;line-height:1.5}.action.primary{border-color:rgba(80,184,255,.48);background:rgba(80,184,255,.09)}.action.danger{border-color:rgba(255,107,107,.45);background:rgba(255,107,107,.08)}
    .bar{height:10px;border-radius:999px;background:#0b1118;border:1px solid var(--line);overflow:hidden}.bar span{display:block;height:100%;background:linear-gradient(90deg,var(--blue),var(--green));border-radius:999px}
    .rowx{border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:12px;color:var(--muted);font-size:13px}.rowx strong{color:var(--ink)}
    .quick-groups{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.quick-title{color:var(--green);font-size:12px;text-transform:uppercase;letter-spacing:.12em;font-weight:950;margin-bottom:8px}.quick-links{display:grid;gap:8px}.quick-links a{border:1px solid var(--line);border-radius:12px;padding:10px;background:#0b1118;color:var(--ink);text-decoration:none;font-weight:850;font-size:13px}
    @media(max-width:1200px){.hero,.layout,.wide{grid-template-columns:1fr}.metrics,.quick-groups{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:720px){.metrics,.quick-groups{grid-template-columns:1fr}}
  </style>

  <div class="admin-cockpit">
    <section class="hero">
      <div>
        <div class="kicker">Admin / Cockpit de produto</div>
        <h1 class="title">DevLog AI saiu de setup. Agora e operacao de go-live.</h1>
        <p class="lead">Acompanhe em uma tela se o produto pode ser divulgado, quais sinais provam maturidade e quais acoes ainda reduzem risco antes de abrir para mais devs.</p>
        <div class="pill-row">
          <span class="pill ok">Launch {{ $launch['percent'] }}%</span>
          <span class="pill ok">GitHub {{ $githubProgram['percent'] }}%</span>
          <span class="pill ok">Hardening {{ $hardening['valid_rate'] }}%</span>
          <span class="pill {{ $health['ok'] ? 'ok' : 'warn' }}">Saude {{ $health['ok'] ? 'OK' : 'Atencao' }}</span>
          <span class="pill warn">{{ $releaseLabel }}</span>
        </div>
      </div>
      <aside class="card decision">
        <div class="kicker">Decisao de divulgacao</div>
        <div>
          <div class="decision-label">{{ $releaseLabel }}</div>
          <div class="decision-main">{{ $decision }}</div>
        </div>
        <div class="decision-copy">Use uma mensagem de beta publico/controlado enquanto Marketplace, volume real e rotacao de secrets ficam redondos.</div>
        <a class="action primary" href="{{ url('/admin/launch-gate') }}"><strong>Abrir launch gate</strong><span>Ver bloqueadores e ordem recomendada.</span></a>
      </aside>
    </section>

    <section class="metrics">
      <div class="metric"><div class="kicker">Launch</div><div class="metric-value ok">{{ $launch['percent'] }}%</div><div class="metric-label">{{ $launch['done'] }} de {{ $launch['total'] }} checks prontos</div></div>
      <div class="metric"><div class="kicker">GitHub</div><div class="metric-value ok">{{ $githubProgram['percent'] }}%</div><div class="metric-label">Developer Program, App e evidencias</div></div>
      <div class="metric"><div class="kicker">Webhooks</div><div class="metric-value ok">{{ $hardening['accepted'] }}/{{ $hardening['total'] }}</div><div class="metric-label">{{ $hardening['rejected'] }} rejeitado(s), {{ $hardening['valid_rate'] }}% validos</div></div>
      <div class="metric"><div class="kicker">Seguranca</div><div class="metric-value {{ $security['percent'] >= 90 ? 'ok' : 'risk' }}">{{ $security['percent'] }}%</div><div class="metric-label">{{ $security['done'] }} de {{ $security['total'] }} checks prontos</div></div>
    </section>

    <section class="metrics">
      <div class="metric"><div class="metric-value">{{ $eventsCount }}</div><div class="metric-label">webhooks recebidos, {{ $validEvents }} validos</div></div>
      <div class="metric"><div class="metric-value">{{ $installationsCount }}</div><div class="metric-label">instalacoes GitHub App vinculadas</div></div>
      <div class="metric"><div class="metric-value">{{ $workspacesCount }}</div><div class="metric-label">workspaces, {{ $usersCount }} usuario(s)</div></div>
      <div class="metric"><div class="metric-value">{{ $roadmapPercent }}%</div><div class="metric-label">{{ $roadmapDone }} de {{ $roadmap->count() }} itens do roadmap</div></div>
    </section>

    <section class="layout">
      <div class="card">
        <div class="kicker">Proximas acoes de maior impacto</div>
        <div class="stack">
          @foreach ($nextActions as $action)
            <a class="action {{ $action['level'] }}" href="{{ $action['href'] }}">
              <strong>{{ $action['title'] }}</strong>
              <span>{{ $action['detail'] }}</span>
            </a>
          @endforeach
        </div>
      </div>

      <aside class="card">
        <div class="kicker">Sinais SaaS</div>
        <div class="stack">
          <div class="rowx"><strong>{{ $mrr }}</strong><br>MRR estimado em assinaturas ativas</div>
          <div class="rowx"><strong>{{ $activeSubscriptions->count() }}</strong><br>assinaturas ativas; {{ $pendingSubscriptions }} pendente(s)</div>
          <div class="rowx"><strong class="{{ $billingRisk > 0 ? 'risk' : 'ok' }}">{{ $billingRisk }}</strong><br>eventos de cobranca com atencao</div>
          <div class="rowx"><strong>{{ $ticketsOpen }}</strong><br>chamados abertos; {{ $canceledSubscriptions }} assinatura(s) cancelada(s)</div>
        </div>
      </aside>
    </section>

    <section class="wide">
      <div class="card">
        <div class="kicker">Eventos por tipo</div>
        <div class="stack">
          @forelse($eventTypes as $type)
            <div>
              <div style="display:flex;justify-content:space-between;gap:12px;margin-bottom:6px"><strong>{{ $type->event_name }}</strong><span style="color:var(--muted)">{{ $type->total }}</span></div>
              <div class="bar"><span style="width:{{ round(($type->total / $maxEventType) * 100) }}%"></span></div>
            </div>
          @empty
            <div class="rowx">Nenhum webhook recebido ainda.</div>
          @endforelse
        </div>
      </div>

      <div class="card">
        <div class="kicker">Atalhos por area</div>
        <div class="quick-groups">
          @foreach ($quickGroups as $group => $links)
            <div>
              <div class="quick-title">{{ $group }}</div>
              <div class="quick-links">
                @foreach ($links as $link)
                  <a href="{{ $link['href'] }}">{{ $link['label'] }}</a>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="wide">
      <div class="card">
        <div class="kicker">Eventos de cobranca recentes</div>
        <div class="stack">
          @forelse($recentBillingEvents as $billingEvent)
            <div class="rowx"><strong>{{ $billingEvent->event_type }} / {{ $billingEvent->status }}</strong><br>recurso {{ $billingEvent->resource_id ?: 'sem recurso' }} - workspace {{ $billingEvent->workspace_id ?: 'nao vinculado' }}</div>
          @empty
            <div class="rowx">Nenhum evento de cobranca recebido ainda.</div>
          @endforelse
        </div>
      </div>
      <div class="card">
        <div class="kicker">Suporte recente</div>
        <div class="stack">
          @forelse($recentTickets as $ticket)
            <div class="rowx"><strong>{{ $ticket->subject }}</strong><br>{{ $ticket->status }} - {{ $ticket->priority }}</div>
          @empty
            <div class="rowx">Nenhum ticket recente.</div>
          @endforelse
        </div>
      </div>
    </section>
  </div>
</x-filament-panels::page>
