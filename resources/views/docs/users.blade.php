<x-layout title="Docs para usuarios - GitHub DevLog AI">
  <section class="band" style="border-top:0; padding-top:0">
    <div class="kicker">Docs para usuarios</div>
    <h1>Receba, valide e investigue webhooks do GitHub sem depender do terminal.</h1>
    <p class="lead">Este guia mostra como um dev conecta um repositorio, valida a entrega, acompanha payloads e usa o DevLog AI como inbox privado para eventos do GitHub.</p>

    <div class="row g-3 mt-3">
      <div class="col-md-4"><div class="cardx"><h3>1. Crie seu workspace</h3><p>Cadastre-se, abra o dashboard e copie o Payload URL e o Secret gerados para o seu workspace privado.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>2. Configure no GitHub</h3><p>No repositorio, acesse Settings -> Webhooks -> Add webhook, use application/json e cole o Secret.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>3. Acompanhe eventos</h3><p>Veja assinatura, delivery id, repositorio, branch, commits, arquivos alterados e payload sanitizado.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Quando usar</div>
    <h2>Feito para debug, auditoria e integracoes GitHub.</h2>
    <div class="row g-3 mt-2">
      <div class="col-md-3"><div class="cardx"><h3>Debug</h3><p>Confirme se o GitHub chamou seu endpoint e com qual payload.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Seguranca</h3><p>Valide X-Hub-Signature-256 antes de confiar no evento.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Colaboracao</h3><p>Crie notas e tarefas a partir de eventos importantes.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Demos</h3><p>Mostre o fluxo GitHub -> DevLog AI -> painel em poucos minutos.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Checklist de primeiro webhook</div>
    <h2>O caminho mais curto para testar.</h2>
    <div class="row g-3 mt-2">
      <div class="col-lg-6"><div class="cardx"><h3>No GitHub</h3><p>Payload URL do dashboard, Content type application/json, Secret do workspace e evento push ou ping.</p></div></div>
      <div class="col-lg-6"><div class="cardx"><h3>No DevLog AI</h3><p>Abra o dashboard, verifique o status de assinatura, confirme o uso mensal e acompanhe o card de eventos recebidos.</p></div></div>
      <div class="col-lg-6"><div class="cardx"><h3>Se nao chegar</h3><p>Confirme se o tunnel/dominio esta online, se o Secret esta igual, se o GitHub mostra HTTP 200 e se o limite mensal nao foi atingido.</p></div></div>
      <div class="col-lg-6"><div class="cardx"><h3>Se chegar invalido</h3><p>Rotacione o Secret no DevLog AI, atualize o webhook no GitHub e envie um novo ping.</p></div></div>
    </div>
  </section>
</x-layout>