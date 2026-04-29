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
    $steps = collect([
        [
            'title' => 'Workspace criado',
            'description' => 'Seu ambiente privado ja existe e tem endpoint proprio.',
            'done' => (bool) $workspace,
            'action' => null,
        ],
        [
            'title' => 'Assinatura apta',
            'description' => 'O workspace pode receber eventos dentro do plano atual.',
            'done' => $canUseWebhooks,
            'action' => '#billing',
        ],
        [
            'title' => 'GitHub App conectado',
            'description' => 'Opcional, mas recomendado para uma integracao mais oficial.',
            'done' => (bool) $githubInstallation,
            'action' => route('github.install'),
        ],
        [
            'title' => 'Primeiro evento recebido',
            'description' => 'Envie um ping ou push a partir do GitHub.',
            'done' => $totalEvents > 0,
            'action' => '#setup',
        ],
        [
            'title' => 'Assinatura validada',
            'description' => 'Confirme pelo menos um evento com X-Hub-Signature-256 valido.',
            'done' => $validEvents > 0,
            'action' => '#eventos',
        ],
    ]);

    $doneCount = $steps->where('done', true)->count();
    $percent = round(($doneCount / max($steps->count(), 1)) * 100);
@endphp

<section class="cardx mb-3 onboarding-card">
  <style>
    .onboarding-card{background:linear-gradient(135deg,rgba(80,184,255,.08),rgba(105,227,154,.06),rgba(16,23,32,.92))}
    .onboarding-grid{display:grid;grid-template-columns:280px 1fr;gap:18px;align-items:start}
    .launch-ring{width:164px;height:164px;border-radius:44px;display:grid;place-items:center;background:radial-gradient(circle at 35% 25%,rgba(105,227,154,.28),rgba(80,184,255,.14) 46%,rgba(8,16,25,.96) 74%);border:1px solid rgba(105,227,154,.32);box-shadow:0 24px 70px rgba(0,0,0,.22)}
    .launch-ring strong{font-size:40px;letter-spacing:-.06em}.launch-ring span{display:block;color:var(--muted);font-size:12px;text-align:center;margin-top:-14px}
    .step-list{display:grid;gap:10px}.step-item{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}
    .step-item.done{border-color:rgba(105,227,154,.4);background:rgba(105,227,154,.07)}.step-mark{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);color:var(--muted);font-weight:950}
    .step-item.done .step-mark{background:var(--green);border-color:var(--green);color:#071018}.step-action{color:var(--blue);text-decoration:none;font-weight:850;font-size:13px}.step-action.muted-link{color:var(--muted)}
    @media(max-width:900px){.onboarding-grid{grid-template-columns:1fr}.launch-ring{width:132px;height:132px;border-radius:34px}}
  </style>

  <div class="d-flex justify-content-between gap-3 flex-wrap align-items-start mb-3">
    <div>
      <div class="kicker">Launch checklist</div>
      <h2 class="h4 mt-2 mb-1">Seu workspace esta {{ $percent }}% pronto para operar.</h2>
      <p class="muted mb-0">Siga estes passos para sair de conta criada para webhook validado, auditavel e pronto para demonstracao.</p>
    </div>
    <span class="pill">Assinatura: {{ $subscriptionStatusLabel }}</span>
  </div>

  <div class="onboarding-grid">
    <div>
      <div class="launch-ring">
        <div>
          <strong>{{ $percent }}%</strong>
          <span>{{ $doneCount }} de {{ $steps->count() }} passos</span>
        </div>
      </div>
      <div class="bar-track mt-3"><span class="bar-fill" style="width: {{ $percent }}%"></span></div>
      <div class="muted mt-3" style="word-break:break-all">Endpoint: {{ $endpoint }}</div>
    </div>

    <div class="step-list">
      @foreach ($steps as $step)
        <div class="step-item {{ $step['done'] ? 'done' : '' }}">
          <div class="step-mark">{{ $step['done'] ? 'ok' : $loop->iteration }}</div>
          <div>
            <strong>{{ $step['title'] }}</strong>
            <div class="muted">{{ $step['description'] }}</div>
          </div>
          @if ($step['done'])
            <span class="step-action muted-link">pronto</span>
          @elseif ($step['action'])
            <a class="step-action" href="{{ $step['action'] }}">resolver</a>
          @else
            <span class="step-action muted-link">pendente</span>
          @endif
        </div>
      @endforeach
    </div>
  </div>
</section>
