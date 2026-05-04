@php
  $links = [
    ['label' => 'Website', 'value' => 'https://ghdevlog.com'],
    ['label' => 'Pagina da integracao', 'value' => 'https://ghdevlog.com/github'],
    ['label' => 'GitHub App publico', 'value' => 'https://github.com/apps/gh-devlog'],
    ['label' => 'Suporte', 'value' => 'contato@asllanmaciel.com.br'],
    ['label' => 'Contato', 'value' => 'https://ghdevlog.com/contact'],
    ['label' => 'Privacidade', 'value' => 'https://ghdevlog.com/privacy'],
    ['label' => 'Termos', 'value' => 'https://ghdevlog.com/terms'],
    ['label' => 'Status', 'value' => 'https://ghdevlog.com/status'],
  ];

  $screenshots = [
    'Pagina publica /github explicando a integracao.',
    'Pagina publica do GitHub App em github.com/apps/gh-devlog.',
    'GitHub App conectado no workspace.',
    'Inbox com 30 eventos e 30 assinaturas validas.',
    'Detalhe do workflow_run#35 com delivery ID e assinatura valida.',
    'Hardening com 35 aceitos, 0 rejeitados e 100% validos.',
    'Paginas de privacidade e termos publicadas.',
  ];

  $checks = [
    ['title' => 'Valor alem de autenticacao', 'detail' => 'Recebe, valida, sanitiza e organiza webhooks reais do GitHub.', 'done' => true],
    ['title' => 'GitHub App publico', 'detail' => 'https://github.com/apps/gh-devlog', 'done' => true],
    ['title' => 'Site e pagina da integracao', 'detail' => 'https://ghdevlog.com/github', 'done' => true],
    ['title' => 'Suporte publicado', 'detail' => 'Email e pagina de contato disponiveis.', 'done' => true],
    ['title' => 'Privacidade e termos', 'detail' => 'Paginas publicas publicadas.', 'done' => true],
    ['title' => 'Plano de preco', 'detail' => 'Comecar gratuito/Starter antes de listagem paga.', 'done' => false],
    ['title' => 'Screenshots sem secrets', 'detail' => 'Revisar antes de anexar em qualquer fluxo publico.', 'done' => false],
    ['title' => 'Marketplace pago', 'detail' => 'Validar publisher, billing e webhooks comerciais antes de cobrar via Marketplace.', 'done' => false],
  ];

  $icons = [
    'market' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16l-1 13H5L4 7Z"></path><path d="M8 7a4 4 0 0 1 8 0"></path><path d="M9 12h6"></path></svg>',
    'copy' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 8h10v12H8z"></path><path d="M6 16H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>',
    'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"></path></svg>',
    'warn' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 9v4"></path><path d="M12 17h.01"></path><path d="M10.3 3.9 2.6 17.5A2 2 0 0 0 4.3 20h15.4a2 2 0 0 0 1.7-2.5L13.7 3.9a2 2 0 0 0-3.4 0Z"></path></svg>',
    'link' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.1 0l2-2a5 5 0 0 0-7.1-7.1l-1.1 1.1"></path><path d="M14 11a5 5 0 0 0-7.1 0l-2 2A5 5 0 0 0 12 20.1l1.1-1.1"></path></svg>',
    'image' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4z"></path><path d="m4 15 4-4 4 4 2-2 6 6"></path><path d="M15 9h.01"></path></svg>',
    'spark' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2 3 14h8l-1 8 11-13h-8l0-7Z"></path></svg>',
    'card' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7h18v10H3z"></path><path d="M3 10h18"></path><path d="M7 15h4"></path></svg>',
  ];
@endphp

