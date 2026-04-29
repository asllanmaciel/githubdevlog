@php
  $usersCount = \App\Models\User::count();
  $workspacesCount = \App\Models\Workspace::count();
  $eventsCount = \App\Models\WebhookEvent::count();
  $validEvents = \App\Models\WebhookEvent::where('signature_valid', true)->count();
  $ticketsOpen = \App\Models\SupportTicket::where('status', 'open')->count();
  $plansCount = \App\Models\BillingPlan::count();
  $activeSubscriptions = \App\Models\WorkspaceSubscription::where('status', 'active')->with('plan')->get();
  $pendingSubscriptions = \App\Models\WorkspaceSubscription::where('status', 'pending')->count();
  $canceledSubscriptions = \App\Models\WorkspaceSubscription::where('status', 'canceled')->count();
  $mrrCents = $activeSubscriptions->sum(fn ($subscription) => (int) ($subscription->plan?->price_cents ?? 0));
  $mrr = 'R$ '.number_format($mrrCents / 100, 2, ',', '.');
  $billingEventsToday = \App\Models\BillingEvent::whereDate('created_at', now()->toDateString())->count();
  $billingRisk = \App\Models\BillingEvent::whereIn('status', ['pending_lookup', 'unmatched'])->count();
  $recentBillingEvents = \App\Models\BillingEvent::latest()->limit(5)->get();
  $roadmap = \App\Models\RoadmapItem::all();
  $roadmapDone = $roadmap->where('status', 'done')->count();
  $roadmapPercent = round(($roadmapDone / max($roadmap->count(), 1)) * 100);
  $eventTypes = \App\Models\WebhookEvent::selectRaw('event_name, count(*) as total')->groupBy('event_name')->orderByDesc('total')->limit(6)->get();
  $maxEventType = max((int) ($eventTypes->max('total') ?? 1), 1);
  $recentTickets = \App\Models\SupportTicket::latest()->limit(5)->get();
@endphp

