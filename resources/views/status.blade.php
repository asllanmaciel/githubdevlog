@php
  $status = App\Support\PublicStatus::report();
  $labels = ['operational' => 'Operacional', 'degraded' => 'Instabilidade parcial', 'outage' => 'Indisponivel'];
@endphp

<x-layout title="Status - GitHub DevLog AI">
  <style>
    .status-hero{border:1px solid var(--line);border-radius:22px;background:rgba(16,23,32,.9);padding:26px;margin-top:24px;display:flex;justify-content:space-between;gap:18px;flex-wrap:wrap;align-items:center}.status-hero strong{font-size:28px}.status-hero.operational{border-color:rgba(105,227,154,.45);box-shadow:0 0 0 1px rgba(105,227,154,.08)}.status-hero.degraded{border-color:rgba(255,209,102,.45)}.status-hero.outage{border-color:rgba(255,107,107,.45)}.status-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px}.status-card{border:1px solid var(--line);border-radius:18px;background:#0b1118;padding:18px}.status-card.operational{border-color:rgba(105,227,154,.32)}.status-card.degraded{border-color:rgba(255,209,102,.35)}.status-card.outage{border-color:rgba(255,107,107,.38)}.status-line{display:flex;justify-content:space-between;gap:12px;align-items:flex-start}.status-line span{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}@media(max-width:900px){.status-grid{grid-template-columns:1fr}}
  </style>

  <section class="hero compact">
    <span class="eyebrow">Status publico</span>
    <h1>Transparencia operacional para quem depende dos webhooks.</h1>
    <p class="lead">Resumo publico dos principais componentes do GitHub DevLog AI. Detalhes internos ficam no admin, mas usuarios conseguem saber se a plataforma esta saudavel.</p>
    <div class="status-hero {{ $status['overall'] }}">
      <strong>{{ $labels[$status['overall']] }}</strong>
      <span class="muted">Atualizado em {{ $status['updated_at']->format('d/m/Y H:i:s') }}</span>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Componentes</div>
    <div class="status-grid">
      @foreach ($status['components'] as $component)
        <article class="status-card {{ $component['status'] }}">
          <div class="status-line"><h3>{{ $component['name'] }}</h3><span>{{ $labels[$component['status']] }}</span></div>
          <p class="muted">{{ $component['detail'] }}</p>
        </article>
      @endforeach
    </div>
  </section>

  <section class="band">
    <div class="kicker">Precisa de ajuda?</div>
    <h2>Confira o status antes de abrir um chamado.</h2>
    <p class="lead">Se um componente estiver instavel, aguarde alguns minutos e consulte novamente. Para problemas especificos do seu workspace, abra suporte autenticado.</p>
    <div class="d-flex gap-2 flex-wrap mt-3"><a class="btnx primary" href="/support">Abrir suporte</a><a class="btnx" href="/health">Health JSON</a></div>
  </section>
</x-layout>