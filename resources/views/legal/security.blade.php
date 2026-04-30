<x-layout title="Segurança - GitHub DevLog AI">
  <main class="hero">
    <span class="eyebrow">Segurança GitHub-first</span>
    <h1>Webhooks privados, assinados, isolados por workspace e preparados para auditoria.</h1>
    <p class="lead">O DevLog AI existe para reduzir incerteza em integrações GitHub. A arquitetura parte do principio de que payload de webhook pode conter informação sensivel.</p>
  </main>

  <section class="band">
    <div class="kicker">Controles principais</div>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>Assinatura GitHub</h3><p>Endpoints validam <code>X-Hub-Signature-256</code> com secret do workspace ou do GitHub App.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Isolamento</h3><p>Eventos sao vinculados ao workspace correto. Membros acessam apenas o que seu papel permite.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Rotacao de secret</h3><p>O painel permite rotacionar o segredo do webhook e registrar a acao para auditoria.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Operacao segura</div>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>Sanitizacao</h3><p>Headers e payloads passam por limpeza para reduzir exposicao de tokens e segredos acidentais.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Auditoria</h3><p>Acoes criticas, como rotacao de segredo, convites e billing, ficam registradas.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Status público</h3><p>Incidentes e saude do sistema podem ser acompanhados pela página de status.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Boas práticas para usuários</div>
    <h2>Configure o GitHub como se o payload pudesse conter informação privada.</h2>
    <p class="lead">Use HTTPS, mantenha o secret fora do repositório, rotacione credenciais quando houver suspeita, convide apenas membros necessários e evite enviar dados pessoais desnecessários nos eventos.</p>
  </section>
</x-layout>

