@php
  $icons = [
    'book' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M4 4v15.5"></path><path d="M20 22V6a2 2 0 0 0-2-2H6.5A2.5 2.5 0 0 0 4 6.5v13"></path></svg>',
    'dashboard' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 13h6V4H4z"></path><path d="M14 20h6V4h-6z"></path><path d="M4 20h6v-3H4z"></path></svg>',
    'billing' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h18v10H3z"></path><path d="M3 10h18"></path><path d="M7 15h4"></path></svg>',
    'support' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12a7 7 0 0 1 14 0v5a2 2 0 0 1-2 2h-2"></path><path d="M9 19h6"></path><path d="M5 12H3v4h2"></path><path d="M19 12h2v4h-2"></path></svg>',
    'github' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 0 0-3 19c.5.1.7-.2.7-.5v-2c-2.8.6-3.4-1.2-3.4-1.2-.5-1.1-1.1-1.4-1.1-1.4-.9-.6.1-.6.1-.6 1 0 1.6 1 1.6 1 .9 1.5 2.3 1.1 2.9.8.1-.7.4-1.1.7-1.4-2.2-.2-4.5-1.1-4.5-4.9 0-1.1.4-2 1-2.7-.1-.3-.4-1.3.1-2.7 0 0 .8-.3 2.8 1a9.7 9.7 0 0 1 5 0c1.9-1.3 2.8-1 2.8-1 .5 1.4.2 2.4.1 2.7.6.7 1 1.6 1 2.7 0 3.8-2.3 4.6-4.5 4.9.4.3.7.9.7 1.8v2.7c0 .3.2.6.7.5A10 10 0 0 0 12 2Z"></path></svg>',
    'market' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16l-1 13H5L4 7Z"></path><path d="M8 7a4 4 0 0 1 8 0"></path><path d="M9 12h6"></path></svg>',
    'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>',
    'link' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.1 0l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1"></path><path d="M14 11a5 5 0 0 0-7.1 0l-2 2A5 5 0 0 0 12 20.1l1.1-1.1"></path></svg>',
  ];

  $cards = [
    ['icon' => 'dashboard', 'kicker' => 'Rotina diaria', 'title' => 'Saude operacional', 'body' => 'Abra a Visao geral, confira eventos de cobranca com atencao, tickets abertos, webhooks recebidos e progresso do roadmap.', 'href' => url('/admin'), 'cta' => 'Abrir dashboard admin'],
    ['icon' => 'billing', 'kicker' => 'Financeiro', 'title' => 'Assinaturas e Mercado Pago', 'body' => 'Use Assinaturas para ver status por workspace. Use Eventos de cobranca para auditar webhooks, retries, pagamentos pendentes e referencias.', 'href' => url('/admin/workspace-subscriptions'), 'cta' => 'Ver assinaturas'],
    ['icon' => 'support', 'kicker' => 'Suporte', 'title' => 'Triagem de chamados', 'body' => 'Classifique por prioridade, verifique workspace, busque delivery ID nos eventos de webhook e compare com notificacoes de billing quando envolver pagamento.', 'href' => url('/admin/support-tickets'), 'cta' => 'Abrir suporte'],
    ['icon' => 'github', 'kicker' => 'GitHub', 'title' => 'Prontidao de integracao', 'body' => 'Confirme dominio HTTPS, GitHub App real, webhook secret, politica de privacidade, termos, demo ponta a ponta e narrativa publica.', 'href' => url('/admin/github-readiness'), 'cta' => 'Ver readiness'],
    ['icon' => 'market', 'kicker' => 'Marketplace', 'title' => 'Listagem do GitHub App', 'body' => 'Use o rascunho de Marketplace para revisar descricao, links, evidencias, screenshots e pendencias antes de publicar o app.', 'href' => url('/admin/github-marketplace'), 'cta' => 'Ver rascunho'],
  ];
@endphp

