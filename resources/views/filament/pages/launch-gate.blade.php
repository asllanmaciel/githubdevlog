@php
  use App\Support\LaunchReadiness;
  use App\Support\SecurityPosture;
  use App\Support\SystemHealth;

  $health = SystemHealth::report();
  $security = SecurityPosture::report();
  $launch = LaunchReadiness::report();
  $blockers = $launch['blockers'];
  $strictReady = $health['ok'] && $security['percent'] >= 75 && $launch['percent'] >= 70 && $blockers->isEmpty();
@endphp

<x-filament-panels::page>
  <style>
    .gate{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--danger:#ff6b6b;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}
    .hero{margin-bottom:16px;position:relative;overflow:hidden}.hero:before{content:"";position:absolute;inset:-80px auto auto 55%;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(80,184,255,.22),transparent 64%)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink);position:relative}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:820px;position:relative}
    .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:16px}.two{display:grid;grid-template-columns:1fr 420px;gap:16px}.metric{font-size:42px;font-weight:950;letter-spacing:-.05em}.muted{color:var(--muted);font-size:13px}.ok{color:var(--green)}.bad{color:var(--danger)}.warn{color:var(--yellow)}
    .status{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--line);border-radius:999px;padding:8px 12px;font-weight:900}.status.ready{background:rgba(105,227,154,.1);border-color:rgba(105,227,154,.45);color:var(--green)}.status.blocked{background:rgba(255,107,107,.1);border-color:rgba(255,107,107,.45);color:var(--danger)}
    .blocker,.check{border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px;margin-bottom:10px}.blocker{border-color:rgba(255,107,107,.35)}.blocker strong,.check strong{display:block;margin-bottom:4px}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}.group{margin-bottom:18px}.group h3{font-size:18px;font-weight:950;margin:0 0 10px}.check.done{border-color:rgba(105,227,154,.35);background:rgba(105,227,154,.06)}
    @media(max-width:1100px){.grid,.two{grid-template-columns:1fr}}
  </style>

  <div class="gate">
    <section class="hero">
      <div class="kicker">Release control</div>
      <h1 class="title">Gate de lancamento para deploy, demo e GitHub Program.</h1>
      <p class="lead">Esta tela separa progresso de prontidao real. Pontuacao boa ajuda, mas o lancamento oficial so passa quando saude, seguranca e todos os bloqueadores obrigatorios estiverem resolvidos.</p>
      <div style="margin-top:16px">
        <span class="status {{ $strictReady ? 'ready' : 'blocked' }}">{{ $strictReady ? 'Liberado em modo strict' : 'Bloqueado para lancamento strict' }}</span>
      </div>
    </section>

    <section class="grid">
      <div class="card"><div class="kicker">Saude</div><div class="metric {{ $health['ok'] ? 'ok' : 'bad' }}">{{ $health['ok'] ? 'OK' : '!' }}</div><div class="muted">Banco, cache, rotas e storage.</div></div>
      <div class="card"><div class="kicker">Seguranca</div><div class="metric {{ $security['percent'] >= 75 ? 'ok' : 'warn' }}">{{ $security['percent'] }}%</div><div class="muted">Minimo recomendado para deploy: 75%.</div></div>
      <div class="card"><div class="kicker">Readiness</div><div class="metric {{ $launch['percent'] >= 70 ? 'ok' : 'warn' }}">{{ $launch['percent'] }}%</div><div class="muted">{{ $blockers->count() }} bloqueador(es) obrigatorio(s).</div></div>
    </section>

    <section class="two">
      <div class="card">
        <div class="kicker">Bloqueadores obrigatorios</div>
        @forelse ($blockers as $blocker)
          <div class="blocker"><strong>{{ $blocker['title'] }}</strong><span class="muted">{{ $blocker['detail'] }}</span></div>
        @empty
          <div class="check done"><strong>Nenhum bloqueador ativo</strong><span class="muted">O projeto pode avancar para validacao final de release.</span></div>
        @endforelse

        <div class="kicker" style="margin-top:18px">Checklist por area</div>
        @foreach ($launch['groups'] as $group => $items)
          <div class="group">
            <h3>{{ $group }}</h3>
            @foreach ($items as $item)
              <div class="check {{ $item['done'] ? 'done' : '' }}"><strong>{{ $item['done'] ? '[ok]' : '[pendente]' }} {{ $item['title'] }}</strong><span class="muted">{{ $item['detail'] }}</span></div>
            @endforeach
          </div>
        @endforeach
      </div>

      <aside class="card">
        <div class="kicker">Comando de release</div>
        <p class="muted">Use este comando antes de liberar o dominio para usuarios, enviar a aplicacao ao GitHub Developer Program ou rodar uma demo externa.</p>
        <pre class="cmd">php artisan devlog:preflight --strict</pre>
        <p class="muted">Para pipeline ou automacao:</p>
        <pre class="cmd">php artisan devlog:preflight --strict --json</pre>
        <p class="muted">Se precisar elevar a barra para producao, ajuste os minimos:</p>
        <pre class="cmd">php artisan devlog:preflight --strict --min-security=90 --min-launch=90</pre>
      </aside>
    </section>
  </div>
</x-filament-panels::page>