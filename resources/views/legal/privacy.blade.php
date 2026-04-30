<x-layout title="Privacidade - GitHub DevLog AI">
  <main class="hero">
    <span class="eyebrow">Privacidade e dados</span>
    <h1>Eventos do GitHub tratados como dados sensíveis desde o primeiro webhook.</h1>
    <p class="lead">O GitHub DevLog AI foi desenhado para receber, validar e organizar webhooks em workspaces privados. Esta página resume como pensamos coleta, uso, retenção e isolamento de dados.</p>
  </main>

  <section class="band">
    <div class="kicker">O que coletamos</div>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>Conta</h3><p>Nome, email, senha protegida por hash e dados básicos para autenticar o usuário.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Workspace</h3><p>Nome do workspace, membros, papeis, convites, secrets rotacionaveis e preferencias de uso.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Webhooks</h3><p>Headers técnicos, payload sanitizado, evento, delivery id, status de assinatura e data de recebimento.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Como usamos</div>
    <h2>Usamos dados para operar o produto, não para vender informação de devs.</h2>
    <p class="lead">Os dados sao usados para autenticar usuários, isolar workspaces, validar assinaturas, exibir histórico, calcular limites de uso, enviar notificacoes, prestar suporte e proteger a plataforma contra abuso.</p>
  </section>

  <section class="band">
    <div class="kicker">Compartilhamento</div>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>Sem payload público</h3><p>Eventos pertencem ao workspace autenticado. Um usuário não acessa webhooks de outro workspace.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Pagamentos</h3><p>Dados de pagamento sao processados pelo Mercado Pago conforme o fluxo contratado.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Obrigações legais</h3><p>Podemos preservar ou compartilhar informações quando necessário para cumprir lei, fraude, abuso ou ordem competente.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Direitos</div>
    <h2>O usuário deve conseguir pedir exportacao, correção ou exclusão.</h2>
    <p class="lead">Antes do live, este canal deve apontar para o email oficial de suporte. Durante o beta, os pedidos podem ser tratados manualmente pelo administrador do produto.</p>
  </section>
</x-layout>

