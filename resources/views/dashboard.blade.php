<x-layout title="Dashboard | GitHub DevLog AI">
@php
    $endpoint = $workspace ? url('/webhooks/github/'.$workspace->uuid) : null;
    $availablePlans = \App\Models\BillingPlan::where('active', true)->orderBy('price_cents')->get();
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
    $usageReport = $workspace ? \App\Support\WorkspaceUsage::report($workspace) : null;
    $plan = $usageReport['plan'] ?? \App\Models\BillingPlan::where('slug', 'free')->first();
    $monthlyEvents = $usageReport['usage'] ?? 0;
    $monthlyLimit = $usageReport['limit'] ?? 1000;
    $remainingEvents = $usageReport['remaining'] ?? max($monthlyLimit - $monthlyEvents, 0);
    $usagePercent = $usageReport['percent'] ?? 0;
    $usageStateClass = $usagePercent >= 100 ? 'danger' : ($usagePercent >= 80 ? 'warn' : '');
    $usageStateLabel = $usagePercent >= 100 ? 'Limite atingido' : ($usagePercent >= 80 ? 'Perto do limite' : 'Uso saudável');
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
    $subscriptionProviderReference = $subscription?->provider_reference ?: 'Ainda não gerada';
    $canUseWebhooks = in_array($subscriptionStatus, ['trialing', 'active', 'pending'], true);
    $healthStatus = $totalEvents === 0 ? 'Aguardando evento' : ($invalidEvents > 0 ? 'Atenção' : 'Saudável');
    $healthClass = $invalidEvents > 0 ? 'status-warn' : 'status-ok';
    $usageWarning = $usagePercent >= 100
        ? 'Limite mensal atingido. Novos webhooks serão recusados até upgrade ou renovação.'
        : ($usagePercent >= 95
            ? 'Uso mensal em nível crítico. Faça upgrade antes de perder eventos importantes.'
            : ($usagePercent >= 80 ? 'Uso mensal próximo do limite. Considere upgrade antes de perder eventos importantes.' : null));
    $unreadNotifications = $notifications->whereNull('read_at')->count();
    $aiUsage = $workspace ? \App\Support\AiAnalysisBilling::report($workspace) : null;
    $openAiConfigured = filled(config('services.openai.api_key'));
    $advancedAiPrice = (($aiUsage['overage_price_cents'] ?? 0) / 100);
@endphp

@if (! $workspace)
  <main class="hero">
    <span class="eyebrow">Workspace pendente</span>
    <h1>Seu usuário ainda não está vinculado a um workspace.</h1>
    <p class="lead">Crie um workspace ou peça acesso ao responsável para começar a receber webhooks privados do GitHub.</p>
  </main>
