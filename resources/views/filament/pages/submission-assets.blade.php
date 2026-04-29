@php
  $report = App\Support\SubmissionAssets::report();
@endphp

<x-filament-panels::page>
  <style>
    .assets{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;--danger:#ff6b6b;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}.hero{margin-bottom:16px;background:radial-gradient(circle at 85% 12%,rgba(80,184,255,.18),transparent 28%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:860px}.metrics{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:16px}.metric{border:1px solid var(--line);border-radius:18px;background:#0b1118;padding:18px}.value{font-size:42px;font-weight:950;letter-spacing:-.05em}.muted{color:var(--muted);font-size:13px;line-height:1.55}.grid{display:grid;grid-template-columns:1fr 390px;gap:16px}.asset{border:1px solid var(--line);border-radius:16px;background:#0b1118;padding:16px;margin-bottom:12px}.asset.passed{border-color:rgba(105,227,154,.42);background:rgba(105,227,154,.06)}.asset.blocked,.asset.failed{border-color:rgba(255,107,107,.42)}.asset h3{font-size:18px;font-weight:950;margin:0 0 8px}.row{display:flex;gap:8px;flex-wrap:wrap;margin:10px 0}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.pill.ok{color:#071018;background:var(--green);border-color:var(--green);font-weight:950}.pill.warn{color:#071018;background:var(--yellow);border-color:var(--yellow);font-weight:950}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}.empty{border:1px dashed var(--line);border-radius:16px;padding:20px;color:var(--muted)}@media(max-width:1100px){.grid,.metrics{grid-template-columns:1fr}}  
  </style>

  <div class="assets">
    <section class="hero">
      <div class="kicker">Submission evidence</div>
      <h1 class="title">Assets visuais para provar o produto.</h1>
      <p class="lead">Controle de screenshots, video curto e evidencias que vamos usar na submissao ao GitHub Developer Program, landing, pitch e demonstracoes para outros devs.</p>
    </section>

    <section class="metrics">
      <div class="metric"><div class="kicker">Checklist</div><div class="value">{{ $report['percent'] }}%</div><div class="muted">{{ $report['done'] }} de {{ $report['total'] }} aprovados.</div></div>
      <div class="metric"><div class="kicker">Evidencias</div><div class="value">{{ $report['evidence_percent'] }}%</div><div class="muted">{{ $report['with_evidence'] }} item(ns) com link/caminho preenchido.</div></div>
      <div class="metric"><div class="kicker">Total</div><div class="value">{{ $report['total'] }}</div><div class="muted">Assets esperados para submissao.</div></div>
      <div class="metric"><div class="kicker">Status</div><div class="value">{{ $report['ready'] ? 'OK' : '!' }}</div><div class="muted">{{ $report['ready'] ? 'Pacote pronto.' : 'Ainda faltam evidencias.' }}</div></div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Checklist de assets</div>
        @forelse ($report['items'] as $item)
          <article class="asset {{ $item->status }}">
            <h3>{{ $item->title }}</h3>
            <div class="row">
              <span class="pill {{ $item->status === 'passed' ? 'ok' : ($item->status === 'blocked' ? 'warn' : '') }}">{{ $item->status }}</span>
              <span class="pill">{{ $item->priority }}</span>
              <span class="pill">#{{ $item->position }}</span>
              @if (filled($item->evidence))<span class="pill ok">evidencia</span>@else<span class="pill">sem evidencia</span>@endif
            </div>
            <div class="muted"><strong>Captura:</strong> {{ $item->instructions }}</div>
            <div class="muted" style="margin-top:8px"><strong>Resultado esperado:</strong> {{ $item->expected_result }}</div>
            @if (filled($item->evidence))
              <pre class="cmd" style="margin-top:10px">{{ $item->evidence }}</pre>
            @endif
          </article>
        @empty
          <div class="empty">Nenhum asset cadastrado. Rode <code>php artisan devlog:seed-submission-assets</code> para criar o checklist inicial.</div>
        @endforelse
      </div>

      <aside class="card">
        <div class="kicker">Como usar</div>
        <p class="muted">1. Rode o seed para criar os itens. 2. Capture screenshots ou videos. 3. Abra QA de lancamento e cole o caminho/link em Evidencia. 4. Marque como aprovado quando o asset estiver utilizavel.</p>
        <pre class="cmd">php artisan devlog:seed-submission-assets</pre>
        <div class="kicker" style="margin-top:18px">Onde editar</div>
        <pre class="cmd">/admin/launch-tests</pre>
        <div class="kicker" style="margin-top:18px">Padrao de evidencia</div>
        <pre class="cmd">storage/app/screenshots/landing.png
https://...
Drive/Notion/Figma link</pre>
      </aside>
    </section>
  </div>
</x-filament-panels::page>