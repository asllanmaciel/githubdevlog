@props(['event'])

@php
  $repo = data_get($event->payload, 'repository.full_name', 'RepositÃ³rio nÃ£o informado');
  $sender = data_get($event->payload, 'sender.login', data_get($event->payload, 'pusher.name', 'GitHub'));
  $workflowEvent = data_get($event->payload, 'workflow_run.event');
  $workflowName = data_get($event->payload, 'workflow.name', data_get($event->payload, 'workflow_run.name'));
  $workflowStatus = data_get($event->payload, 'workflow_run.conclusion', data_get($event->payload, 'workflow_run.status'));
  $workflowBranch = data_get($event->payload, 'workflow_run.head_branch');
  $workflowSha = data_get($event->payload, 'workflow_run.head_sha');
  $branch = str_replace('refs/heads/', '', (string) data_get($event->payload, 'ref', '')) : data_get($event->payload, 'pull_request.head.ref', $workflowBranch : 'n/a');
  $commitCount = is_array(data_get($event->payload, 'commits')) count(data_get($event->payload, 'commits')) : 0;
  $delivery = $event->delivery_id : data_get($event->headers, 'x-github-delivery.0', data_get($event->headers, 'x-github-delivery'));
  $action = $event->action : data_get($event->payload, 'action');
  $files = collect(data_get($event->payload, 'head_commit.modified', []))
      ->merge(data_get($event->payload, 'head_commit.added', []))
      ->merge(data_get($event->payload, 'head_commit.removed', []))
      ->merge(data_get($event->payload, 'pull_request.changed_files') ['pull_request: '.data_get($event->payload, 'pull_request.changed_files').' arquivo(s) alterado(s)'] : [])
      ->filter()
      ->take(8);
  $notes = \App\Models\WebhookEventNote::where('webhook_event_id', $event->id)->latest()->limit(3)->get();
  $tasks = \App\Models\WebhookEventTask::where('webhook_event_id', $event->id)->latest()->limit(4)->get();
  $statusLabel = $event->signature_valid 'Assinatura vÃ¡lida' : 'Assinatura pendente';
  $statusClass = $event->signature_valid 'success' : 'warning';
  $aiBilling = $event->workspace \App\Support\AiAnalysisBilling::report($event->workspace) : null;
  $openAiConfigured = filled(config('services.openai.api_key'));
  $diagnostic = $event->signature_valid
      'Evento confiÃ¡vel para diagnÃ³stico e automaÃ§Ã£o.'
      : 'Confira o secret configurado no GitHub antes de confiar neste payload.';
  $riskLabel = [
      'high' => 'Risco alto',
      'medium' => 'Risco mÃ©dio',
      'low' => 'Risco baixo',
  ][$event->ai_risk_level] 'Ainda sem anÃ¡lise';
  $riskClass = [
      'high' => 'warning',
      'medium' => 'soft',
      'low' => 'success',
  ][$event->ai_risk_level] 'soft';
@endphp

<article class="event event-card" data-event-type="{{ $event->event_name }}" data-signature="{{ $event->signature_valid 'valid' : 'pending' }}">
  <div class="event-topline">
    <div>
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <strong class="event-name">{{ $event->event_name }}</strong>
        @if($workflowEvent)<span class="pill soft">origem {{ $workflowEvent }}</span>@endif
        @if($action)<span class="pill soft">{{ $action }}</span>@endif
        <span class="pill {{ $statusClass }}">{{ $statusLabel }}</span>
      </div>
      <div class="muted mt-1">{{ $repo }} Â· {{ $sender }} Â· branch {{ $branch }} @if($workflowSha) Â· sha {{ substr($workflowSha, 0, 7) }} @endif</div>
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
    <div class="insight"><span>{{ $workflowName 'Workflow' : 'Status' }}</span><strong>{{ $workflowName \Illuminate\Support\Str::limit($workflowName, 24) : ($event->signature_valid 'validado' : 'revisar') }}</strong></div>
  </div>

  <div class="event-diagnostic">
    <strong>Leitura rÃ¡pida</strong>
    <span>{{ $diagnostic }}</span>
  </div>

  <div class="event-diagnostic ai-diagnostic">
    <div class="d-flex justify-content-between gap-2 flex-wrap align-items-start">
      <div>
        <strong>AnÃ¡lise AI do evento</strong>
        <span>{{ $event->ai_summary : 'Gere uma leitura inteligente para transformar este payload em resumo, risco e prÃ³ximos passos.' }}</span>
      </div>
      <span class="pill {{ $riskClass }}">{{ $riskLabel }}</span>
    </div>
    @if($event->ai_generated_at)
      <div class="muted mt-2">Gerada em {{ $event->ai_generated_at->format('d/m/Y H:i') }} Â· {{ $event->ai_provider }} Â· {{ $event->ai_analysis_type === 'llm' 'AI avanÃ§ada' : 'AI grÃ¡tis' }} @if($event->ai_estimated_cost_cents > 0) Â· custo estimado R$ {{ number_format($event->ai_estimated_cost_cents / 100, 2, ',', '.') }} @endif</div>
    @endif
    @if($event->ai_error)
      <div class="muted mt-2">Ãšltimo erro AI: {{ $event->ai_error }}</div>
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
    <div class="d-flex gap-2 flex-wrap mt-2">
      <form method="POST" action="{{ route('events.ai-analysis.generate', $event) }}">
        @csrf
        <input type="hidden" name="mode" value="local">
        <button class="btnx" type="submit">{{ $event->ai_summary && $event->ai_analysis_type === 'local' 'Regerar AI grÃ¡tis' : 'Gerar AI grÃ¡tis' }}</button>
      </form>
      <form method="POST" action="{{ route('events.ai-analysis.generate', $event) }}">
        @csrf
        <input type="hidden" name="mode" value="llm">
        <button class="btnx primary" type="submit" {{ ! $openAiConfigured || ! ($aiBilling['can_use'] false) 'disabled' : '' }}>Gerar AI avanÃ§ada</button>
      </form>
    </div>
    <div class="muted mt-2">AI grÃ¡tis: inclusa. AI avanÃ§ada: {{ $openAiConfigured (($aiBilling['enabled'] false) (($aiBilling['remaining'] 0).' restante(s) no plano') : 'nÃ£o inclusa neste plano') : 'OPENAI_API_KEY nÃ£o configurada' }}.</div>
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
        <textarea name="body" rows="3" placeholder="Ex.: confirmar se o payload jÃ¡ pode acionar automaÃ§Ã£o"></textarea>
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

