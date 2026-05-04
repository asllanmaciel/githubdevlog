@php
  $report = \App\Support\LaunchReadiness::report();
  $launchTests = class_exists(\App\Models\LaunchTest::class) ? \App\Models\LaunchTest::all() : collect();
  $launchTestsDone = $launchTests->where('status', 'passed')->count();
  $launchTestsPercent = round(($launchTestsDone / max($launchTests->count(), 1)) * 100);

  $icons = [
    'rocket' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.5 16.5 3 21l4.5-1.5"></path><path d="M9 15 5 11l6-6c3-3 7-3 8-2 1 1 1 5-2 8l-6 6-4-4"></path><path d="M14 6h.01"></path></svg>',
    'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>',
    'warn' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.3 3.9 2.6 17.5A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.5L13.7 3.9a2 2 0 0 0-3.4 0Z"></path></svg>',
    'test' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 2v6l-4 8a4 4 0 0 0 3.6 6h6.8a4 4 0 0 0 3.6-6l-4-8V2"></path><path d="M8 2h8"></path><path d="M7 16h10"></path></svg>',
    'link' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.1 0l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1"></path><path d="M14 11a5 5 0 0 0-7.1 0l-2 2A5 5 0 0 0 12 20.1l1.1-1.1"></path></svg>',
  ];
@endphp

<x-filament-panels::page>
  <style>
    .launch-center{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffcf66;color:var(--ink)}
    .launch-hero{display:grid;grid-template-columns:1.1fr .9fr;gap:16px;margin-bottom:16px}
    .launch-card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18);position:relative;overflow:hidden}
    .launch-card:after{content:"";position:absolute;right:-46px;top:-46px;width:140px;height:140px;border-radius:50%;background:rgba(80,184,255,.12)}
    .launch-card>*{position:relative}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}
    .title{font-size:clamp(34px,5vw,64px);line-height:.94;letter-spacing:-.065em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:860px}
    .hero-head{display:flex;gap:16px;align-items:flex-start}.icon{width:46px;height:46px;border-radius:16px;display:grid;place-items:center;border:1px solid rgba(80,184,255,.34);background:rgba(80,184,255,.1);color:#b7e4ff;flex:0 0 auto}.mini-icon{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);background:#071018;color:var(--blue);flex:0 0 auto}.mini-icon.ok{background:var(--green);border-color:var(--green);color:#071018}.mini-icon.warn{color:var(--yellow);border-color:rgba(255,207,102,.45);background:rgba(255,207,102,.08)}.icon svg,.mini-icon svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}
    .orb{width:144px;height:144px;border-radius:38px;display:grid;place-items:center;background:radial-gradient(circle at 35% 25%,rgba(105,227,154,.28),rgba(80,184,255,.13) 44%,rgba(8,16,25,.94) 74%);border:1px solid rgba(105,227,154,.3);font-size:36px;font-weight:950}
    .bar{height:10px;border-radius:999px;background:#0b1118;border:1px solid var(--line);overflow:hidden}.bar span{display:block;height:100%;background:linear-gradient(90deg,var(--blue),var(--green));border-radius:999px}
    .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.check-list{display:grid;gap:10px}.check{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}
    .check.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;text-decoration:none}.detail{color:var(--muted);font-size:13px;line-height:1.5}.blocker{border-color:rgba(255,207,102,.45);background:rgba(255,207,102,.07)}
    .action-pill{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--line);border-radius:999px;padding:8px 12px;color:var(--ink);font-size:12px;text-decoration:none;background:#0b1118;font-weight:850}
    @media(max-width:1100px){.launch-hero,.grid{grid-template-columns:1fr}}@media(max-width:720px){.hero-head{display:block}.hero-head .icon{margin-bottom:14px}.check{grid-template-columns:auto 1fr}.check .pill{grid-column:1/-1}}
  </style>

  <div class="launch-center">
    <section class="launch-hero">
      <div class="launch-card">
        <div class="hero-head">
          <div class="icon">{!! $icons['rocket'] !!}</div>
          <div>
            <div class="kicker">Release control</div>
            <h1 class="title">Centro estrutural para lancar o DevLog AI.</h1>
            <p class="lead">Uma leitura consolidada dos blocos essenciais antes de apresentar o produto: infraestrutura, billing, GitHub App, webhooks, documentacao, suporte e roadmap.</p>
          </div>
        </div>
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
              <div class="mini-icon warn">{!! $icons['warn'] !!}</div>
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
        <a class="action-pill" href="{{ url('/admin/launch-tests') }}"><span class="mini-icon">{!! $icons['test'] !!}</span>Abrir QA de lancamento</a>
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
                <div class="mini-icon {{ $item['done'] ? 'ok' : 'warn' }}">{!! $item['done'] ? $icons['check'] : $icons['warn'] !!}</div>
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