@else
  <main>
    <section class="dashboard-hero">
      <div class="cardx">
        <div class="kicker">Painel do workspace</div>
        <h1 class="dashboard-title">Controle dos webhooks do {{ $workspace->name }}</h1>
        <p class="lead mt-3 mb-0">Monitore entregas do GitHub, valide assinatura, acompanhe payloads, registre notas e transforme eventos em tarefas de investigação.</p>
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
            <div class="control-label">Retenção</div>
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
            <div class="kicker">Saúde do workspace</div>
            <h2 class="h3 mt-2 mb-1 {{ $healthClass }}">{{ $healthStatus }}</h2>
            <p class="muted mb-0">{{ $validationRate }}% dos eventos recentes chegaram com assinatura válida.</p>
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


    <section class="cardx mb-3" id="ai">
      <div class="d-flex justify-content-between gap-3 flex-wrap align-items-start">
        <div>
          <div class="kicker">AI do workspace</div>
          <h2 class="h4 mt-2 mb-1">AI grátis para triagem, AI avançada como recurso pago</h2>
          <p class="muted mb-0">A análise local continua inclusa para todos. A análise com LLM usa provedor externo, tem custo real e por isso é limitada por plano.</p>
        </div>
        <span class="pill">{{ $openAiConfigured ? 'OpenAI configurada' : 'OpenAI pendente' }}</span>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-md-3"><div class="summary-cell h-100"><div class="summary-label">AI grátis</div><div class="summary-value">Ilimitada</div><div class="muted mt-1">Provider local-devlog-ai-v1</div></div></div>
        <div class="col-md-3"><div class="summary-cell h-100"><div class="summary-label">AI avançada no mês</div><div class="summary-value">{{ $aiUsage['usage'] ?? 0 }} / {{ $aiUsage['limit'] ?? 0 }}</div><div class="bar-track mt-2"><span class="bar-fill" style="width: {{ $aiUsage['percent'] ?? 0 }}%"></span></div></div></div>
        <div class="col-md-3"><div class="summary-cell h-100"><div class="summary-label">Restantes</div><div class="summary-value">{{ $aiUsage['remaining'] ?? 0 }}</div><div class="muted mt-1">{{ ($aiUsage['enabled'] ?? false) ? 'Inclusas no plano atual' : 'Faça upgrade para usar LLM' }}</div></div></div>
        <div class="col-md-3"><div class="summary-cell h-100"><div class="summary-label">Custo excedente</div><div class="summary-value">{{ $advancedAiPrice > 0 ? 'R$ '.number_format($advancedAiPrice, 2, ',', '.') : 'Sem excedente' }}</div><div class="muted mt-1">Custo estimado por análise avançada</div></div></div>
      </div>
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
                  <strong>{{ $member->user?->name ?? 'Usuário removido' }}</strong>
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
        <summary>Ver matriz de permissões</summary>
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
          <div class="kicker">Notificações do workspace</div>
          <h2 class="h4 mt-2 mb-1">{{ $unreadNotifications }} alerta(s) não lido(s)</h2>
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
            <div class="summary-cell">Nenhuma notificação recente. Quando algo importante acontecer, aparece aqui.</div>
          </div>
        @endforelse
      </div>
    </section>


    <section class="cardx mb-3" id="launch-checklist">
      <div class="d-flex justify-content-between gap-3 flex-wrap align-items-start">
        <div>
          <div class="kicker">Checklist de ativação</div>
          <h2 class="h4 mt-2 mb-1">O caminho mínimo para usar em produção com segurança</h2>
          <p class="muted mb-0">Este bloco ajuda o dev a sair de “recebi um payload” para “posso confiar nesse fluxo no meu produto”.</p>
        </div>
        <span class="pill">Pré-lançamento</span>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-md-3"><div class="summary-cell h-100"><div class="summary-label">1. Conta e workspace</div><div class="summary-value">{{ $workspace ? 'Pronto' : 'Pendente' }}</div><div class="muted mt-1">Eventos ficam privados por workspace.</div></div></div>
        <div class="col-md-3"><div class="summary-cell h-100"><div class="summary-label">2. GitHub conectado</div><div class="summary-value">{{ $totalEvents > 0 ? 'Validado' : 'Aguardando ping' }}</div><div class="muted mt-1">Configure webhook manual ou GitHub App.</div></div></div>
        <div class="col-md-3"><div class="summary-cell h-100"><div class="summary-label">3. Segurança</div><div class="summary-value">{{ $validEvents > 0 ? 'Assinatura OK' : 'Sem evento validado' }}</div><div class="muted mt-1">Secret e HMAC protegem o endpoint.</div></div></div>
        <div class="col-md-3"><div class="summary-cell h-100"><div class="summary-label">4. Operação</div><div class="summary-value">{{ $unreadNotifications > 0 ? $unreadNotifications.' alerta(s)' : 'Sem alertas' }}</div><div class="muted mt-1">Notas, tarefas, suporte e AI ficam no painel.</div></div></div>
      </div>
    </section>
    <section class="mini-board">
      <div class="insight-card">
        <div class="kicker">Distribuição de eventos</div>
        <h2 class="h4">O que o GitHub está enviando</h2>
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
        <div class="kicker">Próximas ações</div>
        <h2 class="h4">Operação guiada</h2>
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
          <h2 class="h4 mt-2 mb-1">{{ $canUseWebhooks ? 'Seu limite de webhooks está sob controle' : 'Assinatura precisa de atenção' }}</h2>
          <p class="muted mb-0">Como o pricing não é protagonista público nesta fase, o consumo fica claro aqui: plano atual, janela mensal, eventos restantes, retenção e upgrade quando fizer sentido.</p>
        </div>
        <span class="pill {{ $subscriptionStatus === 'active' ? 'status-ok' : ($subscriptionStatus === 'canceled' ? 'status-warn' : '') }}">{{ $subscriptionStatusLabel }}</span>
      </div>
      <div class="row g-2 mt-2">
        <div class="col-md-3">
          <div class="summary-cell h-100">
            <div class="summary-label">Plano</div>
            <div class="summary-value">{{ $planName }}</div>
            <div class="muted mt-1">{{ number_format($monthlyLimit, 0, ',', '.') }} eventos por mês</div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="summary-cell h-100">
            <div class="summary-label">Uso no mês</div>
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
            <div class="summary-label">Retenção e excedente</div>
            <div class="summary-value">{{ $retentionDays }} dias</div>
            <div class="muted mt-1">{{ $overagePrice > 0 ? 'Excedente configurado: R$ '.number_format($overagePrice, 2, ',', '.') : 'Sem excedente público nesta fase.' }}</div>
          </div>
        </div>
      </div>
      <div class="summary-cell mt-2">
        <div class="d-flex justify-content-between gap-3 flex-wrap align-items-center">
          <div>
            <div class="summary-label">Referência de assinatura</div>
            <div class="summary-value" style="font-size:14px;word-break:break-all">{{ $subscriptionProviderReference }}</div>
            <div class="muted mt-1">Periodo atual: {{ $subscriptionEndsAt ? $subscriptionEndsAt->format('d/m/Y') : 'sem vencimento definido' }}.</div>
          </div>
          @if ($canManageBilling)
            <a class="btnx" href="#upgrade">Ver opções internas de upgrade</a>
          @endif
        </div>
      </div>
    </section>

    @if ($usageWarning)
      <section class="cardx mb-3" style="border-color: {{ $usagePercent >= 100 ? 'rgba(255,107,107,.5)' : 'rgba(255,209,102,.5)' }}; background: linear-gradient(135deg, rgba(255,209,102,.08), rgba(80,184,255,.05));">
        <div class="d-flex justify-content-between gap-3 flex-wrap align-items-center">
          <div>
            <div class="kicker">Uso e cobrança</div>
            <h2 class="h4 mt-2 mb-1">{{ $usagePercent >= 100 ? 'Limite do plano atingido' : 'Atenção ao consumo mensal' }}</h2>
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
          <p class="muted mb-0">Este bloco fica dentro do workspace para o usuário decidir upgrade com base no consumo real. Ambiente Mercado Pago: {{ $mercadoPagoStatus['environment'] }} - SDK: {{ $mercadoPagoStatus['sdk'] }} - Configurado: {{ $mercadoPagoStatus['configured'] ? 'sim' : 'não' }}</p>
        </div>
        <span class="pill">Plano atual: {{ $planName }}</span>
      </div>
      <div class="row g-2 mt-2">
        @foreach ($availablePlans as $availablePlan)
          <div class="col-md-4">
            <div class="summary-cell h-100">
              <div class="summary-label">{{ $availablePlan->name }}</div>
              <div class="summary-value">R$ {{ number_format($availablePlan->price_cents / 100, 2, ',', '.') }}/mês</div>
              <div class="muted mt-1">{{ number_format($availablePlan->monthly_event_limit, 0, ',', '.') }} eventos/mês - retenção {{ $availablePlan->event_retention_days }} dias</div>
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
          <div class="kicker">Configuração GitHub</div>
          <h2 class="h4 mt-2">Endpoint privado do workspace</h2>
          <p class="muted">Use estes dados em <strong>Settings -> Webhooks -> Add webhook</strong> no repositório GitHub.</p>
          <div class="summary-cell mb-3">
            <div class="summary-label">GitHub App</div>
            <div class="summary-value">{{ $githubInstallation ? 'Instalação '.$githubInstallation->installation_id.' vinculada' : 'GitHub App ainda não vinculado a este workspace' }}</div>
            @if ($canManageGitHub)
              <a class="btnx primary w-100 mt-2" href="{{ route('github.install') }}">Vincular GitHub App a este workspace</a>
              @unless ($githubInstallation)
                <div class="muted mt-2">Se o App já foi instalado no GitHub, clique aqui logado neste usuário para amarrar a instalação ao workspace atual.</div>
              @endunless
            @else
              <div class="muted mt-2">Seu papel atual não permite conectar GitHub App.</div>
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
            <div class="muted">Seu papel atual permite acompanhar eventos, mas não criar eventos de teste.</div>
          @endif
        </div>
      </aside>

      <section id="eventos">
        <div class="cardx event-feed-head">
          <div>
            <div class="kicker">Eventos recebidos</div>
            <h2 class="h3 mt-2 mb-0">Histórico privado do workspace</h2>
          </div>
          <span class="pill">{{ $totalEvents }} evento(s)</span>
        </div>

        @forelse ($events as $event)
          <x-webhook-event-card :event="$event" />
        @empty
          <div class="cardx">
            <div class="kicker">Primeiro uso</div>
            <h2 class="h3 mt-2">Conecte seu primeiro repositório GitHub</h2>
            <p class="muted">Este workspace ainda não recebeu eventos. Siga o fluxo abaixo para validar tudo em poucos minutos.</p>
            <div class="row g-2 mt-2">
              <div class="col-md-4">
                <div class="summary-cell h-100">
                  <div class="summary-label">1. Copie o endpoint</div>
                  <div class="summary-value">Use o Payload URL e o Secret exibidos na coluna de configuração.</div>
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
                  <div class="summary-value">O evento aparecerá aqui com assinatura, payload sanitizado e histórico.</div>
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
