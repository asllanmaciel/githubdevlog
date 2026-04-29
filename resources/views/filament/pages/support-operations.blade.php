@php
  $report = App\Support\SupportSla::report();
  $categories = App\Support\SupportSla::categories();
@endphp

<x-filament-panels::page>
  <style>
    .supportops{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--danger:#ff6b6b;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.9);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 92% 12%,rgba(80,184,255,.16),transparent 34%),rgba(16,23,32,.9)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,58px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:920px}.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.value{font-size:34px;font-weight:950}.label{color:var(--muted);font-size:13px}.grid{display:grid;grid-template-columns:1fr 380px;gap:16px;align-items:start}.rowx{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:13px;margin-bottom:10px;display:grid;grid-template-columns:1fr auto;gap:12px}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.danger{color:var(--danger)}.warn{color:var(--yellow)}.ok{color:var(--green)}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}@media(max-width:1050px){.grid,.metrics{grid-template-columns:1fr}}
  </style>

  <div class="supportops">
    <section class="hero">
      <div class="kicker">Suporte pronto para beta e lancamento</div>
      <h1 class="title">Chamado sem SLA vira ansiedade. Aqui vira fila operacional.</h1>
      <p class="lead">Acompanhe chamados abertos, urgentes, vencidos por primeira resposta e resolucao. O objetivo e garantir que devs usando a ferramenta tenham resposta rapida quando webhook, billing ou GitHub App travarem.</p>
    </section>

    <section class="metrics">
      <div class="metric"><div class="value">{{ $report['open'] }}</div><div class="label">chamados abertos</div></div>
      <div class="metric"><div class="value {{ $report['urgent'] ? 'warn' : 'ok' }}">{{ $report['urgent'] }}</div><div class="label">urgentes</div></div>
      <div class="metric"><div class="value {{ $report['first_response_overdue'] ? 'danger' : 'ok' }}">{{ $report['first_response_overdue'] }}</div><div class="label">resposta vencida</div></div>
      <div class="metric"><div class="value {{ $report['resolution_overdue'] ? 'danger' : 'ok' }}">{{ $report['resolution_overdue'] }}</div><div class="label">resolucao vencida</div></div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Chamados recentes</div>
        @forelse ($report['recent'] as $ticket)
          <div class="rowx">
            <div>
              <strong>{{ $ticket->subject }}</strong>
              <div class="label">{{ $ticket->workspace?->name ?? 'sem workspace' }} · {{ $categories[$ticket->category] ?? $ticket->category }} · {{ $ticket->created_at?->format('d/m/Y H:i') }}</div>
            </div>
            <span class="pill">{{ App\Support\SupportSla::badge($ticket) }}</span>
          </div>
        @empty
          <p class="label">Nenhum chamado registrado ainda.</p>
        @endforelse
      </div>

      <aside class="card">
        <div class="kicker">Fila por categoria</div>
        @forelse ($report['by_category'] as $item)
          <div class="rowx">
            <strong>{{ $categories[$item->category] ?? $item->category }}</strong>
            <span class="pill">{{ $item->total }}</span>
          </div>
        @empty
          <p class="label">Sem fila aberta por categoria.</p>
        @endforelse

        <div class="kicker" style="margin-top:18px">Rotina recomendada</div>
        <pre class="cmd">1. Abrir /admin/support-operations
2. Resolver chamados urgentes ou vencidos
3. Registrar resposta em responded_at
4. Fechar com resolved_at e nota interna</pre>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