<x-filament-panels::page>
  <style>
    .devlog-docs{--panel:#101720;--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;color:var(--ink)}
    .docs-hero,.docs-card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.86);box-shadow:0 22px 60px rgba(0,0,0,.18)}.docs-hero{padding:22px;margin-bottom:16px}.docs-card{padding:18px}
    .hero-head{display:flex;gap:16px;align-items:flex-start}.kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}.title{font-size:clamp(32px,4.4vw,58px);line-height:.96;letter-spacing:-.06em;font-weight:950;margin:0;color:var(--ink)}.lead{color:var(--muted);font-size:17px;line-height:1.65;margin:14px 0 0;max-width:900px}
    .icon{width:46px;height:46px;border-radius:16px;display:grid;place-items:center;border:1px solid rgba(80,184,255,.34);background:rgba(80,184,255,.1);color:#b7e4ff;flex:0 0 auto}.mini-icon{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);background:#071018;color:var(--blue);flex:0 0 auto}.mini-icon.ok{background:var(--green);border-color:var(--green);color:#071018}.icon svg,.mini-icon svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}.mini-icon.github svg{fill:currentColor;stroke:none}
    .docs-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:14px}.docs-card h3{font-size:19px;font-weight:950;margin:0 0 8px;color:var(--ink)}.docs-card p{color:var(--muted);line-height:1.65;margin:0}.docs-card a{color:var(--blue);font-weight:850;text-decoration:none;display:inline-flex;align-items:center;gap:8px;margin-top:12px}.card-head{display:flex;gap:12px;align-items:flex-start}.checklist{display:grid;gap:10px;margin-top:12px}.check{display:flex;gap:10px;align-items:flex-start;border:1px solid var(--line);border-radius:12px;background:#0b1118;padding:12px;color:var(--muted)}.check strong{color:var(--ink)}@media(max-width:800px){.docs-grid{grid-template-columns:1fr}.hero-head{display:block}.hero-head .icon{margin-bottom:14px}}
  </style>

  <div class="devlog-docs">
    <section class="docs-hero">
      <div class="hero-head">
        <div class="icon">{!! $icons['book'] !!}</div>
        <div>
          <div class="kicker">Admin / Documentacao</div>
          <h1 class="title">Manual operacional do DevLog AI.</h1>
          <p class="lead">Guia para operar o SaaS: acompanhar roadmap, revisar contas, gerir assinaturas, auditar billing, responder suporte e preparar divulgacao, demo e Marketplace do GitHub App.</p>
        </div>
      </div>
    </section>

    <section class="docs-grid">
      @foreach ($cards as $card)
        <article class="docs-card">
          <div class="card-head">
            <div class="mini-icon {{ $card['icon'] === 'github' ? 'github' : '' }}">{!! $icons[$card['icon']] !!}</div>
            <div>
              <div class="kicker">{{ $card['kicker'] }}</div>
              <h3>{{ $card['title'] }}</h3>
              <p>{{ $card['body'] }}</p>
              <a href="{{ $card['href'] }}"><span class="mini-icon">{!! $icons['link'] !!}</span>{{ $card['cta'] }}</a>
            </div>
          </div>
        </article>
      @endforeach

      <article class="docs-card">
        <div class="card-head">
          <div class="mini-icon">{!! $icons['check'] !!}</div>
          <div>
            <div class="kicker">Playbook</div>
            <h3>Webhook nao chegou</h3>
            <div class="checklist">
              <div class="check"><div class="mini-icon ok">{!! $icons['check'] !!}</div><div><strong>1. Verifique o GitHub</strong><br>Delivery retornou 200? O endpoint esta correto?</div></div>
              <div class="check"><div class="mini-icon ok">{!! $icons['check'] !!}</div><div><strong>2. Verifique assinatura</strong><br>Secret do workspace esta igual ao do repositorio?</div></div>
              <div class="check"><div class="mini-icon ok">{!! $icons['check'] !!}</div><div><strong>3. Verifique limite</strong><br>Plano atingiu limite mensal?</div></div>
            </div>
          </div>
        </div>
      </article>

      <article class="docs-card">
        <div class="card-head">
          <div class="mini-icon">{!! $icons['billing'] !!}</div>
          <div>
            <div class="kicker">Playbook</div>
            <h3>Pagamento nao ativou</h3>
            <div class="checklist">
              <div class="check"><div class="mini-icon ok">{!! $icons['check'] !!}</div><div><strong>1. Eventos de cobranca</strong><br>Procure por pending_lookup ou unmatched.</div></div>
              <div class="check"><div class="mini-icon ok">{!! $icons['check'] !!}</div><div><strong>2. Referencia</strong><br>Confirme external_reference workspace/plano.</div></div>
              <div class="check"><div class="mini-icon ok">{!! $icons['check'] !!}</div><div><strong>3. Acao manual</strong><br>Se necessario, ajuste assinatura e registre no suporte.</div></div>
            </div>
          </div>
        </div>
      </article>
    </section>
  </div>
</x-filament-panels::page>
