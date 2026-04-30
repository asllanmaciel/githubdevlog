<x-layout title="GitHub DevLog AI - Inbox privado para webhooks do GitHub">
  @php
    $creatorName = config('devlog.creator_name');
    $creatorRole = config('devlog.creator_role');
    $creatorUrl = config('devlog.creator_url');
    $creatorAvatarUrl = config('devlog.creator_avatar_url');
  @endphp

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
    <h2>Um RequestBin privado, pensado para GitHub e para timês de produto.</h2>
    <p class="lead">
      Quando um webhook falha, o problema raramente é só código. É contexto: qual evento chegou, qual repositório disparou, qual payload veio, se a assinatura bateu e quem consegue ver aquilo. O DevLog AI organiza esse fluxo em um workspace seguro para cada dev ou time.
    </p>
    <div class="row g-3 mt-3">
      <div class="col-md-4"><div class="cardx"><h3>Para devs</h3><p>Teste integrações GitHub sem depender de log local, print de terminal ou payload perdido.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Para timês</h3><p>Compartilhe um workspace de debug com histórico comum e eventos separados por origem.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Para demos</h3><p>Mostre o fluxo de ponta a ponta: GitHub envia, o DevLog valida e o painel explica.</p></div></div>
    </div>
  </section>

  <section class="band" id="criador">
    <div class="kicker">Por trás do produto</div>
    <div class="creator-profile">
      <div>
        <img class="creator-photo" src="{{ $creatorAvatarUrl }}" alt="{{ $creatorName }}">
      </div>
      <div>
        <h2>Oi, eu sou {{ $creatorName }}. Criei o DevLog AI para transformar webhooks em clareza.</h2>
        <p class="lead">
          A ideia nasceu de uma dor bem prática: integrar GitHub, testar eventos, provar que um webhook chegou e explicar o que aconteceu sem depender de terminal aberto, prints ou logs perdidos. O DevLog AI organiza esse fluxo em um workspace privado, auditável e pronto para timês.
        </p>
        <div class="creator-badges">
          <span class="pill">Criado por dev</span>
          <span class="pill">GitHub-first</span>
          <span class="pill">SaaS em evolução</span>
          <span class="pill">Foco em webhooks reais</span>
        </div>
        <p class="muted">{{ $creatorRole }}</p>
        @if ($creatorUrl)
          <div class="mt-3"><a class="btnx" href="{{ $creatorUrl }}" target="_blank" rel="noopener">Ver meu GitHub</a></div>
        @endif
      </div>
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

  <section class="band" id="segurança">
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
    <div class="row g-4">
      <div class="col-lg-5">
        <strong>GitHub DevLog AI</strong>
        <p class="muted mt-2 mb-0">Criado por {{ $creatorName }} para validar webhooks do GitHub com menos ruído, mais segurança e histórico confiável.</p>
      </div>
      <div class="col-6 col-lg-2">
        <div class="kicker">Produto</div>
        <div class="d-grid gap-2">
          <a href="{{ route('pricing') }}">Planos</a>
          <a href="{{ route('docs.api') }}">API</a>
          <a href="{{ route('changelog') }}">Changelog</a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <div class="kicker">Confiança</div>
        <div class="d-grid gap-2">
          <a href="{{ route('status') }}">Status</a>
          <a href="{{ route('security') }}">Segurança</a>
          <a href="{{ route('contact') }}">Contato</a>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="kicker">Legal</div>
        <div class="d-grid gap-2">
          <a href="{{ route('privacy') }}">Privacidade</a>
          <a href="{{ route('terms') }}">Termos de uso</a>
          <a href="{{ route('github') }}">GitHub Developer Program</a>
        </div>
      </div>
    </div>
  </footer>
</x-layout>





