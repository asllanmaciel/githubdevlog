<x-layout title="Dashboard | GitHub DevLog AI">
@php
    use App\Models\BillingPlan;
    use App\Support\WorkspaceUsage;

    $endpoint = $workspace ? url('/webhooks/github/'.$workspace->uuid) : null;
    $availablePlans = BillingPlan::where('active', true)->orderBy('price_cents')->get();
    $mercadoPagoStatus = app(\App\Services\MercadoPagoBillingService::class)->checkoutStatus();
    $totalEvents = $events->count();
    $validEvents = $events->where('signature_valid', true)->count();
    $invalidEvents = $events->where('signature_valid', false)->count();
    $validationRate = $totalEvents > 0 ? round(($validEvents / $totalEvents) * 100) : 0;
    $repos = $events->map(fn ($event) => data_get($event->payload, 'repository.full_name'))->filter()->unique()->count();
    $pushes = $events->where('event_name', 'push')->count();
    $lastEvent = $events->first();
    $latestRepo = $lastEvent ? data_get($lastEvent->payload, 'repository.full_name', 'Aguardando primeiro evento') : 'Aguardando primeiro evento';
    $latestSender = $lastEvent ? data_get($lastEvent->payload, 'sender.login', data_get($lastEvent->payload, 'pusher.name', 'GitHub')) : 'GitHub';
    $eventTypes = $events->groupBy('event_name')->map->count()->sortDesc();
    $maxType = max($eventTypes->max() ?? 1, 1);
    $eventIds = $events->pluck('id');
    $openTasks = \App\Models\WebhookEventTask::whereIn('webhook_event_id', $eventIds)->where('status', 'open')->count();
    $notesCount = \App\Models\WebhookEventNote::whereIn('webhook_event_id', $eventIds)->count();
    $subscription = $workspace?->subscription()->with('plan')->first();
    $usageReport = $workspace ? WorkspaceUsage::report($workspace) : null;
    $plan = $usageReport['plan'] ?? BillingPlan::where('slug', 'free')->first();
    $monthlyEvents = $usageReport['usage'] ?? 0;
    $monthlyLimit = $usageReport['limit'] ?? 1000;
    $remainingEvents = $usageReport['remaining'] ?? max($monthlyLimit - $monthlyEvents, 0);
    $usagePercent = $usageReport['percent'] ?? 0;
    $usageStateClass = $usagePercent >= 100 ? 'danger' : ($usagePercent >= 80 ? 'warn' : '');
    $usageStateLabel = $usagePercent >= 100 ? 'Limite atingido' : ($usagePercent >= 80 ? 'Perto do limite' : 'Uso saudavel');
    $periodStart = $usageReport['period_start'] ?? now()->startOfMonth();
    $periodEnd = $usageReport['period_end'] ?? now()->endOfMonth();
    $retentionDays = (int) ($plan?->event_retention_days ?? 30);
    $overagePrice = ((int) ($plan?->overage_price_cents ?? 0)) / 100;
    $planName = $plan?->name ?? 'Free';
    $subscriptionStatus = $subscription?->status ?? 'trialing';
    $subscriptionStatusLabel = [
        'trialing' => 'Trial',
        'pending' => 'Pagamento pendente',
        'active' => 'Ativa',
        'past_due' => 'Em atraso',
        'canceled' => 'Cancelada',
    ][$subscriptionStatus] ?? ucfirst($subscriptionStatus);
    $subscriptionEndsAt = $subscription?->current_period_ends_at;
    $subscriptionProviderReference = $subscription?->provider_reference ?: 'Ainda nao gerada';
    $canUseWebhooks = in_array($subscriptionStatus, ['trialing', 'active', 'pending'], true);
    $healthStatus = $totalEvents === 0 ? 'Aguardando evento' : ($invalidEvents > 0 ? 'Atencao' : 'Saudavel');
    $healthClass = $invalidEvents > 0 ? 'status-warn' : 'status-ok';
    $usageWarning = $usagePercent >= 100
        ? 'Limite mensal atingido. Novos webhooks serao recusados ate upgrade ou renovacao.'
        : ($usagePercent >= 95
            ? 'Uso mensal em nivel critico. Faca upgrade antes de perder eventos importantes.'
            : ($usagePercent >= 80 ? 'Uso mensal proximo do limite. Considere upgrade antes de perder eventos importantes.' : null));
    $unreadNotifications = $notifications->whereNull('read_at')->count();