<x-filament-panels::page>
  <style>
    .devlog-admin-home{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--panel:#101720;--blue:#50b8ff;--green:#69e39a;--yellow:#ffcf66;color:var(--ink)}
    .home-hero{display:grid;grid-template-columns:1.15fr .85fr;gap:16px;margin-bottom:16px}
    .float-card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18);position:relative;overflow:hidden}
    .float-card:after{content:"";position:absolute;right:-46px;top:-46px;width:140px;height:140px;border-radius:50%;background:rgba(80,184,255,.12)}
    .float-card>*{position:relative}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}
    .title{font-size:clamp(30px,4vw,54px);line-height:.98;letter-spacing:-.055em;font-weight:950;margin:0;color:var(--ink)}
    .lead{color:var(--muted);font-size:16px;line-height:1.65;margin:12px 0 0}
    .metric-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}
    .finance-grid{display:grid;grid-template-columns:1.25fr repeat(3,1fr);gap:12px;margin-bottom:16px}
    .metric{border:1px solid var(--line);border-radius:16px;background:linear-gradient(180deg,rgba(16,23,32,.92),rgba(12,18,25,.88));padding:16px;min-height:112px;box-shadow:0 18px 45px rgba(0,0,0,.14)}
    .metric-value{font-size:34px;font-weight:950;letter-spacing:-.05em;color:var(--ink)}.metric-label{color:var(--muted);font-size:13px}
    .bar{height:10px;border-radius:999px;background:#0b1118;border:1px solid var(--line);overflow:hidden}.bar span{display:block;height:100%;background:linear-gradient(90deg,var(--blue),var(--green));border-radius:999px}
    .main-grid{display:grid;grid-template-columns:1fr 360px;gap:16px}.list{display:grid;gap:10px}.rowx{border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:12px;color:var(--muted);font-size:13px}
    .rowx strong{color:var(--ink)}.quick{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:16px}.quick a{border:1px solid var(--line);border-radius:12px;padding:12px;background:#0b1118;color:var(--ink);text-decoration:none;font-weight:850}.quick a.primary{background:var(--blue);border-color:var(--blue);color:#071018}
    .risk{color:var(--yellow)}.ok{color:var(--green)}
    @media(max-width:1100px){.home-hero,.main-grid{grid-template-columns:1fr}.metric-grid,.finance-grid{grid-template-columns:repeat(2,1fr)}.quick{grid-template-columns:1fr}}
    @media(max-width:720px){.metric-grid,.finance-grid{grid-template-columns:1fr}}
  </style>

  <div class="devlog-admin-home">
    <section class="home-hero">
      <div class="float-card">
        <div class="kicker">Admin / Operacao</div>
        <h1 class="title">Centro de controle do DevLog AI.</h1>
        <p class="lead">Acompanhe saude do SaaS, uso, cobranca, roadmap, suporte e preparo para lancamento em uma visao executiva.</p>
        <div class="quick">
          <a class="primary" href="{{ url('/admin/roadmap') }}">Roadmap visual</a>
          <a href="{{ url('/admin/launch-center') }}">Centro de lancamento</a>
          <a href="{{ url('/admin/docs') }}">Docs admin</a>
          <a href="{{ url('/admin/deploy-runbook') }}">Runbook deploy</a>
          <a href="{{ url('/admin/system-status') }}">Status sistema</a>
          <a href="{{ url('/admin/security-center') }}">Seguranca</a>
          <a href="{{ url('/admin/knowledge-base-articles') }}">Base conhecimento</a>
          <a href="{{ url('/admin/github-readiness') }}">Prontidao GitHub</a>
          <a href="{{ url('/admin/support-tickets') }}">Suporte</a>
          <a href="{{ url('/admin/workspace-subscriptions') }}">Assinaturas</a>
          <a href="{{ url('/admin/billing-events') }}">Eventos de cobranca</a>
        </div>
      </div>
      <div class="float-card">
        <div class="kicker">Lancamento</div>
        <div class="metric-value">{{ $roadmapPercent }}%</div>
        <div class="metric-label">{{ $roadmapDone }} de {{ $roadmap->count() }} itens concluidos no roadmap</div>
        <div class="bar" style="margin-top:18px"><span style="width:{{ $roadmapPercent }}%"></span></div>
      </div>
    </section>

    <section class="finance-grid">
      <div class="metric"><div class="metric-value">{{ $mrr }}</div><div class="metric-label">MRR estimado em assinaturas ativas</div></div>
      <div class="metric"><div class="metric-value">{{ $activeSubscriptions->count() }}</div><div class="metric-label">assinaturas ativas</div></div>
      <div class="metric"><div class="metric-value">{{ $pendingSubscriptions }}</div><div class="metric-label">assinaturas pendentes</div></div>
      <div class="metric"><div class="metric-value {{ $billingRisk > 0 ? 'risk' : 'ok' }}">{{ $billingRisk }}</div><div class="metric-label">eventos de cobranca com atencao</div></div>
    </section>

    <section class="metric-grid">
      <div class="metric"><div class="metric-value">{{ $usersCount }}</div><div class="metric-label">usuarios</div></div>
      <div class="metric"><div class="metric-value">{{ $workspacesCount }}</div><div class="metric-label">workspaces</div></div>
      <div class="metric"><div class="metric-value">{{ $eventsCount }}</div><div class="metric-label">webhooks recebidos</div></div>
      <div class="metric"><div class="metric-value">{{ $billingEventsToday }}</div><div class="metric-label">eventos de cobranca hoje</div></div>
    </section>

    <section class="main-grid">
      <div class="float-card">
        <div class="kicker">Eventos por tipo</div>
        <div class="list">
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
      <aside class="float-card">
        <div class="kicker">Sinais operacionais</div>
        <div class="list">
          <div class="rowx"><strong>{{ $validEvents }}</strong><br>eventos com assinatura valida</div>
          <div class="rowx"><strong>{{ $plansCount }}</strong><br>planos comerciais cadastrados</div>
          <div class="rowx"><strong>{{ $ticketsOpen }}</strong><br>chamados abertos</div>
          <div class="rowx"><strong>{{ $canceledSubscriptions }}</strong><br>assinaturas canceladas</div>
        </div>
      </aside>
    </section>

    <section class="main-grid" style="margin-top:16px">
      <div class="float-card">
        <div class="kicker">Eventos de cobranca recentes</div>
        <div class="list">
          @forelse($recentBillingEvents as $billingEvent)
            <div class="rowx"><strong>{{ $billingEvent->event_type }} / {{ $billingEvent->status }}</strong><br>recurso {{ $billingEvent->resource_id ?: 'sem recurso' }} · workspace {{ $billingEvent->workspace_id ?: 'nao vinculado' }}</div>
          @empty
            <div class="rowx">Nenhum evento de cobranca recebido ainda.</div>
          @endforelse
        </div>
      </div>
      <aside class="float-card">
        <div class="kicker">Suporte recente</div>
        <div class="list">
          @forelse($recentTickets as $ticket)
            <div class="rowx"><strong>{{ $ticket->subject }}</strong><br>{{ $ticket->status }} · {{ $ticket->priority }}</div>
          @empty
            <div class="rowx">Nenhum ticket recente.</div>
          @endforelse
        </div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
