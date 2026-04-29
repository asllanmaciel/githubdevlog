<x-filament-panels::page>
  <style>
    .devlog-docs{--panel:#101720;--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;color:var(--ink)}
    .docs-hero,.docs-card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.86);box-shadow:0 22px 60px rgba(0,0,0,.18)}.docs-hero{padding:22px;margin-bottom:16px}.docs-card{padding:18px}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(32px,4.4vw,58px);line-height:.96;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:17px;line-height:1.65;margin:14px 0 0;max-width:900px}
    .docs-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.docs-card h3{font-size:19px;font-weight:950;margin:0 0 8px;color:var(--ink)}.docs-card p{color:var(--muted);line-height:1.65;margin:0}.docs-card a{color:var(--blue);font-weight:850;text-decoration:none;display:inline-flex;margin-top:12px}.checklist{display:grid;gap:10px;margin-top:12px}.check{border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:12px;color:var(--muted)}.check strong{color:var(--ink)}@media(max-width:800px){.docs-grid{grid-template-columns:1fr}}
  </style>
  <div class="devlog-docs">
    <section class="docs-hero">
      <div class="kicker">Admin / Documentacao</div>
      <h1 class="title">Manual operacional do DevLog AI.</h1>
      <p class="lead">Guia para operar o SaaS: acompanhar roadmap, revisar contas, gerir assinaturas, auditar billing, responder suporte e preparar a submissao ao GitHub Developer Program.</p>
    </section>
    <section class="docs-grid">
      <article class="docs-card"><div class="kicker">Rotina diaria</div><h3>Saude operacional</h3><p>Abra a Visao geral, confira eventos de cobranca com atencao, tickets abertos, webhooks recebidos e progresso do roadmap.</p><a href="{{ url('/admin') }}">Abrir dashboard admin</a></article>
      <article class="docs-card"><div class="kicker">Financeiro</div><h3>Assinaturas e Mercado Pago</h3><p>Use Assinaturas para ver status por workspace. Use Eventos de cobranca para auditar webhooks, retries, pagamentos pendentes e referencias.</p><a href="{{ url('/admin/workspace-subscriptions') }}">Ver assinaturas</a></article>
      <article class="docs-card"><div class="kicker">Suporte</div><h3>Triagem de chamados</h3><p>Classifique por prioridade, verifique workspace, busque delivery id nos eventos de webhook e compare com notificacoes de billing quando envolver pagamento.</p><a href="{{ url('/admin/support-tickets') }}">Abrir suporte</a></article>
      <article class="docs-card"><div class="kicker">GitHub Program</div><h3>Prontidao de integracao</h3><p>Antes de submeter, confirme dominio HTTPS, GitHub App real, webhook secret, politica de privacidade, termos, demo ponta a ponta e narrativa publica.</p><a href="{{ url('/admin/github-readiness') }}">Ver readiness</a></article>
      <article class="docs-card"><div class="kicker">Playbook</div><h3>Webhook nao chegou</h3><div class="checklist"><div class="check"><strong>1. Verifique o GitHub</strong><br>Delivery retornou 200? O endpoint esta correto?</div><div class="check"><strong>2. Verifique assinatura</strong><br>Secret do workspace esta igual ao do repositorio?</div><div class="check"><strong>3. Verifique limite</strong><br>Plano atingiu limite mensal?</div></div></article>
      <article class="docs-card"><div class="kicker">Playbook</div><h3>Pagamento nao ativou</h3><div class="checklist"><div class="check"><strong>1. Eventos de cobranca</strong><br>Procure por pending_lookup ou unmatched.</div><div class="check"><strong>2. Referencia</strong><br>Confirme external_reference workspace/plano.</div><div class="check"><strong>3. Acao manual</strong><br>Se necessario, ajuste assinatura e registre no suporte.</div></div></article>
    </section>
  </div>
</x-filament-panels::page>