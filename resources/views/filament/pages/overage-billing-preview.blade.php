@php
  $period = request('period', now()->format('Y-m'));
  $report = App\Support\OverageBilling::report($period);
  $money = fn ($cents) => 'R$ '.number_format($cents / 100, 2, ',', '.');
@endphp

<x-filament-panels::page>
  <style>
    .overage{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;--danger:#ff6b6b;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 85% 12%,rgba(255,209,102,.16),transparent 30%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:850px}.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:18px;background:#0b1118;padding:18px}.value{font-size:42px;font-weight:950;letter-spacing:-.05em}.muted{color:var(--muted);font-size:13px;line-height:1.55}.rowx{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px;margin-bottom:12px}.rowx.billable{border-color:rgba(105,227,154,.42)}.top{display:flex;justify-content:space-between;gap:14px;flex-wrap:wrap}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.pill.ok{background:var(--green);border-color:var(--green);color:#071018;font-weight:950}.pill.warn{background:var(--yellow);border-color:var(--yellow);color:#071018;font-weight:950}input{width:100%;border:1px solid var(--line);border-radius:12px;background:#050a10;color:var(--ink);padding:10px 12px}.filter{display:grid;grid-template-columns:1fr auto;gap:10px;margin-bottom:16px}.btnx{border:1px solid var(--line);border-radius:12px;background:var(--blue);color:#071018;font-weight:950;padding:10px 14px}@media(max-width:1000px){.metrics,.filter{grid-template-columns:1fr}}
  </style>

  <div class="overage">
    <section class="hero">
      <div class="kicker">Billing preview</div>
      <h1 class="title">Excedentes por uso mensal.</h1>
      <p class="lead">Audite workspaces que passaram do limite do plano e estime receita de excedente antes de automatizar cobranca pelo Mercado Pago.</p>
    </section>

    <form class="filter" method="GET">
      <input name="period" value="{{ $period }}" placeholder="YYYY-MM">
      <button class="btnx" type="submit">Filtrar periodo</button>
    </form>

    <section class="metrics">
      <div class="metric"><div class="kicker">Periodo</div><div class="value">{{ $report['period'] }}</div><div class="muted">Snapshot mensal analisado.</div></div>
      <div class="metric"><div class="kicker">Billable</div><div class="value">{{ $report['billable_items'] }}</div><div class="muted">Workspaces com excedente cobravel.</div></div>
      <div class="metric"><div class="kicker">Excedente</div><div class="value">{{ $report['total_overage'] }}</div><div class="muted">Eventos acima dos limites.</div></div>
      <div class="metric"><div class="kicker">Receita estimada</div><div class="value" style="font-size:32px">{{ $money($report['total_amount_cents']) }}</div><div class="muted">Preview, sem cobranca automatica.</div></div>
    </section>

    <section class="card">
      <div class="kicker">Detalhamento</div>
      @forelse ($report['items'] as $item)
        <article class="rowx {{ $item['billable'] ? 'billable' : '' }}">
          <div class="top">
            <div>
              <strong>{{ $item['workspace']?->name ?? 'Workspace removido' }}</strong>
              <div class="muted">Plano {{ $item['plan']?->name ?? 'Sem plano' }} · {{ $item['period'] }}</div>
            </div>
            <div>
              <span class="pill {{ $item['billable'] ? 'ok' : 'warn' }}">{{ $item['billable'] ? 'cobravel' : 'sem cobranca' }}</span>
              <span class="pill">{{ $item['overage_count'] }} excedente(s)</span>
              <span class="pill">{{ $money($item['price_cents']) }}/evento</span>
              <span class="pill">{{ $money($item['amount_cents']) }}</span>
            </div>
          </div>
        </article>
      @empty
        <div class="muted">Nenhum snapshot encontrado para este periodo. Rode <code>php artisan devlog:snapshot-usage --period={{ $period }}</code>.</div>
      @endforelse
    </section>
  </div>
</x-filament-panels::page>