@php
  $appUrl = config('app.url');
  $launch = \App\Support\LaunchReadiness::report();
  $security = \App\Support\SecurityPosture::report();
  $health = \App\Support\SystemHealth::report();
  $materials = collect([
    ['title' => 'Landing publica', 'done' => Route::has('home'), 'detail' => url('/')],
    ['title' => 'Docs de usuarios', 'done' => Route::has('docs.users'), 'detail' => url('/docs/usuarios')],
    ['title' => 'Privacidade', 'done' => Route::has('privacy'), 'detail' => url('/privacy')],
    ['title' => 'Termos', 'done' => Route::has('terms'), 'detail' => url('/terms')],
    ['title' => 'Centro de demo', 'done' => true, 'detail' => url('/admin/demo-center')],
    ['title' => 'Roteiro markdown', 'done' => file_exists(base_path('docs/github-developer-program-submission.md')), 'detail' => 'docs/github-developer-program-submission.md'],
    ['title' => 'Readiness acima de 70%', 'done' => $launch['percent'] >= 70, 'detail' => $launch['percent'].'%'],
    ['title' => 'Seguranca acima de 75%', 'done' => $security['percent'] >= 75, 'detail' => $security['percent'].'%'],
    ['title' => 'Health OK', 'done' => $health['ok'], 'detail' => $health['ok'] ? 'OK' : 'Verificar status'],
  ]);
  $done = $materials->where('done', true)->count();
  $percent = round(($done / max($materials->count(), 1)) * 100);
@endphp

<x-filament-panels::page>
  <style>
    .submission{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{display:grid;grid-template-columns:1fr auto;gap:18px;align-items:center;margin-bottom:16px}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0}.orb{width:132px;height:132px;border-radius:36px;display:grid;place-items:center;border:1px solid rgba(105,227,154,.32);background:radial-gradient(circle at 35% 25%,rgba(105,227,154,.28),rgba(80,184,255,.12) 44%,rgba(8,16,25,.94) 74%);font-weight:950;font-size:30px}
    .grid{display:grid;grid-template-columns:1fr 420px;gap:16px}.copy{display:grid;gap:12px}.copy-block{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px}.copy-block strong{display:block;margin-bottom:8px}.copy-block p{color:var(--muted);line-height:1.65;margin:0}.check{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}.check.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.badge{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950;color:var(--muted)}.check.done .badge{background:var(--green);border-color:var(--green);color:#071018}.detail{color:var(--muted);font-size:13px}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}@media(max-width:1100px){.hero,.grid{grid-template-columns:1fr}}
  </style>

  <div class="submission">
    <section class="hero">
      <div>
        <div class="kicker">GitHub Developer Program</div>
        <h1 class="title">Pacote de submissao pronto para lapidar.</h1>
        <p class="lead">Textos, links, proposta e checklist para transformar o produto em uma apresentacao objetiva para o ecossistema GitHub.</p>
      </div>
      <div class="orb">{{ $percent }}%</div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Copy principal</div>
        <div class="copy">
          <div class="copy-block"><strong>One-liner</strong><p>GitHub DevLog AI e um inbox privado para webhooks do GitHub, com validacao de assinatura, payload sanitizado e painel de debug por workspace.</p></div>
          <div class="copy-block"><strong>Dor</strong><p>Desenvolvedores perdem tempo tentando descobrir se o GitHub chamou o endpoint, qual payload chegou e se a assinatura era confiavel.</p></div>
          <div class="copy-block"><strong>Solucao</strong><p>O DevLog AI organiza eventos GitHub em um workspace privado, valida HMAC, mostra delivery id, repositorio, commits e permite transformar eventos em notas e tarefas.</p></div>
          <div class="copy-block"><strong>Publico</strong><p>Devs criando GitHub Apps, times com automacoes por webhook, SaaS que integram com GitHub e consultorias que precisam demonstrar fluxos reais.</p></div>
          <div class="copy-block"><strong>URL base atual</strong><p>{{ $appUrl }}</p></div>
        </div>
      </div>

      <aside class="card">
        <div class="kicker">Materiais de submissao</div>
        <div class="copy">
          @foreach ($materials as $item)
            <div class="check {{ $item['done'] ? 'done' : '' }}">
              <div class="badge">{{ $item['done'] ? 'ok' : '' }}</div>
              <div><strong>{{ $item['title'] }}</strong><div class="detail">{{ $item['detail'] }}</div></div>
              <span class="pill">{{ $item['done'] ? 'Pronto' : 'Pendente' }}</span>
            </div>
          @endforeach
        </div>
      </aside>
    </section>
  </div>
</x-filament-panels::page>
