@props(['event'])

@php
  $repo = data_get($event->payload, 'repository.full_name', 'Repositório não informado');
  $sender = data_get($event->payload, 'sender.login', data_get($event->payload, 'pusher.name', 'GitHub'));
  $branch = str_replace('refs/heads/', '', (string) data_get($event->payload, 'ref', '')) ?: data_get($event->payload, 'pull_request.head.ref', 'n/a');
  $commitCount = is_array(data_get($event->payload, 'commits')) ? count(data_get($event->payload, 'commits')) : 0;
  $delivery = $event->delivery_id ?: data_get($event->headers, 'x-github-delivery.0', data_get($event->headers, 'x-github-delivery'));
  $action = $event->action ?: data_get($event->payload, 'action');
  $files = collect(data_get($event->payload, 'head_commit.modified', []))
      ->merge(data_get($event->payload, 'head_commit.added', []))
      ->merge(data_get($event->payload, 'head_commit.removed', []))
      ->merge(data_get($event->payload, 'pull_request.changed_files') ? ['pull_request: '.data_get($event->payload, 'pull_request.changed_files').' arquivo(s) alterado(s)'] : [])
      ->filter()
      ->take(8);
  $notes = \App\Models\WebhookEventNote::where('webhook_event_id', $event->id)->latest()->limit(3)->get();
  $tasks = \App\Models\WebhookEventTask::where('webhook_event_id', $event->id)->latest()->limit(4)->get();
  $statusLabel = $event->signature_valid ? 'Assinatura valida' : 'Assinatura pendente';
  $statusClass = $event->signature_valid ? 'success' : 'warning';
  $diagnostic = $event->signature_valid
      ? 'Evento confiavel para diagnostico e automacao.'
      : 'Confira o secret configurado no GitHub antes de confiar neste payload.';
@endphp

<article class="event event-card" data-event-type="{{ $event->event_name }}" data-signature="{{ $event->signature_valid ? 'valid' : 'pending' }}">
  <div class="event-topline">
    <div>
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <strong class="event-name">{{ $event->event_name }}</strong>
        @if($action)<span class="pill soft">{{ $action }}</span>@endif
        <span class="pill {{ $statusClass }}">{{ $statusLabel }}</span>
      </div>
      <div class="muted mt-1">{{ $repo }} · {{ $sender }} · branch {{ $branch }}</div>
    </div>
    <div class="text-end">
      <span class="pill">{{ optional($event->received_at)->format('d/m/Y H:i:s') }}</span>
      @if($delivery)<div class="muted mt-1">delivery: {{ $delivery }}</div>@endif
    </div>
  </div>

  <div class="event-insights">
    <div class="insight"><span>Origem</span><strong>{{ $event->source }}</strong></div>
    <div class="insight"><span>Commits</span><strong>{{ $commitCount }}</strong></div>
    <div class="insight"><span>Arquivos</span><strong>{{ $files->count() }}</strong></div>
    <div class="insight"><span>Status</span><strong>{{ $event->signature_valid ? 'validado' : 'revisar' }}</strong></div>
  </div>

  <div class="event-diagnostic">
    <strong>Leitura rápida</strong>
    <span>{{ $diagnostic }}</span>
  </div>


  <div class="event-diagnostic ai-diagnostic">
    <div class="d-flex justify-content-between gap-2 flex-wrap align-items-start">
      <div>
        <strong>Análise AI do evento</strong>
        <span>{{ $event->ai_summary ?: 'Gere uma leitura inteligente para transformar este payload em resumo, risco e próximos passos.' }}</span>
      </div>
      <span class="pill {{ $riskClass }}">{{ $riskLabel }}</span>
    </div>
    @if($event->ai_generated_at)
      <div class="muted mt-2">Gerada em {{ $event->ai_generated_at->format('d/m/Y H:i') }} · {{ $event->ai_provider }}</div>
    @endif
    @if(is_array($event->ai_signals) && count($event->ai_signals) > 0)
      <div class="file-strip mt-2">
        @foreach($event->ai_signals as $signal)
          <code>{{ $signal }}</code>
        @endforeach
      </div>
    @endif
    @if(is_array($event->ai_action_items) && count($event->ai_action_items) > 0)
      <ul class="muted mt-2 mb-0">
        @foreach($event->ai_action_items as $item)
          <li>{{ $item }}</li>
        @endforeach
      </ul>
    @endif
    <form method="POST" action="{{ route('events.ai-analysis.generate', $event) }}" class="mt-2">
      @csrf
      <button class="btnx primary" type="submit">{{ $event->ai_summary ? 'Regerar análise AI' : 'Gerar análise AI' }}</button>
    </form>
  </div>

  @if($files->isNotEmpty())
    <div class="file-strip">
      @foreach($files as $file)
        <code>{{ $file }}</code>
      @endforeach
    </div>
  @endif

  <div class="event-actions-grid">
    <div class="mini-panel">
      <strong>Notas</strong>
      @forelse($notes as $note)
        <p>{{ $note->body }}</p>
      @empty
        <p class="muted">Sem notas ainda.</p>
      @endforelse
      <form method="POST" action="{{ route('events.notes.store', $event) }}">
        @csrf
        <textarea name="body" rows="3" placeholder="Ex.: confirmar se o payload ja pode acionar automacao"></textarea>
        <button class="btnx w-100 mt-2" type="submit">Adicionar nota</button>
      </form>
    </div>
    <div class="mini-panel">
      <strong>Tarefas</strong>
      @forelse($tasks as $task)
        <p><span class="pill soft">{{ $task->status }}</span> {{ $task->title }}</p>
      @empty
        <p class="muted">Nenhuma tarefa aberta.</p>
      @endforelse
      <form method="POST" action="{{ route('events.tasks.store', $event) }}">
        @csrf
        <input name="title" placeholder="Ex.: revisar parser deste evento">
        <button class="btnx success w-100 mt-2" type="submit">Criar tarefa</button>
      </form>
    </div>
  </div>

  <details class="payload">
    <summary>Ver payload sanitizado</summary>
    <pre>{{ json_encode($event->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
  </details>
</article>
