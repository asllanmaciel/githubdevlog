@php
  $setup = \App\Support\MercadoPagoSetup::report();
@endphp

<x-filament-panels::page>
  <style>
    .mp-ready{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;color:var(--ink)}
    .mp-hero{display:grid;grid-template-columns:1.1fr .9fr;gap:16px;margin-bottom:16px}.mp-card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.9);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18);position:relative;overflow:hidden}.mp-card:after{content:"";position:absolute;right:-56px;top:-56px;width:160px;height:160px;border-radius:50%;background:rgba(80,184,255,.1)}.mp-card>*{position:relative}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(32px,4.6vw,58px);line-height:.96;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0}.score{font-size:52px;font-weight:950;letter-spacing:-.06em}.progress{height:10px;border-radius:999px;background:#0b1118;border:1px solid var(--line);overflow:hidden}.progress span{display:block;height:100%;background:linear-gradient(90deg,var(--blue),var(--green));border-radius:999px}.metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.value{font-size:32px;font-weight:950;letter-spacing:-.04em}.label{color:var(--muted);font-size:13px;line-height:1.5}.layout{display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start}.row{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:start;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px;margin-bottom:10px}.row.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.mark{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950;color:var(--yellow)}.row.done .mark{background:var(--green);border-color:var(--green);color:#071018}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;white-space:nowrap}.copy{border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:12px;margin-bottom:10px}.copy code{display:block;margin-top:6px;word-break:break-all;color:#b7e4ff;white-space:pre-wrap}.actions{display:grid;gap:10px}.action{border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:12px;text-decoration:none;color:var(--ink);font-weight:850}.action.primary{background:var(--blue);border-color:var(--blue);color:#071018}@media(max-width:1100px){.mp-hero,.layout{grid-template-columns:1fr}.metrics{grid-template-columns:1fr}}@media(max-width:720px){.row{grid-template-columns:auto 1fr}.row .pill{grid-column:1/-1}}
  </style>

  <div class="mp-ready">
    <section class="mp-hero">
      <div class="mp-card">
        <div class="kicker">Billing / Producao</div>
        <h1 class="title">Mercado Pago pronto antes de vender.</h1>
        <p class="lead">Checklist para virar do sandbox para cobranca real com webhook assinado, planos ativos, URLs corretas e teste controlado de baixo valor.</p>
      </div>
      <div class="mp-card">
        <div class="kicker">Readiness score</div>
        <div class="score">{{ $setup['percent'] }}%</div>
        <div class="label">{{ $setup['summary']['done'] }} de {{ $setup['summary']['total'] }} etapas prontas</div>
        <div class="progress" style="margin-top:18px"><span style="width:{{ $setup['percent'] }}%"></span></div>
      </div>
    </section>

    <section class="metrics">
      <div class="metric"><div class="value">{{ $setup['summary']['active_plans'] }}</div><div class="label">planos ativos</div></div>
      <div class="metric"><div class="value">{{ $setup['summary']['valid_events'] }}</div><div class="label">webhooks validos</div></div>
      <div class="metric"><div class="value">{{ $setup['summary']['failed_events'] }}</div><div class="label">eventos com erro</div></div>
    </section>

    <section class="layout">
      <div class="mp-card">
        <div class="kicker">Checklist operacional</div>
        @foreach ($setup['steps'] as $step)
          <div class="row {{ $step['done'] ? 'done' : '' }}">
            <div class="mark">{{ $step['done'] ? 'ok' : '!' }}</div>
            <div>
              <strong>{{ $step['title'] }}</strong>
              <div class="label">{{ $step['detail'] }}</div>
            </div>
            <span class="pill">{{ $step['done'] ? 'Pronto' : 'Pendente' }}</span>
          </div>
        @endforeach

        <div class="kicker" style="margin-top:18px">Variaveis .env</div>
        @foreach ($setup['env'] as $env)
          <div class="row {{ $env['done'] ? 'done' : '' }}">
            <div class="mark">{{ $env['done'] ? 'ok' : '!' }}</div>
            <div>
              <strong>{{ $env['key'] }}</strong>
              <div class="label">{{ $env['description'] }}</div>
              <div class="label">Valor: {{ $env['value'] }}</div>
            </div>
            <span class="pill">{{ $env['done'] ? 'OK' : 'Pendente' }}</span>
          </div>
        @endforeach
      </div>

      <aside class="mp-card">
        <div class="kicker">Valores para colar</div>
        @foreach ($setup['urls'] as $url)
          <div class="copy">
            <strong>{{ $url['label'] }}</strong>
            <code>{{ $url['value'] }}</code>
            <div class="label">{{ $url['description'] }}</div>
          </div>
        @endforeach

        <div class="kicker" style="margin-top:18px">Snippet .env</div>
        <div class="copy">
          <strong>Producao</strong>
          <code>{{ $setup['env_snippet'] }}</code>
        </div>

        <div class="kicker" style="margin-top:18px">Teste controlado</div>
        @foreach ($setup['test_plan'] as $item)
          <div class="copy"><div class="label">{{ $item }}</div></div>
        @endforeach

        <div class="kicker" style="margin-top:18px">Atalhos</div>
        <div class="actions">
          <a class="action primary" href="{{ url('/pricing') }}" target="_blank">Abrir precos</a>
          <a class="action" href="{{ url('/admin/billing-events') }}">Eventos de billing</a>
          <a class="action" href="{{ url('/admin/billing-plans') }}">Planos</a>
          <a class="action" href="{{ url('/webhooks/mercado-pago') }}" target="_blank">Health do webhook</a>
        </div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
