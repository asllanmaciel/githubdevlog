<x-layout title="GitHub DevLog AI - Inbox privado para webhooks do GitHub">
  @php
    $creatorName = config('devlog.creator_name');
    $creatorRole = config('devlog.creator_role');
    $creatorUrl = config('devlog.creator_url');
    $creatorAvatarUrl = config('devlog.creator_avatar_url');
  @endphp

  <main class="hero">
    <span class="eyebrow">Feito para devs que precisam confiar nos webhooks do GitHub</span>
    <h1>Transforme webhooks do GitHub em um histÃ³rico privado, legÃ­vel e auditÃ¡vel.</h1>
    <p class="lead">
      O DevLog AI recebe eventos do GitHub, valida o segredo configurado no repositÃ³rio e mostra tudo em um painel por workspace. Sem misturar payloads de outros devs, sem procurar logs no terminal, sem adivinhar se o webhook chegou.
    </p>
    <div class="d-flex gap-2 flex-wrap mt-4">
      <a class="btnx primary" href="{{ route('register') }}">Criar meu workspace</a>
      <a class="btnx" href="{{ route('login') }}">Abrir painel</a>
      <a class="btnx" href="#uso">Ver passo a passo</a>
    </div>

    <div class="hero-grid">
      <section class="terminal" aria-label="Exemplo tÃ©cnico">
        <div class="bar"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
        <pre>POST /webhooks/github/7a2f...
X-GitHub-Event: push
X-Hub-Signature-256: sha256=...

{
  "repository": { "full_name": "acme/api" },
  "pusher": { "name": "ana" },
  "ref": "refs/heads/main"
}

âœ“ assinatura validada
âœ“ evento salvo no workspace correto
âœ“ payload disponÃ­vel no painel</pre>
      </section>
      <aside class="panel" aria-label="Eventos no painel">
        <div class="signal"><strong>push <span>agora</span></strong><span>acme/api Â· validado por X-Hub-Signature-256</span></div>
        <div class="signal"><strong>pull_request <span>2 min</span></strong><span>acme/web Â· payload isolado no workspace</span></div>
        <div class="signal"><strong>workflow_run <span>8 min</span></strong><span>CI finalizado Â· pronto para anÃ¡lise</span></div>
        <div class="signal"><strong>issues <span>14 min</span></strong><span>nova issue recebida com delivery id</span></div>
      </aside>
    </div>
  </main>

  <section class="band" id="produto">
    <div class="kicker">A proposta</div>
    <h2>Um RequestBin privado, pensado para GitHub e para times de produto.</h2>
    <p class="lead">
      Quando um webhook falha, o problema raramente Ã© sÃ³ cÃ³digo. Ã‰ contexto: qual evento chegou, qual repositÃ³rio disparou, qual payload veio, se a assinatura bateu e quem consegue ver aquilo. O DevLog AI organiza esse fluxo em um workspace seguro para cada dev ou time.
    </p>
    <div class="row g-3 mt-3">
      <div class="col-md-4"><div class="cardx"><h3>Para devs</h3><p>Teste integraÃ§Ãµes GitHub sem depender de log local, print de terminal ou payload perdido.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Para times</h3><p>Compartilhe um workspace de debug com histÃ³rico comum e eventos separados por origem.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Para demos</h3><p>Mostre o fluxo de ponta a ponta: GitHub envia, o DevLog valida e o painel explica.</p></div></div>
    </div>
  </section>

  <section class="band" id="criador">
    <div class="kicker">Criador</div>
    <div class="creator-profile">
      <div>
        <img class="creator-photo" src="{{ $creatorAvatarUrl }}" alt="{{ $creatorName }}">
      </div>
      <div>
        <h2>Construido por {{ $creatorName }} para resolver uma dor real de integracao.</h2>
        <p class="lead">
          O DevLog AI nasceu de uma necessidade pratica: entender, validar e demonstrar webhooks do GitHub sem depender de logs soltos, payloads perdidos ou ferramentas genericas demais. A proposta e transformar eventos tecnicos em historico privado, legivel e util para devs.
        </p>
        <div class="creator-badges">
          <span class="pill">GitHub-first</span>
          <span class="pill">SaaS para devs</span>
          <span class="pill">Webhooks privados</span>
          <span class="pill">Produto criado por {{ $creatorName }}</span>
        </div>
        <p class="muted">{{ $creatorRole }}</p>
        @if ($creatorUrl)
          <div class="mt-3"><a class="btnx" href="{{ $creatorUrl }}" target="_blank" rel="noopener">Ver GitHub do criador</a></div>
        @endif
      </div>
    </div>
  </section>
  <section class="band steps" id="uso">
    <div class="kicker">Como o dev usa</div>
    <h2>Do cadastro ao primeiro webhook em minutos.</h2>
    <div class="row g-3">
      <div class="col-lg-4"><div class="cardx step"><h3>Crie uma conta</h3><p>O cadastro gera um workspace privado com endpoint e segredo prÃ³prios. Cada usuÃ¡rio enxerga somente os eventos do seu workspace.</p></div></div>
      <div class="col-lg-4"><div class="cardx step"><h3>Configure no GitHub</h3><p>No repositÃ³rio, adicione o Payload URL do painel, escolha <code>application/json</code> e cole o Secret do workspace.</p></div></div>
      <div class="col-lg-4"><div class="cardx step"><h3>Acompanhe no painel</h3><p>Ao enviar <code>push</code>, <code>pull_request</code>, <code>issues</code> ou <code>workflow_run</code>, o evento aparece com payload, horÃ¡rio e validaÃ§Ã£o.</p></div></div>
    </div>
  </section>

  <section class="band" id="seguranca">
    <div class="kicker">SeguranÃ§a e isolamento</div>
    <h2>Webhook sem conta vira vazamento. Aqui o padrÃ£o Ã© privado.</h2>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>Workspace por conta</h3><p>Eventos sÃ£o filtrados pelo workspace autenticado. Um dev nÃ£o vÃª webhooks de outro.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Assinatura do GitHub</h3><p>O endpoint aceita <code>X-Hub-Signature-256</code> usando o Secret configurado no repositÃ³rio.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>RotaÃ§Ã£o de segredo</h3><p>Se o segredo vazar, o painel permite gerar outro e atualizar a integraÃ§Ã£o.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Comece agora</div>
    <h2>Crie um workspace e conecte seu primeiro repositÃ³rio GitHub.</h2>
    <p class="lead">A ferramenta jÃ¡ entrega o nÃºcleo do produto: cadastro, login, workspace, segredo, endpoint, painel privado e captura de eventos.</p>
    <div class="d-flex gap-2 flex-wrap mt-3">
      <a class="btnx primary" href="{{ route('register') }}">Criar workspace</a>
      <a class="btnx" href="{{ route('login') }}">Entrar no painel</a>
    </div>
  </section>

  <footer class="footer">
    <div class="d-flex justify-content-between gap-3 flex-wrap">
      <span>GitHub DevLog AI Â· criado por {{ $creatorName }} para validar webhooks do GitHub com menos ruÃ­do e mais confianÃ§a.</span>
      <span><a href="{{ route('status') }}">Status</a> Â· <a href="{{ route('github') }}">GitHub</a> Â· <a href="{{ route('login') }}">Login</a> Â· <a href="{{ route('register') }}">Cadastro</a></span>
    </div>
  </footer>
</x-layout>