@endphp

@if (! $workspace)
  <main class="hero">
    <span class="eyebrow">Workspace pendente</span>
    <h1>Seu usuario ainda nao esta vinculado a um workspace.</h1>
    <p class="lead">Crie um workspace ou peca acesso ao responsavel para comecar a receber webhooks privados do GitHub.</p>
  </main>
@else
  <main>
    <section class="dashboard-hero">
      <div class="cardx">
        <div class="kicker">Painel do workspace</div>
        <h1 class="dashboard-title">Controle dos webhooks do {{ $workspace->name }}</h1>
        <p class="lead mt-3 mb-0">Monitore entregas do GitHub, valide assinatura, acompanhe payloads, registre notas e transforme eventos em tarefas de investigacao.</p>
        <div class="control-strip">
          <div class="control-card">
            <div class="control-label">Plano atual</div>
            <div class="control-value">{{ $planName }}</div>
          </div>
          <div class="control-card">
            <div class="control-label">Uso mensal</div>
            <div class="control-value">{{ number_format($monthlyEvents, 0, ',', '.') }} / {{ number_format($monthlyLimit, 0, ',', '.') }}</div>
          </div>
          <div class="control-card">
            <div class="control-label">Restantes</div>
            <div class="control-value">{{ number_format($remainingEvents, 0, ',', '.') }}</div>
          </div>
          <div class="control-card">
            <div class="control-label">Retencao</div>
            <div class="control-value">{{ $retentionDays }} dias</div>
          </div>
          <div class="control-card">
            <div class="control-label">Assinatura</div>
            <div class="control-value">{{ $subscriptionStatusLabel }}</div>
          </div>
        </div>
      </div>

      <aside class="cardx health-panel">
        <div class="d-flex justify-content-between gap-3 align-items-start">
          <div>
            <div class="kicker">Saude do workspace</div>
            <h2 class="h3 mt-2 mb-1 {{ $healthClass }}">{{ $healthStatus }}</h2>
            <p class="muted mb-0">{{ $validationRate }}% dos eventos recentes chegaram com assinatura valida.</p>
          </div>
          <div class="health-orb">{{ $validationRate }}%</div>
        </div>
        <div class="mt-4">
          <div class="d-flex justify-content-between mb-2"><span class="muted">Limite mensal usado</span><strong>{{ $usagePercent }}%</strong></div>
          <div class="bar-track"><span class="bar-fill {{ $usageStateClass }}" style="width: {{ $usagePercent }}%"></span></div>
          <div class="muted mt-2">{{ $usageStateLabel }} · {{ number_format($remainingEvents, 0, ',', '.') }} evento(s) restantes · assinatura {{ $subscriptionStatusLabel }}</div>
        </div>
      </aside>
    </section>

    <section class="metric-grid">
      <div class="metric"><div class="metric-label">Eventos recentes</div><div class="metric-value">{{ $totalEvents }}</div><div class="spark"><span style="height:40%"></span><span style="height:70%"></span><span style="height:55%"></span><span style="height:90%"></span></div></div>
      <div class="metric"><div class="metric-label">Repositorios vistos</div><div class="metric-value">{{ $repos }}</div><div class="muted mt-2">Origem mais recente: {{ $latestRepo }}</div></div>
      <div class="metric"><div class="metric-label">Push recebidos</div><div class="metric-value">{{ $pushes }}</div><div class="muted mt-2">Sender recente: {{ $latestSender }}</div></div>
      <div class="metric"><div class="metric-label">Notas e tarefas</div><div class="metric-value">{{ $notesCount + $openTasks }}</div><div class="muted mt-2">{{ $openTasks }} tarefa(s) aberta(s)</div></div>
    </section>

    <section class="cardx mb-3" id="equipe">
      <div class="d-flex justify-content-between gap-3 flex-wrap align-items-start">
        <div>
          <div class="kicker">Equipe do workspace</div>
          <h2 class="h4 mt-2 mb-1">Acesso privado, compartilhado com controle</h2>
          <p class="muted mb-0">Convide devs do time para acompanhar webhooks, abrir tarefas e investigar entregas sem compartilhar senha ou secret fora do workspace.</p>
          <div class="muted mt-2">Owner/admin gerenciam equipe, billing, secrets e GitHub App. Developer investiga eventos. Viewer acompanha em leitura.</div>
        </div>
        <span class="pill">Seu papel: {{ $workspaceRole ?? 'membro' }}</span>
      </div>
      <div class="row g-3 mt-2">
        <div class="col-lg-7">
          <div class="summary-cell h-100">
            <div class="summary-label">Membros atuais</div>
            @forelse ($members as $member)
              <div class="d-flex justify-content-between gap-2 align-items-center mt-2">
                <div>
                  <strong>{{ $member->user?->name ?? 'Usuario removido' }}</strong>
                  <div class="muted">{{ $member->user?->email ?? 'sem email' }} · {{ $member->role }}</div>
                </div>
                @if ($canManageWorkspace && $member->role !== 'owner' && $member->user_id !== auth()->id())
                  <form method="POST" action="{{ route('workspace.members.remove', $member) }}">
                    @csrf
                    <button class="btnx" type="submit">Remover</button>
                  </form>
                @endif
              </div>
            @empty
              <div class="muted mt-2">Nenhum membro vinculado ainda.</div>
            @endforelse
          </div>
        </div>
        <div class="col-lg-5">
          <div class="summary-cell h-100">
            <div class="summary-label">Convidar dev</div>
            @if ($canManageWorkspace)
              <form method="POST" action="{{ route('workspace.members.invite') }}" class="mt-2">
                @csrf
                <label>Email</label>
                <input name="email" type="email" required placeholder="dev@empresa.com">
                <label class="mt-2">Papel</label>
                <select name="role" required>
                  <option value="developer">Developer</option>
                  <option value="admin">Admin</option>
                  <option value="viewer">Viewer</option>
                </select>
                <button class="btnx primary w-100 mt-2" type="submit">Adicionar ou criar convite</button>
              </form>
            @else
              <div class="muted mt-2">Somente owner ou admin pode convidar novos membros.</div>
            @endif
          </div>
        </div>
      </div>
      <details class="payload mt-3">
        <summary>Ver matriz de permissoes</summary>
        <div class="row g-2 mt-2">
          @foreach ($permissionMatrix as $role => $matrix)
            <div class="col-md-3">
              <div class="summary-cell h-100">
                <div class="summary-label">{{ $matrix['label'] }}</div>
                @foreach ($permissionLabels as $permission => $label)
                  <div class="muted">{{ $matrix['permissions'][$permission] ?? false ? '✓' : '–' }} {{ $label }}</div>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      </details>
      @if ($invites->where('status', 'pending')->count() > 0)
        <div class="row g-2 mt-2">
          @foreach ($invites->where('status', 'pending') as $invite)
            <div class="col-md-6">
              <div class="summary-cell">
                <div class="d-flex justify-content-between gap-2 align-items-start">
                  <div>
                    <div class="summary-label">Convite pendente</div>
                    <div class="summary-value">{{ $invite->email }}</div>
                    <div class="muted">{{ $invite->role }} · expira {{ $invite->expires_at?->format('d/m/Y') }}</div>
                  </div>
                  @if ($canManageWorkspace)
                    <form method="POST" action="{{ route('workspace.invites.cancel', $invite) }}">
                      @csrf
                      <button class="btnx" type="submit">Cancelar</button>
                    </form>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </section>

    <x-workspace-onboarding
      :workspace="$workspace"
      :endpoint="$endpoint"
      :github-installation="$githubInstallation"
      :total-events="$totalEvents"
      :valid-events="$validEvents"
      :subscription-status-label="$subscriptionStatusLabel"
      :can-use-webhooks="$canUseWebhooks"
    />

    <section class="cardx mb-3" id="billing">
      <div class="d-flex justify-content-between gap-3 flex-wrap align-items-start">
        <div>
          <div class="kicker">Notificacoes do workspace</div>
          <h2 class="h4 mt-2 mb-1">{{ $unreadNotifications }} alerta(s) nao lido(s)</h2>
          <p class="muted mb-0">Acompanhe eventos importantes de billing, GitHub App, limite de uso e webhooks recebidos.</p>
        </div>
        @if ($unreadNotifications > 0)
          <form method="POST" action="{{ route('notifications.read-all') }}">
            @csrf
            <button class="btnx" type="submit">Marcar todas como lidas</button>
          </form>
        @endif
      </div>

      <div class="row g-2 mt-2">
        @forelse ($notifications as $notification)
          <div class="col-md-6">
            <div class="summary-cell h-100" style="{{ $notification->read_at ? 'opacity:.72' : 'border-color:rgba(80,184,255,.55)' }}">
              <div class="d-flex justify-content-between gap-2 align-items-start">
                <div>
                  <div class="summary-label">{{ $notification->type }} · {{ $notification->created_at?->format('d/m/Y H:i') }}</div>
                  <div class="summary-value">{{ $notification->title }}</div>
                </div>
                <span class="pill">{{ $notification->read_at ? 'Lida' : 'Nova' }}</span>
              </div>
              @if ($notification->body)
                <div class="muted mt-2">{{ $notification->body }}</div>
              @endif
              @if (! $notification->read_at)
                <form method="POST" action="{{ route('notifications.read', $notification) }}" class="mt-2">
                  @csrf
                  <button class="btnx w-100" type="submit">Marcar como lida</button>
                </form>
              @endif
            </div>
          </div>
        @empty
          <div class="col-12">
            <div class="summary-cell">Nenhuma notificacao recente. Quando algo importante acontecer, aparece aqui.</div>
          </div>
        @endforelse
      </div>
    </section>

    <section class="mini-board">
      <div class="insight-card">
        <div class="kicker">Distribuicao de eventos</div>
        <h2 class="h4">O que o GitHub esta enviando</h2>
        <div class="insight-list">
          @forelse ($eventTypes as $type => $count)
            <div>
              <div class="d-flex justify-content-between mb-1"><strong>{{ $type }}</strong><span class="muted">{{ $count }}</span></div>
              <div class="bar-track"><span class="bar-fill" style="width: {{ round(($count / $maxType) * 100) }}%"></span></div>
            </div>
          @empty
            <p class="muted mb-0">Nenhum evento ainda. Configure o webhook no GitHub ou salve um evento de teste.</p>
          @endforelse
        </div>
      </div>

      <div class="insight-card">
        <div class="kicker">Proximas acoes</div>
        <h2 class="h4">Operacao guiada</h2>
        <div class="quick-actions">
          <a class="quick-action" href="#setup"><span>Configurar webhook no GitHub</span><span>?</span></a>
          <a class="quick-action" href="#eventos"><span>Investigar eventos recebidos</span><span>></span></a>
          <a class="quick-action" href="{{ route('support') }}"><span>Abrir chamado de suporte</span><span>></span></a>
        </div>
      </div>
    </section>

    <section class="cardx mb-3">
      <div class="d-flex justify-content-between gap-3 flex-wrap align-items-start">
        <div>
          <div class="kicker">Uso e plano do workspace</div>
          <h2 class="h4 mt-2 mb-1">{{ $canUseWebhooks ? 'Seu limite de webhooks esta sob controle' : 'Assinatura precisa de atencao' }}</h2>
          <p class="muted mb-0">Como o pricing nao e protagonista publico nesta fase, o consumo fica claro aqui: plano atual, janela mensal, eventos restantes, retencao e upgrade quando fizer sentido.</p>
        </div>
        <span class="pill {{ $subscriptionStatus === 'active' ? 'status-ok' : ($subscriptionStatus === 'canceled' ? 'status-warn' : '') }}">{{ $subscriptionStatusLabel }}</span>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-md-3">
          <div class="summary-cell h-100">
            <div class="summary-label">Plano</div>
            <div class="summary-value">{{ $planName }}</div>
            <div class="muted mt-1">{{ number_format($monthlyLimit, 0, ',', '.') }} eventos por mes</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="summary-cell h-100">
            <div class="summary-label">Uso no mes</div>
            <div class="summary-value">{{ number_format($monthlyEvents, 0, ',', '.') }} usados</div>
            <div class="bar-track mt-2"><span class="bar-fill {{ $usageStateClass }}" style="width: {{ $usagePercent }}%"></span></div>
            <div class="muted mt-1">{{ $usageStateLabel }} · {{ $usagePercent }}% do limite mensal</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="summary-cell h-100">
            <div class="summary-label">Eventos restantes</div>
            <div class="summary-value">{{ number_format($remainingEvents, 0, ',', '.') }}</div>
            <div class="muted mt-1">Janela: {{ $periodStart->format('d/m') }} a {{ $periodEnd->format('d/m') }}</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="summary-cell h-100">
            <div class="summary-label">Retencao e excedente</div>
            <div class="summary-value">{{ $retentionDays }} dias</div>
            <div class="muted mt-1">{{ $overagePrice > 0 ? 'Excedente configurado: R$ '.number_format($overagePrice, 2, ',', '.') : 'Sem excedente publico nesta fase.' }}</div>
          </div>
        </div>
      </div>
      <div class="summary-cell mt-2">
        <div class="d-flex justify-content-between gap-3 flex-wrap align-items-center">
          <div>
            <div class="summary-label">Referencia de assinatura</div>
            <div class="summary-value" style="font-size:14px;word-break:break-all">{{ $subscriptionProviderReference }}</div>
            <div class="muted mt-1">Periodo atual: {{ $subscriptionEndsAt ? $subscriptionEndsAt->format('d/m/Y') : 'sem vencimento definido' }}.</div>
          </div>
          @if ($canManageBilling)
            <a class="btnx" href="#upgrade">Ver opcoes internas de upgrade</a>
          @endif
        </div>
      </div>
    </section>

    @if ($usageWarning)
      <section class="cardx mb-3" style="border-color: {{ $usagePercent >= 100 ? 'rgba(255,107,107,.5)' : 'rgba(255,209,102,.5)' }}; background: linear-gradient(135deg, rgba(255,209,102,.08), rgba(80,184,255,.05));">
        <div class="d-flex justify-content-between gap-3 flex-wrap align-items-center">
          <div>
            <div class="kicker">Uso e cobranca</div>
            <h2 class="h4 mt-2 mb-1">{{ $usagePercent >= 100 ? 'Limite do plano atingido' : 'Atencao ao consumo mensal' }}</h2>
            <p class="muted mb-0">{{ $usageWarning }}</p>
          </div>
          <a class="btnx primary" href="{{ route('support') }}">Falar com suporte</a>
        </div>
      </section>
    @endif

    <section class="cardx mb-3" id="upgrade">
      <div class="d-flex justify-content-between gap-3 flex-wrap align-items-start">
        <div>
          <div class="kicker">Upgrade interno</div>
          <h2 class="h4 mt-2 mb-1">Aumente limite quando o uso justificar</h2>
          <p class="muted mb-0">Este bloco fica dentro do workspace para o usuario decidir upgrade com base no consumo real. Ambiente Mercado Pago: {{ $mercadoPagoStatus['environment'] }} - SDK: {{ $mercadoPagoStatus['sdk'] }} - Configurado: {{ $mercadoPagoStatus['configured'] ? 'sim' : 'nao' }}</p>
        </div>
        <span class="pill">Plano atual: {{ $planName }}</span>
      </div>
      <div class="row g-2 mt-2">
        @foreach ($availablePlans as $availablePlan)
          <div class="col-md-4">
            <div class="summary-cell h-100">
              <div class="summary-label">{{ $availablePlan->name }}</div>
              <div class="summary-value">R$ {{ number_format($availablePlan->price_cents / 100, 2, ',', '.') }}/mes</div>
              <div class="muted mt-1">{{ number_format($availablePlan->monthly_event_limit, 0, ',', '.') }} eventos/mes - retencao {{ $availablePlan->event_retention_days }} dias</div>
              @if ($availablePlan->price_cents > 0)
                <form method="POST" action="{{ route('billing.checkout', $availablePlan) }}" class="mt-2">
                  @csrf
                  <button class="btnx primary w-100" type="submit">Assinar plano</button>
                </form>
              @else
                <span class="pill mt-2">Plano gratuito</span>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    </section>

    <section class="dashboard-grid">
      <aside id="setup" class="config-card">
        <div class="cardx mb-3">
          <div class="kicker">Configuracao GitHub</div>
          <h2 class="h4 mt-2">Endpoint privado do workspace</h2>
          <p class="muted">Use estes dados em <strong>Settings -> Webhooks -> Add webhook</strong> no repositorio GitHub.</p>
          <div class="summary-cell mb-3">
            <div class="summary-label">GitHub App</div>
            <div class="summary-value">{{ $githubInstallation ? 'Instalacao '.$githubInstallation->installation_id.' vinculada' : 'Nenhuma instalacao vinculada ainda' }}</div>
            @if ($canManageGitHub)
              <a class="btnx primary w-100 mt-2" href="{{ route('github.install') }}">Conectar GitHub App</a>
            @else
              <div class="muted mt-2">Seu papel atual nao permite conectar GitHub App.</div>
            @endif
          </div>
          <label>Payload URL</label>
          <div class="endpoint-box mb-3">{{ $endpoint }}</div>
          <label>Content type</label>
          <pre>application/json</pre>
          <label>Secret</label>
          <pre>{{ $workspace->webhook_secret }}</pre>
          @if ($canManageSecrets)
            <form method="POST" action="{{ route('workspace.secret.rotate') }}">
              @csrf
              <button class="btnx w-100" type="submit">Rotacionar secret</button>
            </form>
          @else
            <div class="muted">Somente owner/admin pode rotacionar o secret.</div>
          @endif
        </div>

        <div class="cardx">
          <div class="kicker">Teste manual</div>
          <h2 class="h5 mt-2">Salvar evento no workspace</h2>
          @if ($canCreateTestEvents)
            <form method="POST" action="{{ route('dashboard.test-event') }}">
              @csrf
              <textarea name="payload" rows="8">{ "event": "push", "repository": { "full_name": "demo/repo" }, "pusher": { "name": "dev" } }</textarea>
              <button class="btnx success w-100 mt-2" type="submit">Salvar evento de teste</button>
            </form>
          @else
            <div class="muted">Seu papel atual permite acompanhar eventos, mas nao criar eventos de teste.</div>
          @endif
        </div>
      </aside>

      <section id="eventos">
        <div class="cardx event-feed-head">
          <div>
            <div class="kicker">Eventos recebidos</div>
            <h2 class="h3 mt-2 mb-0">Historico privado do workspace</h2>
          </div>
          <span class="pill">{{ $totalEvents }} evento(s)</span>
        </div>

        @forelse ($events as $event)
          <x-webhook-event-card :event="$event" />
        @empty
          <div class="cardx">
            <div class="kicker">Primeiro uso</div>
            <h2 class="h3 mt-2">Conecte seu primeiro repositorio GitHub</h2>
            <p class="muted">Este workspace ainda nao recebeu eventos. Siga o fluxo abaixo para validar tudo em poucos minutos.</p>
            <div class="row g-2 mt-2">
              <div class="col-md-4">
                <div class="summary-cell h-100">
                  <div class="summary-label">1. Copie o endpoint</div>
                  <div class="summary-value">Use o Payload URL e o Secret exibidos na coluna de configuracao.</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="summary-cell h-100">
                  <div class="summary-label">2. Configure no GitHub</div>
                  <div class="summary-value">Em Settings -> Webhooks, escolha JSON e cole o Secret do workspace.</div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="summary-cell h-100">
                  <div class="summary-label">3. Envie um ping</div>
                  <div class="summary-value">O evento aparecera aqui com assinatura, payload sanitizado e historico.</div>
                </div>
              </div>
            </div>
          </div>
        @endforelse
      </section>
    </section>
  </main>
@endif
<script>
  document.querySelectorAll('.filter-chip').forEach((button) => {
    button.addEventListener('click', () => {
      const filter = button.dataset.filter;
      document.querySelectorAll('.filter-chip').forEach((item) => item.classList.remove('active'));
      button.classList.add('active');
      document.querySelectorAll('.event-card').forEach((card) => {
        const show = filter === 'all'
          || (filter === 'valid' && card.dataset.signature === 'valid')
          || (filter === 'pending' && card.dataset.signature === 'pending')
          || (filter.startsWith('type:') && card.dataset.eventType === filter.slice(5));
        card.style.display = show ? '' : 'none';
      });
    });
  });
</script>
</x-layout>
