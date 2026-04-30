<x-layout title="Contato - GitHub DevLog AI">
  @php
    $supportEmail = config('devlog.support_email');
    $creatorName = config('devlog.creator_name');
    $creatorUrl = config('devlog.creator_url');
  @endphp

  <main class="hero">
    <span class="eyebrow">Contato e suporte</span>
    <h1>Fale com o GitHub DevLog AI antes, durante ou depois da integração.</h1>
    <p class="lead">
      Use este canal para dúvidas sobre produto, segurança, billing, GitHub App, beta privado ou parcerias técnicas.
    </p>
    <div class="d-flex gap-2 flex-wrap mt-4">
      <a class="btnx primary" href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
      <a class="btnx" href="{{ route('docs.api') }}">Ver docs técnicas</a>
    </div>
  </main>

  <section class="band">
    <div class="kicker">Canais</div>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="cardx">
          <h3>Suporte geral</h3>
          <p>Duvidas sobre conta, workspace, planos, configuração do GitHub e uso do painel.</p>
          <div class="mt-3"><a class="btnx" href="mailto:{{ $supportEmail }}">Enviar email</a></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="cardx">
          <h3>Segurança</h3>
          <p>Reporte suspeita de vazamento, abuso, falha de assinatura, acesso indevido ou problema de isolamento.</p>
          <div class="mt-3"><a class="btnx" href="{{ route('security') }}">Ver segurança</a></div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="cardx">
          <h3>GitHub App</h3>
          <p>Para revisao, instalacao, permissoes, webhooks oficiais e validação do fluxo de integração.</p>
          <div class="mt-3"><a class="btnx" href="{{ route('github') }}">Ver integração GitHub</a></div>
        </div>
      </div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Responsável</div>
    <h2>Projeto criado por {{ $creatorName }}.</h2>
    <p class="lead">
      O projeto está sendo preparado para beta, GitHub Developer Program e operação SaaS. Se você quer testar, revisar ou sugerir um caso de uso, o contato público e o melhor ponto de partida.
    </p>
    @if ($creatorUrl)
      <div class="mt-3"><a class="btnx" href="{{ $creatorUrl }}" target="_blank" rel="noopener">Ver perfil no GitHub</a></div>
    @endif
  </section>
</x-layout>

