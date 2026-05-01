<x-layout title="Evento #{{ $event->id }} | GitHub DevLog AI">
  <main>
    <section class="event-detail-shell">
      <div class="event-detail-hero cardx">
        <div>
          <div class="kicker">Investigação do evento</div>
          <h1 class="dashboard-title">Evento #{{ $event->id }} em foco</h1>
          <p class="lead mt-3 mb-0">Aqui ficam a análise AI, sinais, próximos passos, notas, tarefas e payload sanitizado sem poluir a inbox de eventos.</p>
        </div>
        <div class="event-detail-actions">
          <a class="btnx" href="{{ route('dashboard', ['section' => 'events']) }}">Voltar para eventos</a>
          <a class="btnx primary" href="{{ route('dashboard', ['section' => 'ai']) }}">Ver uso de AI</a>
        </div>
      </div>

      <x-webhook-event-card :event="$event" mode="full" />
    </section>
  </main>
</x-layout>
