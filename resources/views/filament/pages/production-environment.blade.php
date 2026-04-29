@php
  $report = App\Support\ProductionEnvironment::report();
  $pending = $report['required_pending'];
@endphp

<x-filament-panels::page>
  <style>
    .envcheck{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--danger:#ff6b6b;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 86% 20%,rgba(105,227,154,.15),transparent 30%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:850px}.grid{display:grid;grid-template-columns:360px 1fr;gap:16px}.score{font-size:58px;font-weight:950;letter-spacing:-.06em}.muted{color:var(--muted);font-size:13px}.status{display:inline-flex;border:1px solid var(--line);border-radius:999px;padding:8px 12px;font-weight:900;margin-top:14px}.ready{color:var(--green);border-color:rgba(105,227,154,.45);background:rgba(105,227,154,.08)}.blocked{color:var(--danger);border-color:rgba(255,107,107,.45);background:rgba(255,107,107,.08)}.group{margin-bottom:18px}.group h3{font-weight:950;font-size:18px;margin:0 0 10px}.check{display:grid;grid-template-columns:32px 1fr auto;gap:10px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px;margin-bottom:10px}.check.done{border-color:rgba(105,227,154,.35);background:rgba(105,227,154,.06)}.badge{width:32px;height:32px;border-radius:10px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950;font-size:11px}.check.done .badge{background:var(--green);border-color:var(--green);color:#071018}.pill{border:1px solid var(--line);border-radius:999px;padding:4px 8px;color:var(--muted);font-size:12px}.cmd{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;overflow:auto}@media(max-width:1000px){.grid{grid-template-columns:1fr}}
  </style>

  <div class="envcheck">
    <section class="hero">
      <div class="kicker">Production readiness</div>
      <h1 class="title">Checklist de ambiente para publicar sem vazar segredo nem quebrar webhook.</h1>
      <p class="lead">Esta tela confere as configuracoes que precisam estar certas no servidor antes de ativar Mercado Pago, GitHub App e usuarios reais. Ela nao mostra valores sensiveis, apenas se cada chave esta pronta.</p>
    </section>

    <section class="grid">
      <aside class="card">
        <div class="kicker">Resumo</div>
        <div class="score">{{ $report['percent'] }}%</div>
        <div class="muted">{{ $report['done'] }} de {{ $report['total'] }} checks concluidos.</div>
        <span class="status {{ $report['ready'] ? 'ready' : 'blocked' }}">{{ $report['ready'] ? 'Ambiente pronto' : 'Pendencias obrigatorias' }}</span>

        <div class="kicker" style="margin-top:22px">Arquivos de apoio</div>
        <pre class="cmd">.env.production.example
docs/production-env-checklist.md</pre>
        <div class="muted">Use estes arquivos para preencher o servidor sem copiar credenciais locais.</div>
      </aside>

      <div class="card">
        @foreach ($report['groups'] as $group => $items)
          <div class="group">
            <h3>{{ $group }}</h3>
            @foreach ($items as $item)
              <div class="check {{ $item['done'] ? 'done' : '' }}">
                <div class="badge">{{ $item['done'] ? 'ok' : '!' }}</div>
                <div><strong>{{ $item['title'] }}</strong><div class="muted">{{ $item['detail'] }}</div></div>
                <span class="pill">{{ $item['required'] ? 'Obrigatorio' : 'Recomendado' }}</span>
              </div>
            @endforeach
          </div>
        @endforeach
      </div>
    </section>
  </div>
</x-filament-panels::page>