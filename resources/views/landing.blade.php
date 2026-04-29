<x-layout title="GitHub DevLog AI - Inbox privado para webhooks do GitHub">
  <main class="hero">
    <span class="eyebrow">Feito para devs que precisam confiar nos webhooks do GitHub</span>
    <h1>Transforme webhooks do GitHub em um histórico privado, legível e auditável.</h1>
    <p class="lead">
      O DevLog AI recebe eventos do GitHub, valida o segredo configurado no repositório e mostra tudo em um painel por workspace. Sem misturar payloads de outros devs, sem procurar logs no terminal, sem adivinhar se o webhook chegou.
    </p>
    <div class="d-flex gap-2 flex-wrap mt-4">
      <a class="btnx primary" href="{{ route('register') }}">Criar meu workspace</a>
      <a class="btnx" href="{{ route('login') }}">Abrir painel</a>
      <a class="btnx" href="#uso">Ver passo a passo</a>
    </div>

    <div class="hero-grid">
      <section class="terminal" aria-label="Exemplo técnico">
        <div class="bar"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
        <pre>POST /webhooks/github/7a2f...
X-GitHub-Event: push
X-Hub-Signature-256: sha256=...

{
  "repository": { "full_name": "acme/api" },
  "pusher": { "name": "ana" },
  "ref": "refs/heads/main"
}

✓ assinatura validada
✓ evento salvo no workspace correto
✓ payload disponível no painel</pre>
      </section>
      <aside class="panel" aria-label="Eventos no painel">
        <div class="signal"><strong>push <span>agora</span></strong><span>acme/api · validado por X-Hub-Signature-256</span></div>
        <div class="signal"><strong>pull_request <span>2 min</span></strong><span>acme/web · payload isolado no workspace</span></div>
        <div class="signal"><strong>workflow_run <span>8 min</span></strong><span>CI finalizado · pronto para análise</span></div>
        <div class="signal"><strong>issues <span>14 min</span></strong><span>nova issue recebida com delivery id</span></div>
      </aside>
    </div>
  </main>

  <section class="band" id="produto">
    <div class="kicker">A proposta</div>
    <h2>Um RequestBin privado, pensado para GitHub e para times de produto.</h2>
    <p class="lead">
      Quando um webhook falha, o problema raramente é só código. É contexto: qual evento chegou, qual repositório disparou, qual payload veio, se a assinatura bateu e quem consegue ver aquilo. O DevLog AI organiza esse fluxo em um workspace seguro para cada dev ou time.
    </p>
    <div class="row g-3 mt-3">
      <div class="col-md-4"><div class="cardx"><h3>Para devs</h3><p>Teste integrações GitHub sem depender de log local, print de terminal ou payload perdido.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Para times</h3><p>Compartilhe um workspace de debug com histórico comum e eventos separados por origem.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Para demos</h3><p>Mostre o fluxo de ponta a ponta: GitHub envia, o DevLog valida e o painel explica.</p></div></div>
    </div>
  </section>

  <section class="band steps" id="uso">
    <div class="kicker">Como o dev usa</div>
    <h2>Do cadastro ao primeiro webhook em minutos.</h2>
    <div class="row g-3">
      <div class="col-lg-4"><div class="cardx step"><h3>Crie uma conta</h3><p>O cadastro gera um workspace privado com endpoint e segredo próprios. Cada usuário enxerga somente os eventos do seu workspace.</p></div></div>
      <div class="col-lg-4"><div class="cardx step"><h3>Configure no GitHub</h3><p>No repositório, adicione o Payload URL do painel, escolha <code>application/json</code> e cole o Secret do workspace.</p></div></div>
      <div class="col-lg-4"><div class="cardx step"><h3>Acompanhe no painel</h3><p>Ao enviar <code>push</code>, <code>pull_request</code>, <code>issues</code> ou <code>workflow_run</code>, o evento aparece com payload, horário e validação.</p></div></div>
    </div>
  </section>

  <section class="band" id="seguranca">
    <div class="kicker">Segurança e isolamento</div>
    <h2>Webhook sem conta vira vazamento. Aqui o padrão é privado.</h2>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>Workspace por conta</h3><p>Eventos são filtrados pelo workspace autenticado. Um dev não vê webhooks de outro.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Assinatura do GitHub</h3><p>O endpoint aceita <code>X-Hub-Signature-256</code> usando o Secret configurado no repositório.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Rotação de segredo</h3><p>Se o segredo vazar, o painel permite gerar outro e atualizar a integração.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Comece agora</div>
    <h2>Crie um workspace e conecte seu primeiro repositório GitHub.</h2>
    <p class="lead">A ferramenta já entrega o núcleo do produto: cadastro, login, workspace, segredo, endpoint, painel privado e captura de eventos.</p>
    <div class="d-flex gap-2 flex-wrap mt-3">
      <a class="btnx primary" href="{{ route('register') }}">Criar workspace</a>
      <a class="btnx" href="{{ route('login') }}">Entrar no painel</a>
    </div>
  </section>

  <footer class="footer">
    <div class="d-flex justify-content-between gap-3 flex-wrap">
      <span>GitHub DevLog AI · produto para validar webhooks do GitHub com menos ruído e mais confiança.</span>
      <span><a href="{{ route('health') }}">Status</a> · <a href="{{ route('login') }}">Login</a> · <a href="{{ route('register') }}">Cadastro</a></span>
    </div>
  </footer>
</x-layout>