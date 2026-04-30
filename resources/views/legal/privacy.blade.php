<x-layout title="Privacidade - GitHub DevLog AI">
  <main class="hero">
    <span class="eyebrow">Privacidade e dados</span>
    <h1>Eventos do GitHub tratados como dados sensiveis desde o primeiro webhook.</h1>
    <p class="lead">O GitHub DevLog AI foi desenhado para receber, validar e organizar webhooks em workspaces privados. Esta pagina resume como pensamos coleta, uso, retencao e isolamento de dados.</p>
  </main>

  <section class="band">
    <div class="kicker">O que coletamos</div>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>Conta</h3><p>Nome, email, senha protegida por hash e dados basicos para autenticar o usuario.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Workspace</h3><p>Nome do workspace, membros, papeis, convites, secrets rotacionaveis e preferencias de uso.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Webhooks</h3><p>Headers tecnicos, payload sanitizado, evento, delivery id, status de assinatura e data de recebimento.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Como usamos</div>
    <h2>Usamos dados para operar o produto, nao para vender informacao de devs.</h2>
    <p class="lead">Os dados sao usados para autenticar usuarios, isolar workspaces, validar assinaturas, exibir historico, calcular limites de uso, enviar notificacoes, prestar suporte e proteger a plataforma contra abuso.</p>
  </section>

  <section class="band">
    <div class="kicker">Compartilhamento</div>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>Sem payload publico</h3><p>Eventos pertencem ao workspace autenticado. Um usuario nao acessa webhooks de outro workspace.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Pagamentos</h3><p>Dados de pagamento sao processados pelo Mercado Pago conforme o fluxo contratado.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Obrigacoes legais</h3><p>Podemos preservar ou compartilhar informacoes quando necessario para cumprir lei, fraude, abuso ou ordem competente.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Direitos</div>
    <h2>O usuario deve conseguir pedir exportacao, correcao ou exclusao.</h2>
    <p class="lead">Antes do live, este canal deve apontar para o email oficial de suporte. Durante o beta, os pedidos podem ser tratados manualmente pelo administrador do produto.</p>
  </section>
</x-layout>
