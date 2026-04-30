@php
  $report = App\Support\OverallLaunchReadiness::report();
@endphp

<x-filament-panels::page>
  <style>
    .launch{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;--red:#ff6b6b;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:20px;background:rgba(16,23,32,.92);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 82% 8%,rgba(105,227,154,.18),transparent 34%),radial-gradient(circle at 10% 0,rgba(80,184,255,.2),transparent 32%),rgba(16,23,32,.92)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(38px,6vw,72px);line-height:.9;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.68;margin:14px 0 0;max-width:940px}
    .score{font-size:72px;font-weight:950;letter-spacing:-.08em}.grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;margin-bottom:16px}.area{border:1px solid var(--line);border-radius:18px;background:#0b1118;padding:16px}.value{font-size:34px;font-weight:950;letter-spacing:-.05em}.label{color:var(--muted);font-size:13px}.bar{height:10px;border-radius:999px;background:#071018;border:1px solid var(--line);overflow:hidden;margin-top:12px}.fill{height:100%;background:linear-gradient(90deg,var(--blue),var(--green))}
    .content{display:grid;grid-template-columns:1fr 420px;gap:16px;align-items:start}.blocker{border:1px solid rgba(255,209,102,.32);background:rgba(255,209,102,.06);border-radius:16px;padding:14px;margin-bottom:10px}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;display:inline-flex}
    @media(max-width:1200px){.grid{grid-template-columns:repeat(2,1fr)}}@media(max-width:900px){.grid,.content{grid-template-columns:1fr}}
  </style>

  <div class="launch">
    <section class="hero">
      <div class="kicker">Placar geral</div>
      <div class="score">{{ $report['percent'] }}%</div>
      <h1 class="title">Prontidão geral de lançamento.</h1>
      <p class="lead">Este placar combina beta/local, go-live, GitHub Developer Program, evidências de release e progresso do roadmap. Ele ajuda a separar maturidade real de produto dos bloqueadores externos de produção.</p>
    </section>

    <section class="grid">
      @foreach ($report['areas'] as $area)
        <div class="area">
          <div class="d-flex justify-content-between gap-2 align-items-start">
            <div>
              <div class="kicker">{{ $area['title'] }}</div>
              <div class="value">{{ $area['percent'] }}%</div>
            </div>
            <span class="pill">{{ $area['status'] }}</span>
          </div>
          <div class="label">{{ $area['detail'] }}</div>
          <div class="bar"><div class="fill" style="width: {{ $area['percent'] }}%"></div></div>
        </div>
      @endforeach
    </section>

    <section class="content">
      <div class="card">
        <div class="kicker">Leitura executiva</div>
        <h2>{{ $report['ready_for_public_launch'] ? 'Produto apto para preparar release público.' : 'Produto maduro, mas ainda bloqueado para live público.' }}</h2>
        <p class="lead">A média geral está em {{ $report['percent'] }}%. O roadmap está em {{ $report['roadmap']['percent'] }}%, com {{ $report['roadmap']['done'] }} de {{ $report['roadmap']['total'] }} itens concluídos.</p>
        <p class="lead">As evidências de release estão em {{ $report['release_evidence']['percent'] }}%, com {{ $report['release_evidence']['done'] }} de {{ $report['release_evidence']['total'] }} materiais prontos para revisão externa.</p>
        <p class="lead">O principal gargalo agora não é funcionalidade local: são dependências externas como domínio, GitHub App oficial, Mercado Pago em produção e e-mail transacional real.</p>
      </div>

      <aside class="card">
        <div class="kicker">Bloqueadores de go-live</div>
        @forelse ($report['go_live_blockers'] as $blocker)
          <div class="blocker">
            <strong>{{ $blocker['title'] }}</strong>
            <div class="label">{{ $blocker['detail'] }}</div>
            <div class="label">Próximo passo: {{ $blocker['nextAction'] }}</div>
          </div>
        @empty
          <div class="area">
            <strong>Nenhum bloqueador crítico</strong>
            <div class="label">O produto pode entrar em preparação final de release público.</div>
          </div>
        @endforelse
      </aside>
    </section>
  </div>
</x-filament-panels::page>