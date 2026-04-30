<x-layout title="Contato - GitHub DevLog AI">
  @php
    $supportEmail = config('devlog.support_email');
    $creatorName = config('devlog.creator_name');
    $creatorUrl = config('devlog.creator_url');
  @endphp

  <main class="hero">
    <span class="eyebrow">Contato e suporte</span>
    <h1>Fale com o GitHub DevLog AI antes, durante ou depois da integracao.</h1>
    <p class="lead">
      Use este canal para duvidas sobre produto, seguranca, billing, GitHub App, beta privado ou parcerias tecnicas.
    </p>
    <div class="d-flex gap-2 flex-wrap mt-4">
      <a class="btnx primary" href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
      <a class="btnx" href="{{ route('docs.api') }}">Ver docs tecnicas</a>
    </div>
  </main>

  <section class="band">
    <div class="kicker">Canais</div>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="cardx">
          <h3>Suporte geral</h3>
          <p>Duvidas sobre conta, workspace, planos, configuracao do GitHub e uso do painel.</p>
          <div class="mt-3"><a class="btnx" href="mailto:{{ $supportEmail }}">Enviar email</a></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="cardx">
          <h3>Seguranca</h3>
          <p>Reporte suspeita de vazamento, abuso, falha de assinatura, acesso indevido ou problema de isolamento.</p>
          <div class="mt-3"><a class="btnx" href="{{ route('security') }}">Ver seguranca</a></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="cardx">
          <h3>GitHub App</h3>
          <p>Para revisao, instalacao, permissoes, webhooks oficiais e validacao do fluxo de integracao.</p>
          <div class="mt-3"><a class="btnx" href="{{ route('github') }}">Ver integracao GitHub</a></div>
        </div>
      </div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Responsavel</div>
    <h2>Projeto criado por {{ $creatorName }}.</h2>
    <p class="lead">
      O projeto esta sendo preparado para beta, GitHub Developer Program e operacao SaaS. Se voce quer testar, revisar ou sugerir um caso de uso, o contato publico e o melhor ponto de partida.
    </p>
    @if ($creatorUrl)
      <div class="mt-3"><a class="btnx" href="{{ $creatorUrl }}" target="_blank" rel="noopener">Ver perfil no GitHub</a></div>
    @endif
  </section>
</x-layout>
