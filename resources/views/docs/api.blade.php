<x-layout title="API e Webhooks - GitHub DevLog AI">
  <main class="hero">
    <span class="eyebrow">Referencia técnica</span>
    <h1>API e webhooks para integrar GitHub ao DevLog AI.</h1>
    <p class="lead">
      Esta referencia mostra os endpoints públicos, headers esperados, validação de assinatura e respostas principais para quem vai configurar ou automatizar integrações.
    </p>
  </main>

  <section class="band">
    <div class="kicker">Webhook GitHub por workspace</div>
    <h2>Receba eventos em um endpoint isolado por workspace.</h2>
    <pre>POST /webhooks/github/{workspaceUuid}
Content-Type: application/json
X-GitHub-Event: push
X-GitHub-Delivery: 8189e9a8-43cc-11f1-8719-a9d9d4b439df
X-Hub-Signature-256: sha256=...</pre>
    <div class="row g-3 mt-3">
      <div class="col-md-4"><div class="cardx"><h3>workspaceUuid</h3><p>Identificador público do workspace. Ele aparece no painel do usuário autenticado.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Secret</h3><p>O segredo do workspace deve ser configurado no GitHub para gerar a assinatura HMAC SHA-256.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Payload</h3><p>O corpo JSON e armazenado de forma sanitizada e aparece no histórico privado do workspace.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Validacao de assinatura</div>
    <h2>Assinatura invalida deve falhar antes de salvar o evento.</h2>
    <pre>expected = "sha256=" + HMAC_SHA256(rawBody, workspaceSecret)
valid = hash_equals(expected, X-Hub-Signature-256)</pre>
    <p class="lead">Se a assinatura não existir ou não bater, o endpoint retorna <code>401</code> e o payload não entra no workspace.</p>
  </section>

  <section class="band">
    <div class="kicker">Respostas comuns</div>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>200 OK</h3><pre>{"ok": true, "id": 123}</pre><p>Evento aceito, validado e salvo.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>401 Unauthorized</h3><pre>{"error": "Assinatura GitHub invalida."}</pre><p>Secret incorreto ou assinatura ausente.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>429 Too Many Requests</h3><pre>{"error": "Limite mensal de eventos atingido."}</pre><p>Workspace atingiu o limite do plano.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">GitHub App</div>
    <h2>O caminho oficial para produção usa GitHub App.</h2>
    <pre>POST /webhooks/github-app
X-GitHub-Event: push
X-Hub-Signature-256: sha256=...</pre>
    <p class="lead">
      O endpoint de GitHub App valida o secret global do app, encontra a instalacao pelo <code>installation.id</code> e salva o evento no workspace vinculado.
    </p>
  </section>

  <section class="band">
    <div class="kicker">Mercado Pago</div>
    <h2>Billing recebe notificacoes assinadas do provedor.</h2>
    <pre>POST /webhooks/mercado-pago
x-signature: ...
x-request-id: ...</pre>
    <p class="lead">Esse endpoint processa eventos de pagamento, assinaturas e faturas de uso quando o secret do webhook está configurado.</p>
  </section>

  <section class="band">
    <div class="kicker">Boas práticas</div>
    <div class="row g-3">
      <div class="col-md-4"><div class="cardx"><h3>Use HTTPS</h3><p>Webhooks de produção devem apontar para dominio oficial com TLS ativo.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Rotacione secrets</h3><p>Se houver suspeita de exposicao, gere um novo secret no painel e atualize o GitHub.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Evite dados desnecessários</h3><p>Revise eventos e repositórios para não enviar payloads alem do necessário.</p></div></div>
    </div>
  </section>
</x-layout>

