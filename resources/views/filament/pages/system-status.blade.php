@php
  $report = \App\Support\SystemHealth::report();
@endphp

<x-filament-panels::page>
  <style>
    .status-page{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--red:#ff7b7b;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}
    .hero{display:grid;grid-template-columns:1fr auto;gap:18px;align-items:center;margin-bottom:16px}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}
    .title{font-size:clamp(34px,5vw,62px);line-height:.94;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0}
    .orb{width:132px;height:132px;border-radius:36px;display:grid;place-items:center;border:1px solid rgba(105,227,154,.32);background:radial-gradient(circle at 35% 25%,rgba(105,227,154,.28),rgba(80,184,255,.12) 44%,rgba(8,16,25,.94) 74%);font-weight:950;font-size:24px}.orb.fail{border-color:rgba(255,123,123,.45);background:rgba(255,123,123,.08)}
    .grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.check{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px}.check.ok{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}.check.fail{border-color:rgba(255,123,123,.45);background:rgba(255,123,123,.07)}
    .badge{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);font-weight:950;color:var(--muted)}.check.ok .badge{background:var(--green);border-color:var(--green);color:#071018}.check.fail .badge{background:var(--red);border-color:var(--red);color:#071018}.detail{color:var(--muted);font-size:13px}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}@media(max-width:900px){.hero,.grid{grid-template-columns:1fr}}
  </style>

  <div class="status-page">
    <section class="hero">
      <div>
        <div class="kicker">Observabilidade</div>
        <h1 class="title">Status operacional do DevLog AI.</h1>
        <p class="lead">Checks reutilizados pelo endpoint publico de health e pelo painel admin para acompanhar banco, storage, filas, billing, GitHub e ambiente.</p>
      </div>
      <div class="orb {{ $report['ok'] ? '' : 'fail' }}">{{ $report['ok'] ? 'OK' : 'ATENCAO' }}</div>
    </section>

    <section class="grid">
      @foreach ($report['checks'] as $name => $check)
        <article class="check {{ $check['ok'] ? 'ok' : 'fail' }}">
          <div class="badge">{{ $check['ok'] ? 'ok' : '!' }}</div>
          <div>
            <strong>{{ str_replace('_', ' ', $name) }} · {{ $check['label'] }}</strong>
            <div class="detail">{{ $check['detail'] }}</div>
          </div>
          <span class="pill">{{ $check['ok'] ? 'Saudavel' : 'Verificar' }}</span>
        </article>
      @endforeach
    </section>
  </div>
</x-filament-panels::page>
