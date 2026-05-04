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
    ['title' => 'Deployar painel Marketplace', 'detail' => 'Levar /admin/github-marketplace para producao e revisar a listagem do App.', 'href' => url('/admin/github-marketplace'), 'level' => 'primary', 'icon' => 'market'],
    ['title' => 'Rotacionar secrets expostos em prints', 'detail' => 'Trocar secret do GitHub App se algum valor apareceu em evidencia, chat ou video.', 'href' => url('/admin/security-center'), 'level' => 'danger', 'icon' => 'key'],
    ['title' => 'Rodar teste real pos-deploy', 'detail' => 'Gerar um push e confirmar evento novo no inbox, detalhe e hardening.', 'href' => url('/dashboard/events'), 'level' => 'normal', 'icon' => 'pulse'],
    ['title' => 'Preparar mensagem de beta', 'detail' => 'Divulgar como beta para devs que auditam e depuram webhooks GitHub.', 'href' => url('/admin/github-submission'), 'level' => 'normal', 'icon' => 'spark'],
  ]);

  $quickGroups = [
    'Go-live' => [
      ['label' => 'Launch gate', 'href' => url('/admin/launch-gate'), 'icon' => 'rocket'],
      ['label' => 'Prontidao GitHub', 'href' => url('/admin/github-readiness'), 'icon' => 'github'],
      ['label' => 'Marketplace', 'href' => url('/admin/github-marketplace'), 'icon' => 'market'],
      ['label' => 'Submissao GitHub', 'href' => url('/admin/github-submission'), 'icon' => 'check'],
    ],
    'Operacao' => [
      ['label' => 'Status sistema', 'href' => url('/admin/system-status'), 'icon' => 'pulse'],
      ['label' => 'Seguranca', 'href' => url('/admin/security-center'), 'icon' => 'shield'],
      ['label' => 'Hardening webhooks', 'href' => url('/admin/webhook-hardening'), 'icon' => 'check'],
      ['label' => 'Eventos webhook', 'href' => url('/admin/webhook-events'), 'icon' => 'activity'],
    ],
    'SaaS' => [
      ['label' => 'Assinaturas', 'href' => url('/admin/workspace-subscriptions'), 'icon' => 'market'],
      ['label' => 'Eventos cobranca', 'href' => url('/admin/billing-events'), 'icon' => 'activity'],
      ['label' => 'Suporte', 'href' => url('/admin/support-tickets'), 'icon' => 'spark'],
      ['label' => 'Roadmap', 'href' => url('/admin/roadmap'), 'icon' => 'rocket'],
    ],
    'Publico' => [
      ['label' => 'Pagina GitHub', 'href' => url('/github'), 'icon' => 'github'],
      ['label' => 'Contato', 'href' => url('/contact'), 'icon' => 'spark'],
      ['label' => 'Privacidade', 'href' => url('/privacy'), 'icon' => 'shield'],
      ['label' => 'Termos', 'href' => url('/terms'), 'icon' => 'check'],
    ],
  ];

  $icons = [
    'rocket' => '<svg viewBox="0 0 24 24"><path d="M4.5 16.5c-1 1.2-1.5 2.6-1.5 4.5 1.9 0 3.3-.5 4.5-1.5"></path><path d="M9 15 4 20"></path><path d="m14.5 4.5 5 5"></path><path d="M15 3c2.3.2 4.2 1.1 6 3-1.9 4.8-4.8 8.5-9 11l-5-5c2.5-4.2 6.2-7.1 11-9Z"></path><path d="M9 12H5l-2 4 5 1"></path><path d="M12 15v4l4 2 1-5"></path></svg>',
    'github' => '<svg viewBox="0 0 24 24"><path d="M12 2a10 10 0 0 0-3.2 19.5c.5.1.7-.2.7-.5v-1.8c-2.9.6-3.5-1.2-3.5-1.2-.5-1.2-1.2-1.5-1.2-1.5-1-.7.1-.7.1-.7 1.1.1 1.7 1.1 1.7 1.1 1 .1.6 2.4 1.7 3 .8-.6.1-.4.4-1.1-2.3-.3-4.7-1.1-4.7-5A3.9 3.9 0 0 1 5 8.2c-.1-.3-.5-1.4.1-2.8 0 0 .9-.3 2.9 1.1a9.8 9.8 0 0 1 5.3 0c2-1.4 2.9-1.1 2.9-1.1.6 1.4.2 2.5.1 2.8a3.9 3.9 0 0 1 1.1 2.7c0 3.9-2.4 4.8-4.7 5 .4.3.8 1 .8 2v3c0 .3.2.6.8.5A10 10 0 0 0 12 2Z"></path></svg>',
    'shield' => '<svg viewBox="0 0 24 24"><path d="M12 3 5 6v5c0 4.6 2.8 8.7 7 10 4.2-1.3 7-5.4 7-10V6l-7-3Z"></path><path d="m9 12 2 2 4-5"></path></svg>',
    'pulse' => '<svg viewBox="0 0 24 24"><path d="M3 12h4l2-7 4 14 2-7h6"></path></svg>',
    'spark' => '<svg viewBox="0 0 24 24"><path d="M12 3l1.8 5.2L19 10l-5.2 1.8L12 17l-1.8-5.2L5 10l5.2-1.8L12 3Z"></path><path d="M19 15l.8 2.2L22 18l-2.2.8L19 21l-.8-2.2L16 18l2.2-.8L19 15Z"></path></svg>',
    'key' => '<svg viewBox="0 0 24 24"><path d="M14 7a5 5 0 1 0 3 9l4-4-2-2 2-2-2-2-3 3"></path><path d="M7 14h.01"></path></svg>',
    'check' => '<svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"></path></svg>',
    'activity' => '<svg viewBox="0 0 24 24"><path d="M4 19V5"></path><path d="M4 19h16"></path><path d="M8 16v-5"></path><path d="M12 16V8"></path><path d="M16 16v-3"></path></svg>',
    'market' => '<svg viewBox="0 0 24 24"><path d="M6 7h12l1 14H5L6 7Z"></path><path d="M9 7a3 3 0 0 1 6 0"></path><path d="M9 11h.01"></path><path d="M15 11h.01"></path></svg>',
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
    .icon{width:38px;height:38px;border-radius:14px;display:grid;place-items:center;border:1px solid rgba(80,184,255,.34);background:rgba(80,184,255,.1);color:#b7e4ff;margin-bottom:12px;flex:none}.icon svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}.icon.green{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.1);color:var(--green)}.icon.yellow{border-color:rgba(255,207,102,.38);background:rgba(255,207,102,.1);color:var(--yellow)}.icon.red{border-color:rgba(255,107,107,.38);background:rgba(255,107,107,.1);color:var(--danger)}
    .mini-icon{width:30px;height:30px;border-radius:11px;display:grid;place-items:center;border:1px solid rgba(80,184,255,.28);background:rgba(80,184,255,.08);color:#b7e4ff;flex:none}.mini-icon svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:1.9;stroke-linecap:round;stroke-linejoin:round}.mini-icon.green{color:var(--green);border-color:rgba(105,227,154,.35);background:rgba(105,227,154,.08)}.mini-icon.yellow{color:var(--yellow);border-color:rgba(255,207,102,.35);background:rgba(255,207,102,.08)}.mini-icon.red{color:var(--danger);border-color:rgba(255,107,107,.35);background:rgba(255,107,107,.08)}
    .pill-row{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}.pill{border:1px solid var(--line);border-radius:999px;padding:7px 10px;color:var(--muted);font-size:12px;font-weight:850}.pill.ok{color:var(--green);border-color:rgba(105,227,154,.4);background:rgba(105,227,154,.08)}.pill.warn{color:var(--yellow);border-color:rgba(255,207,102,.42);background:rgba(255,207,102,.08)}
    .metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}.metric-value{font-size:34px;font-weight:950;letter-spacing:-.05em;color:var(--ink)}.metric-label{color:var(--muted);font-size:13px;line-height:1.45}.ok{color:var(--green)}.risk{color:var(--yellow)}.bad{color:var(--danger)}
    .layout{display:grid;grid-template-columns:1fr 390px;gap:16px;margin-bottom:16px}.wide{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}.stack{display:grid;gap:10px}
    .action{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:13px;text-decoration:none;color:var(--ink);display:flex;align-items:flex-start;gap:12px}.action strong{display:block;margin-bottom:5px}.action span{color:var(--muted);font-size:13px;line-height:1.5}.action.primary{border-color:rgba(80,184,255,.48);background:rgba(80,184,255,.09)}.action.danger{border-color:rgba(255,107,107,.45);background:rgba(255,107,107,.08)}
    .bar{height:10px;border-radius:999px;background:#0b1118;border:1px solid var(--line);overflow:hidden}.bar span{display:block;height:100%;background:linear-gradient(90deg,var(--blue),var(--green));border-radius:999px}
    .rowx{border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:12px;color:var(--muted);font-size:13px;display:flex;align-items:flex-start;gap:10px}.rowx strong{color:var(--ink)}
    .quick-groups{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}.quick-title{color:var(--green);font-size:12px;text-transform:uppercase;letter-spacing:.12em;font-weight:950;margin-bottom:8px}.quick-links{display:grid;gap:8px}.quick-links a{border:1px solid var(--line);border-radius:12px;padding:10px;background:#0b1118;color:var(--ink);text-decoration:none;font-weight:850;font-size:13px;display:flex;align-items:center;gap:9px}
    @media(max-width:1200px){.hero,.layout,.wide{grid-template-columns:1fr}.metrics,.quick-groups{grid-template-columns:repeat(2,1fr)}}
    @media(max-width:720px){.metrics,.quick-groups{grid-template-columns:1fr}}
  </style>

  <div class="admin-cockpit">
    <section class="hero">
      <div>
        <div class="icon green" aria-hidden="true">{!! $icons['rocket'] !!}</div>
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
        <div class="icon green" aria-hidden="true">{!! $icons['check'] !!}</div>
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
      <div class="metric"><div class="icon green" aria-hidden="true">{!! $icons['rocket'] !!}</div><div class="kicker">Launch</div><div class="metric-value ok">{{ $launch['percent'] }}%</div><div class="metric-label">{{ $launch['done'] }} de {{ $launch['total'] }} checks prontos</div></div>
      <div class="metric"><div class="icon green" aria-hidden="true">{!! $icons['github'] !!}</div><div class="kicker">GitHub</div><div class="metric-value ok">{{ $githubProgram['percent'] }}%</div><div class="metric-label">Developer Program, App e evidencias</div></div>
      <div class="metric"><div class="icon green" aria-hidden="true">{!! $icons['pulse'] !!}</div><div class="kicker">Webhooks</div><div class="metric-value ok">{{ $hardening['accepted'] }}/{{ $hardening['total'] }}</div><div class="metric-label">{{ $hardening['rejected'] }} rejeitado(s), {{ $hardening['valid_rate'] }}% validos</div></div>
      <div class="metric"><div class="icon {{ $security['percent'] >= 90 ? 'green' : 'yellow' }}" aria-hidden="true">{!! $icons['shield'] !!}</div><div class="kicker">Seguranca</div><div class="metric-value {{ $security['percent'] >= 90 ? 'ok' : 'risk' }}">{{ $security['percent'] }}%</div><div class="metric-label">{{ $security['done'] }} de {{ $security['total'] }} checks prontos</div></div>
    </section>

    <section class="metrics">
      <div class="metric"><div class="icon" aria-hidden="true">{!! $icons['activity'] !!}</div><div class="metric-value">{{ $eventsCount }}</div><div class="metric-label">webhooks recebidos, {{ $validEvents }} validos</div></div>
      <div class="metric"><div class="icon" aria-hidden="true">{!! $icons['github'] !!}</div><div class="metric-value">{{ $installationsCount }}</div><div class="metric-label">instalacoes GitHub App vinculadas</div></div>
      <div class="metric"><div class="icon" aria-hidden="true">{!! $icons['spark'] !!}</div><div class="metric-value">{{ $workspacesCount }}</div><div class="metric-label">workspaces, {{ $usersCount }} usuario(s)</div></div>
      <div class="metric"><div class="icon" aria-hidden="true">{!! $icons['rocket'] !!}</div><div class="metric-value">{{ $roadmapPercent }}%</div><div class="metric-label">{{ $roadmapDone }} de {{ $roadmap->count() }} itens do roadmap</div></div>
    </section>

    <section class="layout">
      <div class="card">
        <div class="icon yellow" aria-hidden="true">{!! $icons['spark'] !!}</div>
        <div class="kicker">Proximas acoes de maior impacto</div>
        <div class="stack">
          @foreach ($nextActions as $action)
            <a class="action {{ $action['level'] }}" href="{{ $action['href'] }}">
              <span class="mini-icon {{ $action['level'] === 'danger' ? 'red' : ($action['level'] === 'primary' ? 'green' : '') }}" aria-hidden="true">{!! $icons[$action['icon']] !!}</span>
              <span><strong>{{ $action['title'] }}</strong><span>{{ $action['detail'] }}</span></span>
            </a>
          @endforeach
        </div>
      </div>

      <aside class="card">
        <div class="icon" aria-hidden="true">{!! $icons['activity'] !!}</div>
        <div class="kicker">Sinais SaaS</div>
        <div class="stack">
          <div class="rowx"><span class="mini-icon green" aria-hidden="true">{!! $icons['market'] !!}</span><span><strong>{{ $mrr }}</strong><br>MRR estimado em assinaturas ativas</span></div>
          <div class="rowx"><span class="mini-icon" aria-hidden="true">{!! $icons['check'] !!}</span><span><strong>{{ $activeSubscriptions->count() }}</strong><br>assinaturas ativas; {{ $pendingSubscriptions }} pendente(s)</span></div>
          <div class="rowx"><span class="mini-icon {{ $billingRisk > 0 ? 'yellow' : 'green' }}" aria-hidden="true">{!! $icons['pulse'] !!}</span><span><strong class="{{ $billingRisk > 0 ? 'risk' : 'ok' }}">{{ $billingRisk }}</strong><br>eventos de cobranca com atencao</span></div>
          <div class="rowx"><span class="mini-icon" aria-hidden="true">{!! $icons['spark'] !!}</span><span><strong>{{ $ticketsOpen }}</strong><br>chamados abertos; {{ $canceledSubscriptions }} assinatura(s) cancelada(s)</span></div>
        </div>
      </aside>
    </section>

    <section class="wide">
      <div class="card">
        <div class="icon" aria-hidden="true">{!! $icons['activity'] !!}</div>
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
        <div class="icon" aria-hidden="true">{!! $icons['market'] !!}</div>
        <div class="kicker">Atalhos por area</div>
        <div class="quick-groups">
          @foreach ($quickGroups as $group => $links)
            <div>
              <div class="quick-title">{{ $group }}</div>
              <div class="quick-links">
                @foreach ($links as $link)
                  <a href="{{ $link['href'] }}"><span class="mini-icon" aria-hidden="true">{!! $icons[$link['icon']] !!}</span><span>{{ $link['label'] }}</span></a>
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
            <div class="rowx"><span class="mini-icon" aria-hidden="true">{!! $icons['activity'] !!}</span><span><strong>{{ $billingEvent->event_type }} / {{ $billingEvent->status }}</strong><br>recurso {{ $billingEvent->resource_id ?: 'sem recurso' }} - workspace {{ $billingEvent->workspace_id ?: 'nao vinculado' }}</span></div>
          @empty
            <div class="rowx">Nenhum evento de cobranca recebido ainda.</div>
          @endforelse
        </div>
      </div>
      <div class="card">
        <div class="kicker">Suporte recente</div>
        <div class="stack">
          @forelse($recentTickets as $ticket)
            <div class="rowx"><span class="mini-icon" aria-hidden="true">{!! $icons['spark'] !!}</span><span><strong>{{ $ticket->subject }}</strong><br>{{ $ticket->status }} - {{ $ticket->priority }}</span></div>
          @empty
            <div class="rowx">Nenhum ticket recente.</div>
          @endforelse
        </div>
      </div>
    </section>
  </div>
</x-filament-panels::page>
