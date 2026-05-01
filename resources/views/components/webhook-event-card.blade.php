@props(['event', 'mode' => 'full'])

@php
  $repo = data_get($event->payload, 'repository.full_name', 'Repositório não informado');
  $sender = data_get($event->payload, 'sender.login', data_get($event->payload, 'pusher.name', 'GitHub'));
  $workflowEvent = data_get($event->payload, 'workflow_run.event');
  $workflowName = data_get($event->payload, 'workflow.name', data_get($event->payload, 'workflow_run.name'));
  $workflowStatus = data_get($event->payload, 'workflow_run.conclusion', data_get($event->payload, 'workflow_run.status'));
  $workflowBranch = data_get($event->payload, 'workflow_run.head_branch');
  $workflowSha = data_get($event->payload, 'workflow_run.head_sha');
  $branch = str_replace('refs/heads/', '', (string) data_get($event->payload, 'ref', '')) ?: data_get($event->payload, 'pull_request.head.ref', $workflowBranch ?: 'n/a');
  $commitCount = is_array(data_get($event->payload, 'commits')) ? count(data_get($event->payload, 'commits')) : 0;
  $delivery = $event->delivery_id ?: data_get($event->headers, 'x-github-delivery.0', data_get($event->headers, 'x-github-delivery'));
  $action = $event->action ?: data_get($event->payload, 'action');
  $files = collect(data_get($event->payload, 'head_commit.modified', []))
      ->merge(data_get($event->payload, 'head_commit.added', []))
      ->merge(data_get($event->payload, 'head_commit.removed', []))
      ->merge(data_get($event->payload, 'pull_request.changed_files') ? ['pull_request: '.data_get($event->payload, 'pull_request.changed_files').' arquivo(s) alterado(s)'] : [])
      ->filter()
      ->take(8);
  $notes = \App\Models\WebhookEventNote::where('webhook_event_id', $event->id)->latest()->limit($mode === 'compact' ? 1 : 3)->get();
  $tasks = \App\Models\WebhookEventTask::where('webhook_event_id', $event->id)->latest()->limit($mode === 'compact' ? 1 : 4)->get();
  $openTaskCount = \App\Models\WebhookEventTask::where('webhook_event_id', $event->id)->where('status', 'open')->count();
  $statusLabel = $event->signature_valid ? 'Assinatura válida' : 'Assinatura pendente';
  $statusClass = $event->signature_valid ? 'success' : 'warning';
  $aiBilling = $event->workspace ? \App\Support\AiAnalysisBilling::report($event->workspace) : null;
  $openAiConfigured = filled(config('services.openai.api_key'));
  $diagnostic = $event->event_name === 'workflow_run' && $workflowEvent
      ? 'Workflow '.$workflowEvent.' recebido via GitHub App'.($workflowStatus ? ' com status '.$workflowStatus : '').'.'
      : ($event->signature_valid
          ? 'Evento confiável para diagnóstico e automação.'
          : 'Confira o secret configurado no GitHub antes de confiar neste payload.');
  $riskLabel = [
      'high' => 'Risco alto',
      'medium' => 'Risco médio',
      'low' => 'Risco baixo',
  ][$event->ai_risk_level] ?? 'Ainda sem análise';
  $riskClass = [
      'high' => 'warning',
      'medium' => 'soft',
      'low' => 'success',
  ][$event->ai_risk_level] ?? 'soft';
  $eventIcon = [
      'push' => '↗',
      'pull_request' => 'PR',
      'workflow_run' => 'CI',
      'issues' => '!',
      'installation' => 'APP',
      'installation_repositories' => 'REPO',
  ][$event->event_name] ?? 'GH';
@endphp

