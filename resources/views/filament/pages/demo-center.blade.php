@php
  $workspace = \App\Models\Workspace::latest()->first();
  $latestEvent = $workspace?->webhookEvents()->latest()->first();
  $validEvents = \App\Models\WebhookEvent::where('signature_valid', true)->count();
  $launch = \App\Support\OverallLaunchReadiness::report();
  $health = \App\Support\SystemHealth::report();
  $security = \App\Support\SecurityPosture::report();
  $demoChecks = collect([
    ['title' => 'Workspace demo disponível', 'done' => (bool) $workspace, 'detail' => $workspace?->name ?? 'Criar workspace'],
    ['title' => 'Endpoint HTTPS ou tunnel ativo', 'done' => str_starts_with((string) config('app.url'), 'https://'), 'detail' => config('app.url')],
    ['title' => 'Evento GitHub recebido', 'done' => (bool) $latestEvent, 'detail' => $latestEvent?->event_name ?? 'Enviar ping/push'],
    ['title' => 'Assinatura validada', 'done' => $validEvents > 0, 'detail' => $validEvents.' evento(s) validado(s)'],
    ['title' => 'Sistema saudável', 'done' => $health['ok'], 'detail' => $health['ok'] ? 'Health OK' : 'Ver system status'],
    ['title' => 'Segurança revisada', 'done' => $security['percent'] >= 75, 'detail' => $security['percent'].'% de postura'],
    ['title' => 'Launch alto', 'done' => $launch['percent'] >= 90, 'detail' => $launch['percent'].'% de readiness'],
  ]);
  $done = $demoChecks->where('done', true)->count();
  $percent = round(($done / max($demoChecks->count(), 1)) * 100);
@endphp

<x-filament-panels::page>
  <style>
    .demo-center{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;color:var(--ink)}.hero,.card{border:1px solid var(--line);border-radius:22px;background:rgba(16,23,32,.88);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}.hero{display:grid;grid-template-columns:1fr auto;gap:18px;align-items:center;margin-bottom:16px;background:radial-gradient(circle at 90% 0%,rgba(105,227,154,.16),transparent 34%),linear-gradient(135deg,rgba(16,23,32,.96),rgba(9,14,20,.9))}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0}.orb{width:132px;height:132px;border-radius:36px;display:grid;place-items:center;border:1px solid rgba(105,227,154,.32);background:radial-gradient(circle at 35% 25%,rgba(105,227,154,.28),rgba(80,184,255,.12) 44%,rgba(8,16,25,.94) 74%);font-weight:950;font-size:30px}.grid{display:grid;grid-template-columns:1fr 390px;gap:16px}.check-list{display:grid;gap:10px}.check{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}.check.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.badge{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950;color:var(--muted)}.check.done .badge{background:var(--green);border-color:var(--green);color:#071018}.detail{color:var(--muted);font-size:13px}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.actions{display:grid;grid-template-columns:repeat(2,1fr);gap:10px}.action{border:1px solid var(--line);border-radius:12px;background:#0b1118;color:var(--ink);text-decoration:none;padding:12px;font-weight:850}.action.primary{background:var(--blue);border-color:var(--blue);color:#071018}@media(max-width:1100px){.hero,.grid{grid-template-columns:1fr}.actions{grid-template-columns:1fr}}
  </style>

  <div class="demo-center">
    <section class="hero">
      <div>
        <div class="kicker">GitHub Developer Program</div>
        <h1 class="title">Uma demo curta, técnica e vendável.</h1>
        <p class="lead">A demonstração deve provar uma coisa sem enrolar: o GitHub envia um webhook, o DevLog AI valida, organiza, resume com AI e transforma o payload em operação para devs.</p>
      </div>
      <div class="orb">{{ $percent }}%</div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Roteiro de 7 minutos</div>
        <div class="check-list">
          <div class="check done"><div class="badge">1</div><div><strong>Pitch em uma frase</strong><div class="detail">“Debug e auditoria de webhooks GitHub sem expor payload, sem depender de terminal e sem misturar clientes.”</div></div><span class="pill">30s</span></div>
          <div class="check done"><div class="badge">2</div><div><strong>Landing e confiança</strong><div class="detail">Mostrar proposta, segurança, privacidade, suporte, status e GitHub-first.</div></div><span class="pill">1min</span></div>
          <div class="check done"><div class="badge">3</div><div><strong>Workspace demo</strong><div class="detail">Abrir dashboard, checklist, plano, consumo, endpoint e secret.</div></div><span class="pill">1min</span></div>
          <div class="check done"><div class="badge">4</div><div><strong>GitHub enviando evento</strong><div class="detail">Enviar ping/push e mostrar assinatura válida, delivery id, repo, branch e payload sanitizado.</div></div><span class="pill">2min</span></div>
          <div class="check done"><div class="badge">5</div><div><strong>AI e operação</strong><div class="detail">Gerar resumo, risco e próximos passos. Criar nota ou tarefa para o time.</div></div><span class="pill">1min</span></div>
          <div class="check done"><div class="badge">6</div><div><strong>Admin SaaS</strong><div class="detail">Mostrar roadmap, suporte, billing, status, readiness e bloqueadores externos.</div></div><span class="pill">1min</span></div>
          <div class="check done"><div class="badge">7</div><div><strong>Fechamento</strong><div class="detail">Explicar que o produto é SaaS por workspace, com limites por uso e GitHub App oficial.</div></div><span class="pill">30s</span></div>
        </div>
      </div>

      <aside class="card">
        <div class="kicker">Prontidão da demo</div>
        <div class="check-list">
          @foreach ($demoChecks as $check)
            <div class="check {{ $check['done'] ? 'done' : '' }}">
              <div class="badge">{{ $check['done'] ? 'ok' : '' }}</div>
              <div><strong>{{ $check['title'] }}</strong><div class="detail">{{ $check['detail'] }}</div></div>
              <span class="pill">{{ $check['done'] ? 'Pronto' : 'Pendente' }}</span>
            </div>
          @endforeach
        </div>
        <div class="actions" style="margin-top:14px">
          <a class="action primary" href="{{ url('/dashboard') }}">Abrir dashboard</a>
          <a class="action" href="{{ url('/admin/launch-gate') }}">Launch gate</a>
          <a class="action" href="{{ url('/admin/system-status') }}">Status sistema</a>
          <a class="action" href="{{ url('/github') }}" target="_blank">Página GitHub</a>
        </div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>