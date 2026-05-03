@php
  $report = \App\Support\WebhookDeliveryHardening::report();
@endphp

<x-filament-panels::page>
  <style>
    .wh{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--red:#ff8a8a;color:var(--ink)}
    .wh-hero,.wh-card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.9);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.wh-hero{margin-bottom:16px;background:radial-gradient(circle at 92% 0%,rgba(105,227,154,.16),transparent 34%),rgba(16,23,32,.9)}.wh-kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.wh-title{font-size:clamp(34px,5vw,58px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.wh-lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:920px}.wh-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}.wh-metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.wh-value{font-size:34px;font-weight:950;letter-spacing:-.04em}.wh-label{color:var(--muted);font-size:13px;line-height:1.5}.wh-layout{display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start}.wh-list{display:grid;gap:10px}.wh-row{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}.wh-row-top{display:flex;justify-content:space-between;gap:12px;align-items:start}.wh-pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;white-space:nowrap}.wh-pill.ok{background:var(--green);border-color:var(--green);color:#061018;font-weight:950}.wh-pill.warn{background:var(--red);border-color:var(--red);color:#1b0808;font-weight:950}.wh-code{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;color:var(--muted);word-break:break-all}@media(max-width:1000px){.wh-grid,.wh-layout{grid-template-columns:1fr}}
  </style>

  <div class="wh">
    <section class="wh-hero">
      <div class="wh-kicker">Confiabilidade / Entrada critica</div>
      <h1 class="wh-title">Webhooks com auditoria e idempotencia.</h1>
      <p class="wh-lead">Eventos validos sao deduplicados por entrega; rejeicoes ficam registradas com payload minimo para investigar assinatura, replay e configuracao sem guardar conteudo sensivel desnecessario.</p>
    </section>

    <section class="wh-grid">
      <div class="wh-metric"><div class="wh-kicker">Total</div><div class="wh-value">{{ $report['total'] }}</div><div class="wh-label">Eventos recebidos no banco.</div></div>
      <div class="wh-metric"><div class="wh-kicker">Aceitos</div><div class="wh-value">{{ $report['accepted'] }}</div><div class="wh-label">Assinatura valida e processamento idempotente.</div></div>
      <div class="wh-metric"><div class="wh-kicker">Rejeitados</div><div class="wh-value">{{ $report['rejected'] }}</div><div class="wh-label">Falhas registradas para diagnostico.</div></div>
      <div class="wh-metric"><div class="wh-kicker">Validade</div><div class="wh-value">{{ $report['valid_rate'] }}%</div><div class="wh-label">Taxa historica de assinaturas validas.</div></div>
    </section>

    <section class="wh-layout">
      <div class="wh-card">
        <div class="wh-kicker">Ultimas falhas</div>
        <div class="wh-list">
          @forelse ($report['latest_failures'] as $event)
            <div class="wh-row">
              <div class="wh-row-top">
                <div>
                  <strong>{{ $event->source }} / {{ $event->event_name }}</strong>
                  <div class="wh-label">{{ $event->failure_reason ?: 'signature_invalid' }} · {{ optional($event->received_at)->diffForHumans() }}</div>
                  <div class="wh-code">{{ $event->delivery_id ?: $event->dedupe_key }}</div>
                </div>
                <span class="wh-pill warn">rejeitado</span>
              </div>
            </div>
          @empty
            <div class="wh-row">
              <strong>Nenhuma falha registrada.</strong>
              <div class="wh-label">Quando uma assinatura invalida chegar, ela aparece aqui sem expor payload bruto.</div>
            </div>
          @endforelse
        </div>
      </div>

      <aside class="wh-card">
        <div class="wh-kicker">Estado 24h</div>
        <div class="wh-list">
          <div class="wh-row">
            <div class="wh-row-top">
              <div>
                <strong>{{ $report['invalid_24h'] }} rejeicao(oes)</strong>
                <div class="wh-label">Falhas recentes exigem conferir secret, header X-Hub-Signature-256 e URL configurada.</div>
              </div>
              <span class="wh-pill {{ $report['needs_attention'] ? 'warn' : 'ok' }}">{{ $report['needs_attention'] ? 'atenção' : 'ok' }}</span>
            </div>
          </div>
          <div class="wh-row">
            <strong>Proximo passo tecnico</strong>
            <div class="wh-label">Separar reprocessamento controlado em fila dedicada quando houver volume real e SLAs definidos.</div>
          </div>
        </div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
