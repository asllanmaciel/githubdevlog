<x-layout title="Integração GitHub - GitHub DevLog AI">
  @php
    $creatorName = config('devlog.creator_name');
    $creatorRole = config('devlog.creator_role');
  @endphp

  <section class="hero">
    <span class="eyebrow">GitHub-first webhook intelligence</span>
    <h1>Conecte o GitHub e transforme webhooks em sinais acionáveis.</h1>
    <p class="lead">
      O DevLog AI recebe eventos do GitHub, valida assinatura, isola cada workspace e organiza payloads em uma timeline legível para debugging, auditoria, colaboração e análise AI. Projeto criado por {{ $creatorName }}, com foco em resolver uma dor real de devs que integram produtos ao GitHub.
    </p>
    <div class="d-flex gap-2 flex-wrap mt-4">
      <a class="btnx primary" href="{{ route('register') }}">Criar workspace</a>
      <a class="btnx" href="https://github.com/apps/gh-devlog" target="_blank" rel="noopener">Ver GitHub App</a>
      <a class="btnx" href="{{ route('docs.users') }}">Ver documentação</a>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Como funciona</div>
    <h2>Do evento bruto ao diagnóstico em segundos.</h2>
    <div class="row g-3 mt-2">
      <div class="col-md-4"><div class="cardx"><h3>1. Configure o endpoint</h3><p>Use o Payload URL do workspace no GitHub em Settings → Webhooks → Add webhook.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>2. Valide o secret</h3><p>O DevLog AI confere a assinatura <code>X-Hub-Signature-256</code> antes de aceitar o evento.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>3. Investigue no painel</h3><p>Veja repositório, branch, autor, commits, delivery id, payload sanitizado, notas, tarefas e análise AI.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Autoria e programa GitHub</div>
    <h2>{{ $creatorRole }}.</h2>
    <p class="lead">A iniciativa está inscrita no GitHub Developer Program e evolui como SaaS GitHub-first, com roadmap, operação, suporte, segurança e integração oficial por GitHub App.</p>
    <div class="row g-3 mt-2">
      <div class="col-md-4"><div class="cardx"><h3>GitHub App público</h3><p>O app está disponível em <a href="https://github.com/apps/gh-devlog" target="_blank" rel="noopener">github.com/apps/gh-devlog</a> para instalação e configuração.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Endpoint oficial</h3><p>Eventos do GitHub App chegam em <code>/webhooks/github-app</code> com validação de assinatura antes do processamento.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Suporte público</h3><p>Usuários podem falar pelo canal de <a href="{{ route('contact') }}">contato</a>, com páginas públicas de status, segurança, privacidade e termos.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Permissões e segurança</div>
    <h2>O padrão é privado, validado e isolado por workspace.</h2>
    <div class="row g-3 mt-2">
      <div class="col-md-4"><div class="cardx"><h3>Isolamento</h3><p>Cada usuário acessa apenas os eventos do próprio workspace.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Dados sensíveis</h3><p>Headers e campos com tokens, secrets e credenciais são mascarados antes do armazenamento.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>GitHub App</h3><p>O fluxo oficial de GitHub App está preparado para instalação por usuário ou organização.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Revisão e confiança</div>
    <h2>Uma integração feita para demonstrar funcionamento real, não só configuração.</h2>
    <p class="lead">
      O produto mantém histórico de entregas, delivery IDs, status de aceite, métricas de hardening e payload sanitizado para que devs consigam provar o que o GitHub enviou, quando chegou e como foi validado.
    </p>
    <div class="row g-3 mt-2">
      <div class="col-md-3"><div class="cardx"><h3>Eventos</h3><p><code>push</code>, <code>workflow_run</code>, <code>pull_request</code>, <code>issues</code> e eventos de instalação.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Callback</h3><p>O retorno OAuth do GitHub App usa <code>/github/callback</code> para concluir a vinculação ao workspace.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Privacidade</h3><p>Leia como dados de conta, workspace e webhooks são tratados na <a href="{{ route('privacy') }}">política de privacidade</a>.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Termos</h3><p>O uso permitido, responsabilidades e limitações estão documentados nos <a href="{{ route('terms') }}">termos de uso</a>.</p></div></div>
    </div>
  </section>
</x-layout>
