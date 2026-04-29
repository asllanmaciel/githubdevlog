<x-layout title="Admin - GitHub DevLog AI">
  <section class="dashboard-hero">
    <div class="cardx">
      <div class="kicker">Admin</div>
      <h1 class="dashboard-title">Central de lançamento e operação SaaS.</h1>
      <p class="lead mt-3 mb-0">Gerencie roadmap, contas, workspaces, suporte, planos e os próximos blocos do produto antes do lançamento público.</p>
      <div class="d-flex gap-2 flex-wrap mt-3">
        <a class="btnx primary" href="{{ url('/admin') }}">Abrir painel Filament</a>
        <a class="btnx" href="{{ route('docs.admin') }}">Docs admin</a>
      </div>
    </div>
    <div class="cardx">
      <div class="kicker">Resumo operacional</div>
      <div class="metric-value">{{ $eventsCount }}</div>
      <div class="metric-label">webhooks recebidos no ambiente</div>
      <div class="d-flex gap-2 flex-wrap mt-3">
        <span class="pill">{{ $users->count() }} usuários recentes</span>
        <span class="pill">{{ $workspaces->count() }} workspaces recentes</span>
        <span class="pill">{{ $tickets->where('status', 'open')->count() }} chamados abertos</span>
      </div>
    </div>
  </section>

  <section class="metric-grid">
    <div class="metric">
      <div class="metric-value">{{ round(($roadmap->where('status', 'done')->count() / max($roadmap->count(), 1)) * 100) }}%</div>
      <div class="metric-label">progresso geral do lançamento</div>
      <div class="spark"><span style="height:30%"></span><span style="height:45%"></span><span style="height:70%"></span><span style="height:100%"></span></div>
    </div>
    <div class="metric"><div class="metric-value">{{ $plans->count() }}</div><div class="metric-label">planos comerciais preparados</div></div>
    <div class="metric"><div class="metric-value">{{ $tickets->count() }}</div><div class="metric-label">tickets recentes</div></div>
    <div class="metric"><div class="metric-value">MP</div><div class="metric-label">SDK Mercado Pago instalado</div></div>
  </section>

  <div class="dashboard-grid">
    <aside class="config-card">
      <div class="cardx mb-3">
        <div class="kicker">Contas recentes</div>
        @foreach ($users as $user)
          <div class="event-subtitle mb-2"><strong>{{ $user->name }}</strong><br>{{ $user->email }} @if($user->is_super_admin) · admin @endif</div>
        @endforeach
      </div>

      <div class="cardx mb-3">
        <div class="kicker">Workspaces recentes</div>
        @foreach ($workspaces as $workspace)
          <div class="event-subtitle mb-2"><strong>{{ $workspace->name }}</strong><br>{{ $workspace->slug }}</div>
        @endforeach
      </div>

      <div class="cardx">
        <div class="kicker">Planos</div>
        @foreach ($plans as $plan)
          <div class="event-subtitle mb-2"><strong>{{ $plan->name }}</strong><br>R$ {{ number_format($plan->price_cents / 100, 2, ',', '.') }} · {{ $plan->monthly_event_limit }} eventos/mês</div>
        @endforeach
      </div>
    </aside>

    <section>
      <div class="cardx mb-3">
        <div class="kicker">Roadmap de lançamento</div>
        <h2 class="h3 mt-2">Cockpit visual do produto</h2>
        <p class="muted mb-0">Cada bloco representa uma frente real para deixar o DevLog AI pronto para o programa do GitHub, usuários beta e operação SaaS.</p>
      </div>

      <div class="roadmap-board">
        @foreach ($roadmap->groupBy('area') as $area => $items)
          @php
            $done = $items->where('status', 'done')->count();
            $total = max($items->count(), 1);
            $percent = round(($done / $total) * 100);
          @endphp
          <article class="roadmap-phase">
            <div class="roadmap-head">
              <div>
                <h3 class="roadmap-title">{{ $area }}</h3>
                <div class="roadmap-count">{{ $done }} de {{ $items->count() }} item(ns) concluído(s)</div>
              </div>
              <div class="roadmap-percent">{{ $percent }}%</div>
            </div>

            <div class="roadmap-progress" aria-label="Progresso de {{ $area }}">
              <span style="width: {{ $percent }}%"></span>
            </div>

            <div class="roadmap-items">
              @foreach ($items as $item)
                <form method="POST" action="{{ route('admin.roadmap.toggle', $item) }}" class="roadmap-item {{ $item->status === 'done' ? 'done' : '' }}">
                  @csrf
                  <div class="roadmap-check">{{ $item->status === 'done' ? '✓' : '' }}</div>
                  <div>
                    <div class="roadmap-item-title">{{ $item->title }}</div>
                    <div class="roadmap-item-desc">{{ $item->description }}</div>
                    <div class="roadmap-meta">
                      <span class="pill">{{ $item->priority }}</span>
                      <span class="pill">{{ $item->status === 'done' ? 'Concluído' : 'Pendente' }}</span>
                      @if($item->completed_at)<span class="pill">finalizado em {{ $item->completed_at->format('d/m/Y') }}</span>@endif
                    </div>
                  </div>
                  <div class="roadmap-action">
                    <button class="btnx {{ $item->status === 'done' ? '' : 'success' }}" type="submit">{{ $item->status === 'done' ? 'Reabrir' : 'Concluir' }}</button>
                  </div>
                </form>
              @endforeach
            </div>
          </article>
        @endforeach
      </div>

      <div class="cardx mt-3">
        <div class="kicker">Suporte recente</div>
        @forelse ($tickets as $ticket)
          <div class="summary-cell mb-2"><strong>{{ $ticket->subject }}</strong><div class="muted">{{ $ticket->status }} · {{ $ticket->priority }} · {{ $ticket->message }}</div></div>
        @empty
          <p class="muted mb-0">Nenhum chamado ainda.</p>
        @endforelse
      </div>
    </section>
  </div>
</x-layout>
