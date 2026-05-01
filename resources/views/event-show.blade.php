<x-layout title="Evento #{{ $event->id }} | GitHub DevLog AI">
  @php
    $repo = data_get($event->payload, 'repository.full_name', 'Repositório não informado');
    $sender = data_get($event->payload, 'sender.login', data_get($event->payload, 'pusher.name', 'GitHub'));
    $branch = str_replace('refs/heads/', '', (string) data_get($event->payload, 'ref', '')) ?: data_get($event->payload, 'workflow_run.head_branch', 'n/a');
    $riskLabel = [
      'high' => 'Risco alto',
      'medium' => 'Risco médio',
      'low' => 'Risco baixo',
    ][$event->ai_risk_level] ?? 'Sem análise';
  @endphp
  <main>
    <section class="event-detail-shell">
      <div class="event-detail-hero cardx">
        <div>
          <div class="kicker">War room do webhook</div>
          <h1 class="dashboard-title">{{ $event->event_name }} #{{ $event->id }}</h1>
          <p class="lead mt-3 mb-0">{{ $repo }} · {{ $sender }} · branch {{ $branch }}</p>
          <div class="event-hero-pills">
            <span class="pill {{ $event->signature_valid ? 'success' : 'warning' }}">{{ $event->signature_valid ? 'Assinatura validada' : 'Assinatura pendente' }}</span>
            <span class="pill soft">{{ $event->source }}</span>
            <span class="pill soft">{{ $riskLabel }}</span>
            <span class="pill">{{ optional($event->received_at)->format('d/m/Y H:i') }}</span>
          </div>
        </div>
        <div class="event-detail-actions">
          <a class="btnx" href="{{ route('dashboard', ['section' => 'events']) }}">Voltar para eventos</a>
          <a class="btnx primary" href="{{ route('dashboard', ['section' => 'ai']) }}">Ver uso de AI</a>
        </div>
      </div>

      <div class="event-command-strip">
        <div class="event-command-card">
          <span>Resultado</span>
          <strong>{{ $event->signature_valid ? 'Confiável' : 'Revisar' }}</strong>
          <small>Validação HMAC do GitHub</small>
        </div>
        <div class="event-command-card">
          <span>AI</span>
          <strong>{{ $event->ai_generated_at ? 'Gerada' : 'Pendente' }}</strong>
          <small>{{ $event->ai_provider ?: 'Pronta para análise' }}</small>
        </div>
        <div class="event-command-card">
          <span>Ação</span>
          <strong>{{ $event->action ?: 'n/a' }}</strong>
          <small>Tipo operacional recebido</small>
        </div>
      </div>

      <x-webhook-event-card :event="$event" mode="full" />
    </section>
  </main>
</x-layout>
