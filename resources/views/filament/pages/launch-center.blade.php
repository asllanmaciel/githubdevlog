@php
  $report = \App\Support\LaunchReadiness::report();
  $launchTests = class_exists(\App\Models\LaunchTest::class) ? \App\Models\LaunchTest::all() : collect();
  $launchTestsDone = $launchTests->where('status', 'passed')->count();
  $launchTestsPercent = round(($launchTestsDone / max($launchTests->count(), 1)) * 100);
@endphp

<x-filament-panels::page>
  <style>
    .launch-center{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffcf66;color:var(--ink)}
    .launch-hero{display:grid;grid-template-columns:1.1fr .9fr;gap:16px;margin-bottom:16px}
    .launch-card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18);position:relative;overflow:hidden}
    .launch-card:after{content:"";position:absolute;right:-46px;top:-46px;width:140px;height:140px;border-radius:50%;background:rgba(80,184,255,.12)}
    .launch-card>*{position:relative}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}
    .title{font-size:clamp(34px,5vw,64px);line-height:.94;letter-spacing:-.065em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:860px}
    .orb{width:144px;height:144px;border-radius:38px;display:grid;place-items:center;background:radial-gradient(circle at 35% 25%,rgba(105,227,154,.28),rgba(80,184,255,.13) 44%,rgba(8,16,25,.94) 74%);border:1px solid rgba(105,227,154,.3);font-size:36px;font-weight:950}
    .bar{height:10px;border-radius:999px;background:#0b1118;border:1px solid var(--line);overflow:hidden}.bar span{display:block;height:100%;background:linear-gradient(90deg,var(--blue),var(--green));border-radius:999px}
    .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.check-list{display:grid;gap:10px}.check{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}
    .check.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.badge{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950;color:var(--muted)}
    .check.done .badge{background:var(--green);border-color:var(--green);color:#071018}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.detail{color:var(--muted);font-size:13px;line-height:1.5}.blocker{border-color:rgba(255,207,102,.45);background:rgba(255,207,102,.07)}
    @media(max-width:1100px){.launch-hero,.grid{grid-template-columns:1fr}}@media(max-width:720px){.check{grid-template-columns:auto 1fr}.check .pill{grid-column:1/-1}}
  </style>

  <div class="launch-center">
    <section class="launch-hero">
      <div class="launch-card">
        <div class="kicker">Release control</div>
        <h1 class="title">Centro estrutural para lancar o DevLog AI.</h1>
        <p class="lead">Uma leitura consolidada dos blocos essenciais antes de apresentar o produto: infraestrutura, billing, GitHub App, webhooks, documentacao, suporte e roadmap.</p>
      </div>
      <div class="launch-card" style="display:flex;justify-content:space-between;gap:18px;align-items:center">
        <div>
          <div class="kicker">Launch score</div>
          <div style="font-size:36px;font-weight:950;letter-spacing:-.05em">{{ $report['percent'] }}%</div>
          <div class="detail">{{ $report['done'] }} de {{ $report['total'] }} checks prontos</div>
          <div class="bar" style="margin-top:18px"><span style="width:{{ $report['percent'] }}%"></span></div>
        </div>
        <div class="orb">{{ $report['percent'] }}%</div>
      </div>
    </section>

    @if ($report['blockers']->isNotEmpty())
      <section class="launch-card blocker" style="margin-bottom:16px">
        <div class="kicker">Bloqueadores atuais</div>
        <div class="check-list">
          @foreach ($report['blockers']->take(6) as $blocker)
            <div class="check">
              <div class="badge">!</div>
              <div><strong>{{ $blocker['title'] }}</strong><div class="detail">{{ $blocker['detail'] }}</div></div>
              <span class="pill">Pendente</span>
            </div>
          @endforeach
        </div>
      </section>
    @endif

    <section class="launch-card" style="margin-bottom:16px">
      <div class="kicker">QA de lancamento</div>
      <div style="display:flex;justify-content:space-between;gap:18px;align-items:center;flex-wrap:wrap">
        <div>
          <div style="font-size:32px;font-weight:950;letter-spacing:-.05em">{{ $launchTestsPercent }}%</div>
          <div class="detail">{{ $launchTestsDone }} de {{ $launchTests->count() }} testes aprovados</div>
        </div>
        <a class="pill" href="{{ url('/admin/launch-tests') }}">Abrir QA de lancamento</a>
      </div>
      <div class="bar" style="margin-top:14px"><span style="width:{{ $launchTestsPercent }}%"></span></div>
    </section>

    <section class="grid">
      @foreach ($report['groups'] as $group => $items)
        <article class="launch-card">
          <div class="kicker">{{ $group }}</div>
          <div class="check-list">
            @foreach ($items as $item)
              <div class="check {{ $item['done'] ? 'done' : '' }}">
                <div class="badge">{{ $item['done'] ? 'ok' : '' }}</div>
                <div><strong>{{ $item['title'] }}</strong><div class="detail">{{ $item['detail'] }}</div></div>
                <span class="pill">{{ $item['done'] ? 'Pronto' : 'Pendente' }}</span>
              </div>
            @endforeach
          </div>
        </article>
      @endforeach
    </section>
  </div>
</x-filament-panels::page>