<x-filament-panels::page>
  <style>
    .market{--ink:#f3f7fb;--muted:#9aa9b5;--line:#273544;--blue:#50b8ff;--green:#69e39a;--yellow:#ffd166;color:var(--ink)}
    .hero,.card{border:1px solid var(--line);border-radius:18px;background:rgba(16,23,32,.88);padding:20px;box-shadow:0 22px 60px rgba(0,0,0,.18)}
    .hero{margin-bottom:16px;background:radial-gradient(circle at 80% 10%,rgba(105,227,154,.18),transparent 28%),rgba(16,23,32,.88)}
    .kicker{color:var(--blue);font-size:12px;text-transform:uppercase;letter-spacing:.14em;font-weight:950;margin-bottom:10px}
    .title{font-size:clamp(34px,5vw,62px);line-height:.95;letter-spacing:-.055em;font-weight:950;margin:0;color:var(--ink)}
    .lead{color:var(--muted);font-size:16px;line-height:1.65;margin:14px 0 0;max-width:900px}
    .grid{display:grid;grid-template-columns:1fr 420px;gap:16px;margin-bottom:16px}.wide{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;margin-bottom:16px}
    .icon{width:42px;height:42px;border-radius:14px;display:grid;place-items:center;border:1px solid rgba(80,184,255,.34);background:rgba(80,184,255,.1);color:#b7e4ff;flex:0 0 auto}.icon svg,.mini-icon svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round}.mini-icon{width:34px;height:34px;border-radius:12px;display:grid;place-items:center;border:1px solid var(--line);background:#071018;color:var(--blue);flex:0 0 auto}.mini-icon.ok{background:var(--green);border-color:var(--green);color:#071018}.mini-icon.warn{color:var(--yellow);border-color:rgba(255,209,102,.45);background:rgba(255,209,102,.08)}
    .hero-head{display:flex;gap:16px;align-items:flex-start}.stack{display:grid;gap:12px}.block{display:flex;gap:12px;align-items:flex-start;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:14px}.block strong{display:block;margin-bottom:8px}.block p{color:var(--muted);line-height:1.65;margin:0}
    .copy{border:1px solid var(--line);border-radius:14px;background:#050a10;color:#b7e4ff;padding:14px;white-space:pre-wrap;line-height:1.55;overflow:auto}
    .check{display:grid;grid-template-columns:auto 1fr auto;gap:12px;align-items:center;border:1px solid var(--line);border-radius:14px;background:#0b1118;padding:12px}
    .check.done{border-color:rgba(105,227,154,.38);background:rgba(105,227,154,.07)}
    .detail{color:var(--muted);font-size:13px;line-height:1.55}.pill{border:1px solid var(--line);border-radius:999px;padding:5px 9px;color:var(--muted);font-size:12px}.done .pill{color:var(--green);border-color:rgba(105,227,154,.4)}
    .action-link{display:inline-flex;align-items:center;gap:8px;border:1px solid var(--line);border-radius:12px;padding:10px 12px;text-decoration:none;color:var(--ink);font-weight:850;background:#0b1118}.action-link.primary{background:var(--blue);border-color:var(--blue);color:#071018}
    @media(max-width:1100px){.grid,.wide{grid-template-columns:1fr}}@media(max-width:720px){.hero-head{display:block}.hero-head .icon{margin-bottom:14px}.check{grid-template-columns:auto 1fr}.check .pill{grid-column:1/-1}}
  </style>

  <div class="market">
    <section class="hero">
      <div class="hero-head">
        <div class="icon">{!! $icons['market'] !!}</div>
        <div>
          <div class="kicker">GitHub Marketplace</div>
          <h1 class="title">Rascunho da futura listagem publica.</h1>
          <p class="lead">O Developer Program ja esta concluido. Esta tela organiza a proxima trilha opcional: publicar o GitHub App no Marketplace com descricao, links, evidencias e requisitos de prontidao.</p>
        </div>
      </div>
    </section>

    <section class="grid">
      <div class="card">
        <div class="kicker">Texto principal</div>
        <div class="stack">
          <div class="block"><div class="mini-icon">{!! $icons['market'] !!}</div><div><strong>Nome</strong><p>GH DevLog ou GitHub DevLog AI.</p></div></div>
          <div class="block"><div class="mini-icon">{!! $icons['spark'] !!}</div><div><strong>Categoria</strong><p>Developer tools. Secundarias possiveis: Monitoring, Collaboration.</p></div></div>
          <div class="block"><div class="mini-icon">{!! $icons['copy'] !!}</div><div><strong>Short description</strong><pre class="copy">Private webhook inbox for GitHub Apps and repository events, with signature validation, sanitized payload history and workspace-based debugging.</pre></div></div>
          <div class="block"><div class="mini-icon">{!! $icons['copy'] !!}</div><div><strong>Full description</strong><pre class="copy">GitHub DevLog AI helps developers and teams debug GitHub webhooks with confidence. It receives GitHub App and repository webhook events, validates X-Hub-Signature-256, stores sanitized payloads in isolated workspaces and turns raw deliveries into a readable inbox.

Teams can inspect event type, delivery ID, repository, branch, sender, workflow status, commits, changed files and sanitized payload context. The product also supports notes, tasks, AI-assisted analysis and admin hardening metrics for accepted and rejected deliveries.

It is built for developers creating GitHub Apps, SaaS teams integrating with GitHub, agencies demonstrating automations and engineering teams that need an audit trail for webhook deliveries.</pre></div></div>
        </div>
      </div>

      <aside class="card">
        <div class="kicker">Readiness</div>
        <div class="stack">
          @foreach ($checks as $check)
            <div class="check {{ $check['done'] ? 'done' : '' }}">
              <div class="mini-icon {{ $check['done'] ? 'ok' : 'warn' }}">{!! $check['done'] ? $icons['check'] : $icons['warn'] !!}</div>
              <div><strong>{{ $check['title'] }}</strong><div class="detail">{{ $check['detail'] }}</div></div>
              <span class="pill">{{ $check['done'] ? 'Pronto' : 'Pendente' }}</span>
            </div>
          @endforeach
        </div>
      </aside>
    </section>

    <section class="wide">
      <div class="card">
        <div class="kicker">Links de publicacao</div>
        <div class="stack">
          @foreach ($links as $link)
            <div class="block"><div class="mini-icon">{!! $icons['link'] !!}</div><div><strong>{{ $link['label'] }}</strong><p>{{ $link['value'] }}</p></div></div>
          @endforeach
        </div>
      </div>

      <div class="card">
        <div class="kicker">Screenshots</div>
        <div class="stack">
          @foreach ($screenshots as $shot)
            <div class="block"><div class="mini-icon">{!! $icons['image'] !!}</div><div><p>{{ $shot }}</p></div></div>
          @endforeach
        </div>
      </div>
    </section>

    <section class="wide">
      <div class="card">
        <div class="kicker">Valor alem de login</div>
        <div class="block"><div class="mini-icon">{!! $icons['spark'] !!}</div><div><p>O produto nao usa GitHub apenas para autenticar usuarios. Ele recebe e valida entregas reais, vincula instalacoes a workspaces, armazena payloads sanitizados, mostra delivery IDs e oferece uma visao operacional da saude dos webhooks.</p></div></div>
      </div>
      <div class="card">
        <div class="kicker">Plano inicial sugerido</div>
        <div class="block"><div class="mini-icon">{!! $icons['card'] !!}</div><div><strong>Starter / Free</strong><p>Para devs individuais testando fluxos de webhook e validando entregas de GitHub App. Limite mensal reduzido, retencao basica, um workspace, historico de assinaturas validas e suporte publico.</p></div></div>
      </div>
    </section>
  </div>
</x-filament-panels::page>
