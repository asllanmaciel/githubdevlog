<x-layout title="Suporte - GitHub DevLog AI">
  <section class="dashboard-hero">
    <div class="cardx">
      <div class="kicker">Suporte</div>
      <h1 class="dashboard-title">Abra um chamado com contexto tecnico suficiente para resolver rapido.</h1>
      <p class="lead mt-3 mb-0">Informe repositorio, evento, delivery id, horario aproximado e o que voce esperava ver. Isso ajuda o suporte a diagnosticar webhook, assinatura, billing ou GitHub App sem pedir tudo de novo.</p>
      <div class="control-strip mt-4">
        <div class="control-card"><div class="control-label">Webhook nao chegou</div><div class="control-value">URL, evento e horario</div></div>
        <div class="control-card"><div class="control-label">Assinatura invalida</div><div class="control-value">Secret e delivery id</div></div>
        <div class="control-card"><div class="control-label">Billing</div><div class="control-value">Plano e referencia MP</div></div>
      </div>
    </div>
    <form class="cardx" method="POST" action="{{ route('support.store') }}">
      @csrf
      <label>Assunto</label>
      <input name="subject" required placeholder="Ex: webhook push nao aparece no painel">
      <label class="mt-3">Mensagem</label>
      <textarea name="message" rows="10" required placeholder="Descreva: repositorio, evento, delivery id, horario, URL configurada, resposta do GitHub/Mercado Pago e o resultado esperado."></textarea>
      <button class="btnx primary w-100 mt-3" type="submit">Abrir chamado</button>
    </form>
  </section>
</x-layout>