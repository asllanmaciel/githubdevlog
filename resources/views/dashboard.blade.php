<x-layout title="Dashboard | GitHub DevLog AI">
@php
    use App\Models\BillingPlan;

    $endpoint = $workspace ? url('/webhooks/github/'.$workspace->uuid) : null;
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
    $plan = $subscription?->plan ?? BillingPlan::where('slug', 'free')->first();
    $monthlyEvents = $workspace ? $workspace->webhookEvents()->whereBetween('received_at', [now()->startOfMonth(), now()->endOfMonth()])->count() : 0;
    $monthlyLimit = max((int) ($plan?->monthly_event_limit ?? 1000), 1);
    $usagePercent = min(100, round(($monthlyEvents / $monthlyLimit) * 100));
    $retentionDays = (int) ($plan?->event_retention_days ?? 30);
    $planName = $plan?->name ?? 'Free';
    $subscriptionStatus = $subscription?->status ?? 'trialing';
    $healthStatus = $totalEvents === 0 ? 'Aguardando evento' : ($invalidEvents > 0 ? 'Atenção' : 'Saudável');
    $healthClass = $invalidEvents > 0 ? 'status-warn' : 'status-ok';
    $usageWarning = $usagePercent >= 100
        ? 'Limite mensal atingido. Novos webhooks serão recusados até upgrade ou renovação.'
        : ($usagePercent >= 80 ? 'Uso mensal próximo do limite. Considere upgrade antes de perder eventos importantes.' : null);
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
            <div class="control-label">Retenção</div>
            <div class="control-value">{{ $retentionDays }} dias</div>
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
          <div class="bar-track"><span class="bar-fill" style="width: {{ $usagePercent }}%"></span></div>
          <div class="muted mt-2">Status da assinatura: {{ $subscriptionStatus }}</div>
        </div>
      </aside>
    </section>

    <section class="metric-grid">
      <div class="metric"><div class="metric-label">Eventos recentes</div><div class="metric-value">{{ $totalEvents }}</div><div class="spark"><span style="height:40%"></span><span style="height:70%"></span><span style="height:55%"></span><span style="height:90%"></span></div></div>
      <div class="metric"><div class="metric-label">Repositórios vistos</div><div class="metric-value">{{ $repos }}</div><div class="muted mt-2">Origem mais recente: {{ $latestRepo }}</div></div>
      <div class="metric"><div class="metric-label">Push recebidos</div><div class="metric-value">{{ $pushes }}</div><div class="muted mt-2">Sender recente: {{ $latestSender }}</div></div>
      <div class="metric"><div class="metric-label">Notas e tarefas</div><div class="metric-value">{{ $notesCount + $openTasks }}</div><div class="muted mt-2">{{ $openTasks }} tarefa(s) aberta(s)</div></div>
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
          <a class="quick-action" href="#setup"><span>Configurar webhook no GitHub</span><span>→</span></a>
          <a class="quick-action" href="#eventos"><span>Investigar eventos recebidos</span><span>→</span></a>
          <a class="quick-action" href="{{ route('support') }}"><span>Abrir chamado de suporte</span><span>→</span></a>
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

    <section class="dashboard-grid">
      <aside id="setup" class="config-card">
        <div class="cardx mb-3">
          <div class="kicker">Configuração GitHub</div>
          <h2 class="h4 mt-2">Endpoint privado do workspace</h2>
          <p class="muted">Use estes dados em <strong>Settings → Webhooks → Add webhook</strong> no repositório GitHub.</p>
          <label>Payload URL</label>
          <div class="endpoint-box mb-3">{{ $endpoint }}</div>
          <label>Content type</label>
          <pre>application/json</pre>
          <label>Secret</label>
          <pre>{{ $workspace->webhook_secret }}</pre>
          <form method="POST" action="{{ route('workspace.secret.rotate') }}">
            @csrf
            <button class="btnx w-100" type="submit">Rotacionar secret</button>
          </form>
        </div>

        <div class="cardx">
          <div class="kicker">Teste manual</div>
          <h2 class="h5 mt-2">Salvar evento no workspace</h2>
          <form method="POST" action="{{ route('dashboard.test-event') }}">
            @csrf
            <textarea name="payload" rows="8">{ "event": "push", "repository": { "full_name": "demo/repo" }, "pusher": { "name": "dev" } }</textarea>
            <button class="btnx success w-100 mt-2" type="submit">Salvar evento de teste</button>
          </form>
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
          @php
            $repo = data_get($event->payload, 'repository.full_name', 'Repositório não informado');
            $sender = data_get($event->payload, 'sender.login', data_get($event->payload, 'pusher.name', 'GitHub'));
            $branch = str_replace('refs/heads/', '', (string) data_get($event->payload, 'ref', '')) ?: 'n/a';
            $commitCount = is_array(data_get($event->payload, 'commits')) ? count(data_get($event->payload, 'commits')) : 0;
            $files = collect(data_get($event->payload, 'head_commit.modified', []))->merge(data_get($event->payload, 'head_commit.added', []))->merge(data_get($event->payload, 'head_commit.removed', []))->take(8);
          @endphp
          <article class="event-card">
            <div class="event-top">
              <div class="event-type">
                <div class="event-icon">{{ strtoupper(substr($event->event_name, 0, 2)) }}</div>
                <div>
                  <div class="event-title">{{ $event->event_name }}</div>
                  <div class="event-subtitle">{{ $event->source }} · {{ $event->validation_method }} · {{ $event->received_at?->format('d/m/Y H:i:s') }}</div>
                </div>
              </div>
              <div class="text-end">
                <span class="pill {{ $event->signature_valid ? 'status-ok' : 'status-warn' }}">{{ $event->signature_valid ? 'Assinatura válida' : 'Assinatura inválida' }}</span>
                @if ($event->delivery_id)
                  <div class="muted mt-1">delivery: {{ $event->delivery_id }}</div>
                @endif
              </div>
            </div>

            <div class="event-summary">
              <div class="summary-cell"><div class="summary-label">Repositório</div><div class="summary-value">{{ $repo }}</div></div>
              <div class="summary-cell"><div class="summary-label">Sender</div><div class="summary-value">{{ $sender }}</div></div>
              <div class="summary-cell"><div class="summary-label">Branch / commits</div><div class="summary-value">{{ $branch }} · {{ $commitCount }} commit(s)</div></div>
            </div>

            @if ($files->isNotEmpty())
              <div class="summary-label">Arquivos tocados</div>
              <div class="file-list">
                @foreach ($files as $file)
                  <span class="file-chip">{{ $file }}</span>
                @endforeach
              </div>
            @endif

            <div class="row g-2 mt-3">
              <div class="col-md-6">
                <form method="POST" action="{{ route('events.notes.store', $event) }}">
                  @csrf
                  <label>Nota interna</label>
                  <textarea name="body" rows="3" placeholder="Ex.: investigar payload antes de liberar automação"></textarea>
                  <button class="btnx w-100 mt-2" type="submit">Adicionar nota</button>
                </form>
              </div>
              <div class="col-md-6">
                <form method="POST" action="{{ route('events.tasks.store', $event) }}">
                  @csrf
                  <label>Criar tarefa</label>
                  <input name="title" placeholder="Ex.: validar assinatura no ambiente de produção">
                  <button class="btnx w-100 mt-2" type="submit">Criar tarefa</button>
                </form>
              </div>
            </div>

            <details class="payload">
              <summary>Ver payload sanitizado</summary>
              <pre>{{ json_encode($event->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
            </details>
          </article>
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
                  <div class="summary-value">Em Settings → Webhooks, escolha JSON e cole o Secret do workspace.</div>
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
</x-layout>
