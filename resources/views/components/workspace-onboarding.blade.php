@props([
    'workspace',
    'endpoint',
    'githubInstallation',
    'totalEvents' => 0,
    'validEvents' => 0,
    'subscriptionStatusLabel' => 'Trial',
    'canUseWebhooks' => true,
])

@php
    $recommendedEvents = ['push', 'pull_request', 'workflow_run', 'issues'];
    $manualMode = ! $githubInstallation;
    $steps = collect([
        ['title' => 'Copiar Payload URL', 'description' => 'Use a URL privada do workspace no GitHub.', 'done' => filled($endpoint), 'action' => '#setup'],
        ['title' => 'Definir Content type', 'description' => 'Escolha application/json para receber payload estruturado.', 'done' => true, 'action' => '#setup'],
        ['title' => 'Configurar Secret', 'description' => 'Cole o secret do workspace para validar X-Hub-Signature-256.', 'done' => filled($workspace?->webhook_secret), 'action' => '#setup'],
        ['title' => 'Selecionar eventos', 'description' => 'Comece com push, pull_request, workflow_run e issues.', 'done' => $totalEvents > 0, 'action' => '#setup'],
        ['title' => 'Enviar ping ou push', 'description' => 'O primeiro evento deve aparecer no histórico privado.', 'done' => $totalEvents > 0, 'action' => '#eventos'],
        ['title' => 'Confirmar assinatura', 'description' => 'Ao menos um evento precisa chegar com assinatura válida.', 'done' => $validEvents > 0, 'action' => '#eventos'],
    ]);
    $doneCount = $steps->where('done', true)->count();
    $percent = round(($doneCount / max($steps->count(), 1)) * 100);
    $curlPayload = '{"event":"push","repository":{"full_name":"demo/repo"},"pusher":{"name":"dev"}}';
@endphp

<section class="cardx mb-3 onboarding-card" id="onboarding">
  <style>
    .onboarding-card{background:linear-gradient(135deg,rgba(80,184,255,.08),rgba(105,227,154,.06),rgba(16,23,32,.92))}.onboarding-grid{display:grid;grid-template-columns:300px 1fr;gap:18px;align-items:start}.launch-ring{width:164px;height:164px;border-radius:44px;display:grid;place-items:center;background:radial-gradient(circle at 35% 25%,rgba(105,227,154,.28),rgba(80,184,255,.14) 46%,rgba(8,16,25,.96) 74%);border:1px solid rgba(105,227,154,.32);box-shadow:0 24px 70px rgba(0,0,0,.22)}.launch-ring strong{font-size:40px;letter-spacing:-.06em}.launch-ring span{display:block;color:var(--muted);font-size:12px;text-align:center;margin-top:-14px}.step-list{display:grid;gap:10px}.step-item{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}.step-item.done{border-color:rgba(105,227,154,.4);background:rgba(105,227,154,.07)}.step-mark{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);color:var(--muted);font-weight:950}.step-item.done .step-mark{background:var(--green);border-color:var(--green);color:#071018}.step-action{color:var(--blue);text-decoration:none;font-weight:850;font-size:13px}.step-action.muted-link{color:var(--muted)}.setup-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:10px;margin-top:14px}.setup-box{border:1px solid var(--line);border-radius:14px;background:#07101a;padding:12px}.setup-box strong{display:block;margin-bottom:6px}.copyline{word-break:break-all;color:#b7e4ff}.event-chip{display:inline-flex;margin:4px 5px 0 0;border:1px solid rgba(80,184,255,.35);border-radius:999px;padding:5px 9px;color:#b7e4ff;font-size:12px}.curl-box{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto;margin-top:12px}@media(max-width:1000px){.onboarding-grid,.setup-grid{grid-template-columns:1fr}.launch-ring{width:132px;height:132px;border-radius:34px}}
  </style>

  <div class="d-flex justify-content-between gap-3 flex-wrap align-items-start mb-3">
    <div>
      <div class="kicker">Webhook onboarding</div>
      <h2 class="h4 mt-2 mb-1">Configure, teste e valide seu primeiro webhook GitHub.</h2>
      <p class="muted mb-0">Este guia leva o dev de workspace criado para evento GitHub validado sem caçar configuração em documentação solta.</p>
    </div>
    <span class="pill">Modo: {{ $manualMode ? 'Webhook manual' : 'GitHub App conectado' }}</span>
  </div>

  <div class="onboarding-grid">
    <div>
      <div class="launch-ring"><div><strong>{{ $percent }}%</strong><span>{{ $doneCount }} de {{ $steps->count() }} passos</span></div></div>
      <div class="bar-track mt-3"><span class="bar-fill" style="width: {{ $percent }}%"></span></div>
      <div class="muted mt-3">Assinatura: {{ $subscriptionStatusLabel }}</div>
      <div class="muted">Eventos recebidos: {{ $totalEvents }} · validados: {{ $validEvents }}</div>
    </div>

    <div>
      <div class="setup-grid">
        <div class="setup-box"><strong>Payload URL</strong><div class="copyline">{{ $endpoint }}</div></div>
        <div class="setup-box"><strong>Content type</strong><div class="copyline">application/json</div></div>
        <div class="setup-box"><strong>Secret</strong><div class="copyline">{{ $workspace?->webhook_secret }}</div></div>
        <div class="setup-box"><strong>Eventos recomendados</strong>@foreach($recommendedEvents as $event)<span class="event-chip">{{ $event }}</span>@endforeach</div>
      </div>

      <div class="step-list mt-3">
        @foreach ($steps as $step)
          <div class="step-item {{ $step['done'] ? 'done' : '' }}">
            <div class="step-mark">{{ $step['done'] ? 'ok' : $loop->iteration }}</div>
            <div><strong>{{ $step['title'] }}</strong><div class="muted">{{ $step['description'] }}</div></div>
            @if ($step['done'])<span class="step-action muted-link">pronto</span>@else<a class="step-action" href="{{ $step['action'] }}">resolver</a>@endif
          </div>
        @endforeach
      </div>

      <details class="mt-3">
        <summary class="step-action">Ver teste local com assinatura HMAC</summary>
        <pre class="curl-box">payload='{{ $curlPayload }}'
signature="sha256=$(printf "$payload" | openssl dgst -sha256 -hmac '{{ $workspace?->webhook_secret }}' -binary | xxd -p -c 256)"
curl -X POST '{{ $endpoint }}' \
  -H "Content-Type: application/json" \
  -H "X-GitHub-Event: push" \
  -H "X-GitHub-Delivery: local-test-$(date +%s)" \
  -H "X-Hub-Signature-256: $signature" \
  -d "$payload"</pre>
      </details>
    </div>
  </div>
</section>