<article class="event event-card {{ $mode === 'compact' ? 'event-card-compact' : 'event-card-full' }}" data-event-type="{{ $event->event_name }}" data-signature="{{ $event->signature_valid ? 'valid' : 'pending' }}">
  <div class="event-topline">
    <div class="event-type">
      <div class="event-icon">{{ $eventIcon }}</div>
      <div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
          <strong class="event-name">{{ $event->event_name }}</strong>
          @if($workflowEvent)<span class="pill soft">origem {{ $workflowEvent }}</span>@endif
          @if($action)<span class="pill soft">{{ $action }}</span>@endif
          <span class="pill {{ $statusClass }}">{{ $statusLabel }}</span>
          <span class="pill {{ $riskClass }}">{{ $riskLabel }}</span>
        </div>
        <div class="muted mt-1">{{ $repo }} · {{ $sender }} · branch {{ $branch }} @if($workflowSha) · sha {{ substr($workflowSha, 0, 7) }} @endif</div>
      </div>
    </div>
    <div class="text-end">
      <span class="pill">{{ optional($event->received_at)->format('d/m/Y H:i:s') }}</span>
      @if($delivery)<div class="muted mt-1">delivery: {{ $delivery }}</div>@endif
    </div>
  </div>

  <div class="event-insights">
    <div class="insight"><div class="insight-glyph">GH</div><div><span>Origem</span><strong>{{ $event->source }}</strong></div></div>
    <div class="insight"><div class="insight-glyph">BR</div><div><span>Commits</span><strong>{{ $commitCount }}</strong></div></div>
    <div class="insight"><div class="insight-glyph">FL</div><div><span>Arquivos</span><strong>{{ $files->count() }}</strong></div></div>
    <div class="insight"><div class="insight-glyph">OK</div><div><span>{{ $workflowName ? 'Workflow' : 'Status' }}</span><strong>{{ $workflowName ? \Illuminate\Support\Str::limit($workflowName, 24) : ($event->signature_valid ? 'validado' : 'revisar') }}</strong></div></div>
  </div>

  <div class="event-diagnostic">
    <div class="section-orb">LR</div>
    <div>
      <strong>Leitura rápida</strong>
      <span>{{ $event->ai_summary ?: $diagnostic }}</span>
    </div>
  </div>

  @if($mode === 'compact')
    <div class="event-compact-footer">
      <div class="event-mini-stack">
        <span class="pill soft">{{ $event->ai_generated_at ? 'AI gerada' : 'AI pendente' }}</span>
        <span class="pill">{{ $openTaskCount }} tarefa(s) aberta(s)</span>
      </div>
      <a class="btnx primary" href="{{ route('dashboard.event', $event) }}">Abrir investigação</a>
    </div>
  @else
    <div class="event-diagnostic ai-diagnostic">
      <div class="d-flex justify-content-between gap-2 flex-wrap align-items-start">
        <div>
          <strong>Análise AI do evento</strong>
          <span>{{ $event->ai_summary ?: 'Gere uma leitura inteligente para transformar este payload em resumo, risco e próximos passos.' }}</span>
        </div>
        <span class="pill {{ $riskClass }}">{{ $riskLabel }}</span>
      </div>
      <div class="ai-meta-line">
        <span class="pill {{ $riskClass }}">{{ $riskLabel }}</span>
        @if($event->ai_generated_at)<span>Gerada em {{ $event->ai_generated_at->format('d/m/Y H:i') }}</span>@endif
        @if($event->ai_provider)<span>{{ $event->ai_provider }}</span>@endif
        @if($event->ai_analysis_type)<span>{{ $event->ai_analysis_type === 'llm' ? 'AI avançada' : 'AI grátis' }}</span>@endif
        @if($event->ai_estimated_cost_cents > 0)<span>custo estimado R$ {{ number_format($event->ai_estimated_cost_cents / 100, 2, ',', '.') }}</span>@endif
      </div>
      @if($event->ai_generated_at)
        <div class="muted mt-2 d-none">Gerada em {{ $event->ai_generated_at->format('d/m/Y H:i') }} · {{ $event->ai_provider }} · {{ $event->ai_analysis_type === 'llm' ? 'AI avançada' : 'AI grátis' }} @if($event->ai_estimated_cost_cents > 0) · custo estimado R$ {{ number_format($event->ai_estimated_cost_cents / 100, 2, ',', '.') }} @endif</div>
      @endif
      @if($event->ai_error)
        <div class="muted mt-2">Último erro AI: {{ $event->ai_error }}</div>
      @endif
      @if(is_array($event->ai_signals) && count($event->ai_signals) > 0)
        <div class="file-strip mt-2">
          @foreach($event->ai_signals as $signal)
            <code>{{ $signal }}</code>
          @endforeach
        </div>
      @endif
      @if(is_array($event->ai_action_items) && count($event->ai_action_items) > 0)
        <ul class="ai-action-list muted mt-2 mb-0">
          @foreach($event->ai_action_items as $item)
            <li>{{ $item }}</li>
          @endforeach
        </ul>
      @endif
      <div class="ai-action-bar">
        <form method="POST" action="{{ route('events.ai-analysis.generate', $event) }}">
          @csrf
          <input type="hidden" name="mode" value="local">
          <button class="btnx" type="submit">{{ $event->ai_summary && $event->ai_analysis_type === 'local' ? 'Regerar AI grátis' : 'Gerar AI grátis' }}</button>
        </form>
        <form method="POST" action="{{ route('events.ai-analysis.generate', $event) }}">
          @csrf
          <input type="hidden" name="mode" value="llm">
          <button class="btnx primary" type="submit" {{ ! $openAiConfigured || ! ($aiBilling['can_use'] ?? false) ? 'disabled' : '' }}>Gerar AI avançada</button>
        </form>
      </div>
      <div class="muted mt-2">AI grátis: inclusa. AI avançada: {{ $openAiConfigured ? (($aiBilling['enabled'] ?? false) ? (($aiBilling['remaining'] ?? 0).' restante(s) no plano') : 'não inclusa neste plano') : 'OPENAI_API_KEY não configurada' }}.</div>
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
        <div class="mini-panel-head"><strong>Notas</strong><span class="pill">Adicionar nota abaixo</span></div>
        @forelse($notes as $note)
          <p>{{ $note->body }}</p>
        @empty
          <p class="muted">Sem notas ainda.</p>
        @endforelse
        <form method="POST" action="{{ route('events.notes.store', $event) }}">
          @csrf
          <textarea name="body" rows="3" placeholder="Ex.: confirmar se o payload já pode acionar automação"></textarea>
          <button class="btnx w-100 mt-2" type="submit">Adicionar nota</button>
        </form>
      </div>
      <div class="mini-panel">
        <div class="mini-panel-head"><strong>Tarefas</strong><span class="pill">Nova tarefa abaixo</span></div>
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
  @endif
</article>
