@php
  $report = \App\Support\AuthSecurity::report();
@endphp

<x-filament-panels::page>
  <style>
    .as{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;color:var(--ink)}
    .as-hero,.as-card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.9);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.as-hero{margin-bottom:16px;background:radial-gradient(circle at 92% 0%,rgba(80,184,255,.16),transparent 34%),rgba(16,23,32,.9)}.as-kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.as-title{font-size:clamp(34px,5vw,58px);line-height:.95;letter-spacing:-.06em;font-weight:950;margin:0}.as-lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}.as-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}.as-metric{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px}.as-value{font-size:34px;font-weight:950;letter-spacing:-.04em}.as-label{color:var(--muted);font-size:13px;line-height:1.5}.as-layout{display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start}.as-list{display:grid;gap:10px}.as-row{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}.as-top{display:flex;justify-content:space-between;gap:12px;align-items:start}.as-pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;white-space:nowrap}.as-pill.ok{background:var(--green);border-color:var(--green);color:#061018;font-weight:950}.as-pill.warn{background:var(--yellow);border-color:var(--yellow);color:#061018;font-weight:950}.as-code{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;color:var(--muted);word-break:break-all}@media(max-width:1000px){.as-grid,.as-layout{grid-template-columns:1fr}}
  </style>

  <div class="as">
    <section class="as-hero">
      <div class="as-kicker">Seguranca / Antifraude</div>
      <h1 class="as-title">Login com freio e trilha de auditoria.</h1>
      <p class="as-lead">Tentativas de login e cadastro agora entram em rate limit por IP/conta, e eventos sensiveis ficam visiveis para investigar abuso antes que vire problema de suporte.</p>
    </section>

    <section class="as-grid">
      <div class="as-metric"><div class="as-kicker">Falhas 24h</div><div class="as-value">{{ $report['failed_24h'] }}</div><div class="as-label">Tentativas de login recusadas.</div></div>
      <div class="as-metric"><div class="as-kicker">Sucessos 24h</div><div class="as-value">{{ $report['success_24h'] }}</div><div class="as-label">Sessões autenticadas.</div></div>
      <div class="as-metric"><div class="as-kicker">Cadastros 24h</div><div class="as-value">{{ $report['registered_24h'] }}</div><div class="as-label">Novos workspaces criados.</div></div>
      <div class="as-metric"><div class="as-kicker">Bloqueios 24h</div><div class="as-value">{{ $report['rate_limited_24h'] }}</div><div class="as-label">Rate limits ativados.</div></div>
    </section>

    <section class="as-layout">
      <div class="as-card">
        <div class="as-kicker">Eventos recentes</div>
        <div class="as-list">
          @forelse ($report['latest_events'] as $event)
            <div class="as-row">
              <div class="as-top">
                <div>
                  <strong>{{ $event->action }}</strong>
                  <div class="as-label">{{ optional($event->created_at)->diffForHumans() }} · {{ $event->ip_address ?: 'IP indisponivel' }}</div>
                  <div class="as-code">{{ data_get($event->metadata, 'email', 'sem email') }}</div>
                </div>
                <span class="as-pill {{ str_contains($event->action, 'success') || str_contains($event->action, 'registered') ? 'ok' : 'warn' }}">{{ str_contains($event->action, 'success') || str_contains($event->action, 'registered') ? 'ok' : 'atenção' }}</span>
              </div>
            </div>
          @empty
            <div class="as-row">
              <strong>Nenhum evento de autenticacao ainda.</strong>
              <div class="as-label">Os primeiros logins, cadastros e bloqueios aparecem aqui automaticamente.</div>
            </div>
          @endforelse
        </div>
      </div>

      <aside class="as-card">
        <div class="as-kicker">IPs suspeitos</div>
        <div class="as-list">
          @forelse ($report['suspicious_ips'] as $ip)
            <div class="as-row">
              <div class="as-top">
                <div>
                  <strong>{{ $ip->ip_address }}</strong>
                  <div class="as-label">{{ $ip->total }} evento(s) de risco em 24h.</div>
                </div>
                <span class="as-pill warn">revisar</span>
              </div>
            </div>
          @empty
            <div class="as-row">
              <strong>Sem concentração suspeita.</strong>
              <div class="as-label">O painel fica em observação para abuso de credencial e cadastro.</div>
            </div>
          @endforelse
        </div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
