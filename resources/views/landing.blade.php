<x-layout :title="__('GitHub DevLog AI - Inbox privado para webhooks do GitHub')">
  @php
    $creatorName = config('devlog.creator_name');
    $creatorRole = config('devlog.creator_role');
    $creatorUrl = config('devlog.creator_url');
    $creatorAvatarUrl = config('devlog.creator_avatar_url');
  @endphp

  <main class="hero">
    <span class="eyebrow">{{ __('Feito para devs que precisam confiar nos webhooks do GitHub') }}</span>
    <h1>{{ __('Pare de perder tempo tentando descobrir se o webhook do GitHub chegou, falhou ou sumiu.') }}</h1>
    <p class="lead">
      {{ __('O DevLog AI é uma central privada para receber, validar e investigar webhooks do GitHub com clareza. Veja cada evento, assinatura, payload e entrega em um painel feito para debugging, demonstrações e auditoria real de integrações.') }}
    </p>
    <div class="d-flex gap-2 flex-wrap mt-4">
      <a class="btnx primary" href="{{ route('register') }}">{{ __('Criar meu workspace') }}</a>
      <a class="btnx" href="{{ route('login') }}">{{ __('Abrir painel') }}</a>
      <a class="btnx" href="#uso">{{ __('Ver passo a passo') }}</a>
    </div>

    <div class="hero-grid">
      <section class="terminal" aria-label="{{ __('Exemplo técnico') }}">
        <div class="bar"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
        <pre>POST /webhooks/github/7a2f...
X-GitHub-Event: push
X-Hub-Signature-256: sha256=...

{
  "repository": { "full_name": "acme/api" },
  "pusher": { "name": "ana" },
  "ref": "refs/heads/main"
}

