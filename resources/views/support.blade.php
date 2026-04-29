<x-layout title="Suporte - GitHub DevLog AI">
  <section class="dashboard-hero">
    <div class="cardx">
      <div class="kicker">Suporte</div>
      <h1 class="dashboard-title">Precisa de ajuda com webhooks, painel ou GitHub?</h1>
      <p class="lead mt-3 mb-0">Abra um chamado com contexto técnico. Isso cria a base para atendimento dentro do próprio SaaS.</p>
    </div>
    <form class="cardx" method="POST" action="{{ route('support.store') }}">
      @csrf
      <label>Assunto</label>
      <input name="subject" required placeholder="Ex: webhook não aparece no painel">
      <label class="mt-3">Mensagem</label>
      <textarea name="message" rows="8" required placeholder="Conte o que aconteceu, repositório, evento e horário aproximado."></textarea>
      <button class="btnx primary w-100 mt-3" type="submit">Abrir chamado</button>
    </form>
  </section>
</x-layout>