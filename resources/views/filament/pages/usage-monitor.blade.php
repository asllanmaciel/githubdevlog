@php
  $reports = App\Models\Workspace::query()
    ->with(['subscription.plan'])
    ->orderBy('name')
    ->get()
    ->map(fn ($workspace) => App\Support\WorkspaceUsage::report($workspace));

  $totalUsage = $reports->sum('usage');
  $totalLimit = max($reports->sum('limit'), 1);
  $globalPercent = min(100, (int) round(($totalUsage / $totalLimit) * 100));
  $blocked = $reports->where('limit_reached', true)->count();
  $near = $reports->where('near_limit', true)->where('limit_reached', false)->count();
@endphp

<x-filament-panels::page>
  <style>
    .usage{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;--danger:#ff6b6b;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 84% 12%,rgba(105,227,154,.16),transparent 30%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:850px}.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:18px;background:#0b1118;padding:18px}.value{font-size:42px;font-weight:950;letter-spacing:-.05em}.muted{color:var(--muted);font-size:13px;line-height:1.55}.workspace{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px;margin-bottom:12px}.workspace.danger{border-color:rgba(255,107,107,.45)}.workspace.warn{border-color:rgba(255,209,102,.45)}.top{display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.pill.ok{background:var(--green);border-color:var(--green);color:#071018;font-weight:950}.pill.warn{background:var(--yellow);border-color:var(--yellow);color:#071018;font-weight:950}.pill.danger{background:var(--danger);border-color:var(--danger);color:#071018;font-weight:950}.bar{height:10px;border-radius:999px;background:#050a10;border:1px solid var(--line);overflow:hidden;margin-top:12px}.bar span{display:block;height:100%;background:linear-gradient(90deg,var(--blue),var(--green));border-radius:999px}.workspace.warn .bar span{background:var(--yellow)}.workspace.danger .bar span{background:var(--danger)}@media(max-width:1000px){.metrics{grid-template-columns:1fr}}
  </style>

  <div class="usage">
    <section class="hero">
      <div class="kicker">Usage based SaaS</div>
      <h1 class="title">Uso mensal por workspace.</h1>
      <p class="lead">Monitore consumo, limites de plano e workspaces proximos do bloqueio. Essa tela fecha o ciclo entre billing por uso e captura real de webhooks.</p>
    </section>

    <section class="metrics">
      <div class="metric"><div class="kicker">Uso global</div><div class="value">{{ $globalPercent }}%</div><div class="muted">{{ $totalUsage }} de {{ $totalLimit }} eventos no mes.</div></div>
      <div class="metric"><div class="kicker">Workspaces</div><div class="value">{{ $reports->count() }}</div><div class="muted">Contas com plano/limite monitorado.</div></div>
      <div class="metric"><div class="kicker">Perto do limite</div><div class="value">{{ $near }}</div><div class="muted">Acima de 80% do uso mensal.</div></div>
      <div class="metric"><div class="kicker">Bloqueados</div><div class="value">{{ $blocked }}</div><div class="muted">Limite mensal atingido.</div></div>
    </section>

    <section class="card">
      <div class="kicker">Workspaces</div>
      @forelse ($reports as $report)
        <article class="workspace {{ $report['limit_reached'] ? 'danger' : ($report['near_limit'] ? 'warn' : '') }}">
          <div class="top">
            <div>
              <strong>{{ $report['workspace']->name }}</strong>
              <div class="muted">{{ $report['workspace']->slug }} · plano {{ $report['plan']?->name ?? 'Free' }}</div>
            </div>
            <div>
              <span class="pill {{ $report['limit_reached'] ? 'danger' : ($report['near_limit'] ? 'warn' : 'ok') }}">{{ $report['percent'] }}%</span>
              <span class="pill">{{ $report['usage'] }}/{{ $report['limit'] }}</span>
              <span class="pill">restam {{ $report['remaining'] }}</span>
            </div>
          </div>
          <div class="bar"><span style="width:{{ $report['percent'] }}%"></span></div>
          <div class="muted" style="margin-top:10px">Janela: {{ $report['period_start']->format('d/m/Y') }} ate {{ $report['period_end']->format('d/m/Y') }}</div>
        </article>
      @empty
        <div class="muted">Nenhum workspace encontrado. Rode <code>php artisan devlog:seed-demo</code> para criar um cenario de teste.</div>
      @endforelse
    </section>
  </div>
</x-filament-panels::page>