{{ __('terminal.signature_validated') }}
{{ __('terminal.event_saved') }}
{{ __('terminal.payload_available') }}</pre>
      </section>
      <aside class="panel" aria-label="{{ __('Eventos no painel') }}">
        <div class="signal"><strong>push <span>{{ __('agora') }}</span></strong><span>{{ __('acme/api · validado por X-Hub-Signature-256') }}</span></div>
        <div class="signal"><strong>pull_request <span>2 min</span></strong><span>{{ __('acme/web · payload isolado no workspace') }}</span></div>
        <div class="signal"><strong>workflow_run <span>8 min</span></strong><span>{{ __('CI finalizado · pronto para análise') }}</span></div>
        <div class="signal"><strong>issues <span>14 min</span></strong><span>{{ __('nova issue recebida com delivery id') }}</span></div>
      </aside>
    </div>
  </main>

  <section class="band" id="produto">
    <div class="kicker">{{ __('A proposta') }}</div>
    <h2>{{ __('Um RequestBin privado, pensado para GitHub e para times de produto.') }}</h2>
    <p class="lead">
      {{ __('Quando um webhook falha, o problema raramente é só código. É contexto: qual evento chegou, qual repositório disparou, qual payload veio, se a assinatura bateu e quem consegue ver aquilo. O DevLog AI organiza esse fluxo em um workspace seguro para cada dev ou time.') }}
    </p>
    <div class="row g-3 mt-3">
      <div class="col-md-4"><div class="cardx"><h3>{{ __('Para devs') }}</h3><p>{{ __('Teste integrações GitHub sem depender de log local, print de terminal ou payload perdido.') }}</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>{{ __('Para times') }}</h3><p>{{ __('Compartilhe um workspace de debug com histórico comum e eventos separados por origem.') }}</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>{{ __('Para demos') }}</h3><p>{{ __('Mostre o fluxo de ponta a ponta: GitHub envia, o DevLog valida e o painel explica.') }}</p></div></div>
    </div>
  </section>

  <section class="band" id="criador">
    <div class="kicker">{{ __('Por trás do produto') }}</div>
    <div class="creator-profile">
      <div>
        <img class="creator-photo" src="{{ $creatorAvatarUrl }}" alt="{{ $creatorName }}">
      </div>
      <div>
        <h2>{{ __('Oi, eu sou :name. Criei o DevLog AI para transformar webhooks em clareza.', ['name' => $creatorName]) }}</h2>
        <p class="lead">
          {{ __('A ideia nasceu de uma dor bem prática: integrar GitHub, testar eventos, provar que um webhook chegou e explicar o que aconteceu sem depender de terminal aberto, prints ou logs perdidos. O DevLog AI organiza esse fluxo em um workspace privado, auditável e pronto para times.') }}
        </p>
        <div class="creator-badges">
          <span class="pill">{{ __('Criado por dev') }}</span>
          <span class="pill">GitHub-first</span>
          <span class="pill">{{ __('SaaS em evolução') }}</span>
          <span class="pill">{{ __('Foco em webhooks reais') }}</span>
        </div>
        <p class="muted">{{ __($creatorRole) }}</p>
        @if ($creatorUrl)
          <div class="mt-3"><a class="btnx" href="{{ $creatorUrl }}" target="_blank" rel="noopener">{{ __('Ver meu GitHub') }}</a></div>
        @endif
      </div>
    </div>
  </section>

  <section class="band steps" id="uso">
    <div class="kicker">{{ __('Como o dev usa') }}</div>
    <h2>{{ __('Do cadastro ao primeiro webhook em minutos.') }}</h2>
    <div class="row g-3">
      <div class="col-lg-4"><div class="cardx step"><h3>{{ __('Crie uma conta') }}</h3><p>{{ __('O cadastro gera um workspace privado com endpoint e segredo próprios. Cada usuário enxerga somente os eventos do seu workspace.') }}</p></div></div>
      <div class="col-lg-4"><div class="cardx step"><h3>{{ __('Configure no GitHub') }}</h3><p>{!! __('No repositório, adicione o Payload URL do painel, escolha :content_type e cole o Secret do workspace.', ['content_type' => '<code>application/json</code>']) !!}</p></div></div>
      <div class="col-lg-4"><div class="cardx step"><h3>{{ __('Acompanhe no painel') }}</h3><p>{!! __('Ao enviar :push, :pull_request, :issues ou :workflow_run, o evento aparece com payload, horário e validação.', ['push' => '<code>push</code>', 'pull_request' => '<code>pull_request</code>', 'issues' => '<code>issues</code>', 'workflow_run' => '<code>workflow_run</code>']) !!}</p></div></div>
    </div>
  </section>

  <section class="band" id="segurança">
    <div class="kicker">{{ __('Segurança e isolamento') }}</div>
    <h2>{{ __('Webhook sem conta vira vazamento. Aqui o padrão é privado.') }}</h2>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>{{ __('Workspace por conta') }}</h3><p>{{ __('Eventos são filtrados pelo workspace autenticado. Um dev não vê webhooks de outro.') }}</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>{{ __('Assinatura do GitHub') }}</h3><p>{!! __('O endpoint aceita :header usando o Secret configurado no repositório.', ['header' => '<code>X-Hub-Signature-256</code>']) !!}</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>{{ __('Rotação de segredo') }}</h3><p>{{ __('Se o segredo vazar, o painel permite gerar outro e atualizar a integração.') }}</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">{{ __('Comece agora') }}</div>
    <h2>{{ __('Crie um workspace e conecte seu primeiro repositório GitHub.') }}</h2>
    <p class="lead">{{ __('A ferramenta já entrega o núcleo do produto: cadastro, login, workspace, segredo, endpoint, painel privado e captura de eventos.') }}</p>
    <div class="d-flex gap-2 flex-wrap mt-3">
      <a class="btnx primary" href="{{ route('register') }}">{{ __('Criar workspace') }}</a>
      <a class="btnx" href="{{ route('login') }}">{{ __('Entrar no painel') }}</a>
    </div>
  </section>

  <footer class="footer">
    <div class="row g-4">
      <div class="col-lg-5">
        <strong>GitHub DevLog AI</strong>
        <p class="muted mt-2 mb-0">{{ __('Criado por :name para validar webhooks do GitHub com menos ruído, mais segurança e histórico confiável.', ['name' => $creatorName]) }}</p>
      </div>
      <div class="col-6 col-lg-2">
        <div class="kicker">{{ __('Produto') }}</div>
        <div class="d-grid gap-2">
          <a href="{{ route('docs.api') }}">API</a>
          <a href="{{ route('pricing') }}">{{ __('Planos') }}</a>
          <a href="{{ route('changelog') }}">Changelog</a>
        </div>
      </div>
      <div class="col-6 col-lg-2">
        <div class="kicker">{{ __('Confiança') }}</div>
        <div class="d-grid gap-2">
          <a href="{{ route('status') }}">Status</a>
          <a href="{{ route('security') }}">{{ __('Segurança') }}</a>
          <a href="{{ route('contact') }}">{{ __('Contato') }}</a>
        </div>
      </div>
      <div class="col-lg-3">
        <div class="kicker">Legal</div>
        <div class="d-grid gap-2">
          <a href="{{ route('privacy') }}">{{ __('Privacidade') }}</a>
          <a href="{{ route('terms') }}">{{ __('Termos de uso') }}</a>
          <a href="{{ route('github') }}">GitHub Developer Program</a>
        </div>
      </div>
    </div>
  </footer>
</x-layout>
