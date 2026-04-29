<x-filament-panels::page>
  <style>
    .devlog-docs {
      --bg:#090d12; --panel:#101720; --ink:#f3f7fb; --muted:#9aa9b5; --line:#273544; --blue:#50b8ff; --green:#69e39a;
      color:var(--ink);
    }
    .docs-hero{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.86);padding:22px;box-shadow:0 22px 60px rgba(0,0,0,.18);margin-bottom:16px}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}
    .title{font-size:clamp(32px,4.4vw,58px);line-height:.96;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}
    .lead{color:var(--muted);font-size:17px;line-height:1.65;margin:14px 0 0;max-width:820px}
    .docs-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.docs-card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.86);padding:18px;box-shadow:0 18px 50px rgba(0,0,0,.16)}
    .docs-card h3{font-size:19px;font-weight:950;margin:0 0 8px;color:var(--ink)}.docs-card p{color:var(--muted);line-height:1.65;margin:0}.docs-card a{color:var(--blue);font-weight:850;text-decoration:none;display:inline-flex;margin-top:12px}
    .checklist{display:grid;gap:10px;margin-top:12px}.check{border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:12px;color:var(--muted);display:flex;gap:10px;align-items:flex-start}.check strong{color:var(--ink)}.ok{color:var(--green);font-weight:950}.go{color:var(--blue);font-weight:950}
    @media(max-width:800px){.docs-grid{grid-template-columns:1fr}}
  </style>
  <div class="devlog-docs">
    <section class="docs-hero">
      <div class="kicker">Admin / Documentação</div>
      <h1 class="title">Manual operacional do DevLog AI.</h1>
      <p class="lead">Guia rápido para administrar o SaaS: acompanhar roadmap, revisar contas, entender suporte, preparar cobrança e manter a proposta pronta para devs e para o GitHub Developer Program.</p>
    </section>
    <section class="docs-grid">
      <article class="docs-card"><div class="kicker">Produto</div><h3>Roadmap vivo</h3><p>Use a visão visual para acompanhar percentuais, marcar entregas concluídas e manter o lançamento sob controle.</p><a href="{{ url('/admin/roadmap') }}">Abrir roadmap</a></article>
      <article class="docs-card"><div class="kicker">Operação</div><h3>Suporte e contas</h3><p>Tickets, usuários e workspaces são acompanhados no painel admin para triagem, diagnóstico e evolução da experiência.</p></article>
      <article class="docs-card"><div class="kicker">Comercial</div><h3>Planos por uso</h3><p>O modelo recomendado é limitar por eventos, retenção, workspaces e colaboração. Mercado Pago entra como checkout e recorrência.</p></article>
      <article class="docs-card"><div class="kicker">Checklist</div><h3>Antes de lançar</h3><div class="checklist"><div class="check"><span class="ok">✓</span><span><strong>Roadmap no admin</strong><br>Visível e acionável.</span></div><div class="check"><span class="go">→</span><span><strong>Cobrança</strong><br>Checkout e webhook Mercado Pago.</span></div><div class="check"><span class="go">→</span><span><strong>GitHub Program</strong><br>Preparar narrativa, segurança e demo.</span></div></div></article>
    </section>
  </div>
</x-filament-panels::page>

