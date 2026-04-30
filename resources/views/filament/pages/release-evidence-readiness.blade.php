@php
  $report = App\Support\ReleaseEvidenceReadiness::report();
@endphp

<x-filament-panels::page>
  <style>
    .evidence{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;--red:#ff6b6b;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:20px;background:rgba(16,23,32,.92);padding:22px;box-shadow:0 24px 70px rgba(0,0,0,.2)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 82% 8%,rgba(105,227,154,.18),transparent 34%),radial-gradient(circle at 10% 0,rgba(80,184,255,.2),transparent 32%),rgba(16,23,32,.92)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,64px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.68;margin:14px 0 0;max-width:900px}
    .score{font-size:70px;font-weight:950;letter-spacing:-.08em}.grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}.item{border:1px solid var(--line);border-radius:18px;background:#0b1118;padding:16px}.item.done{border-color:rgba(105,227,154,.42);background:rgba(105,227,154,.06)}.item.pending{border-color:rgba(255,209,102,.36);background:rgba(255,209,102,.06)}
    .pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px;display:inline-flex}.done .pill{color:var(--green);border-color:rgba(105,227,154,.5)}.pending .pill{color:var(--yellow);border-color:rgba(255,209,102,.5)}
    .bar{height:12px;border-radius:999px;background:#071018;border:1px solid var(--line);overflow:hidden;margin-top:16px}.fill{height:100%;background:linear-gradient(90deg,var(--blue),var(--green))}
    .where{color:#b7e4ff;font-size:13px;margin-top:10px;font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace}
    @media(max-width:900px){.grid{grid-template-columns:1fr}}
  </style>

  <div class="evidence">
    <section class="hero">
      <div class="kicker">Launch assets</div>
      <div class="score">{{ $report['percent'] }}%</div>
      <h1 class="title">Evidências para mostrar, revisar e lançar.</h1>
      <p class="lead">Este painel separa o que precisa existir como prova de maturidade: páginas públicas, documentação, suporte, checklists, segurança e materiais para avaliação no GitHub Developer Program.</p>
      <div class="bar"><div class="fill" style="width: {{ $report['percent'] }}%"></div></div>
    </section>

    <section class="grid">
      @foreach ($report['items'] as $item)
        <article class="item {{ $item['done'] ? 'done' : 'pending' }}">
          <div class="d-flex justify-content-between gap-2 align-items-start">
            <div>
              <div class="kicker">{{ $item['done'] ? 'Pronto' : 'Pendente' }}</div>
              <h2 class="h5">{{ $item['title'] }}</h2>
            </div>
            <span class="pill">{{ $item['done'] ? 'ok' : 'revisar' }}</span>
          </div>
          <p class="lead">{{ $item['description'] }}</p>
          <div class="where">{{ $item['where'] }}</div>
        </article>
      @endforeach
    </section>
  </div>
</x-filament-panels::page>