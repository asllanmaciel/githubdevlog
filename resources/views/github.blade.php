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
    <p class="lead">A iniciativa está sendo estruturada como SaaS GitHub-first para submissão e evolução no ecossistema do GitHub Developer Program, com roadmap, operação, suporte, segurança e integração oficial por GitHub App.</p>
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
</x-layout>