<x-layout title="FAQ - GitHub DevLog AI">
  <main class="hero">
    <span class="eyebrow">FAQ para devs</span>
    <h1>Antes de conectar seu GitHub, estas são as respostas que importam.</h1>
    <p class="lead">O DevLog AI foi criado para debug, auditoria e operação de webhooks GitHub. Esta FAQ resume como usar, o que fica privado, onde entra a AI e o que muda no lançamento oficial.</p>
  </main>

  <section class="band">
    <div class="kicker">Uso</div>
    <div class="row g-3">
      <div class="col-md-6"><div class="cardx"><h3>Como começo?</h3><p>Crie uma conta, abra seu workspace, copie o Payload URL e o Secret, configure no GitHub em Settings → Webhooks e envie um ping ou push.</p></div></div>
      <div class="col-md-6"><div class="cardx"><h3>Preciso instalar GitHub App?</h3><p>A primeira versão funciona com webhook manual por workspace. O GitHub App oficial entra como caminho recomendado para instalação mais limpa e escalável.</p></div></div>
      <div class="col-md-6"><div class="cardx"><h3>Vejo webhooks de outros devs?</h3><p>Não. Eventos ficam vinculados ao workspace. Você só acessa workspaces onde é membro e respeitando seu papel.</p></div></div>
      <div class="col-md-6"><div class="cardx"><h3>Posso usar para demo local?</h3><p>Sim. Use domínio público, tunnel HTTPS ou ambiente final. O importante é o GitHub conseguir chamar o endpoint público.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Segurança e dados</div>
    <div class="row g-3">
      <div class="col-md-6"><div class="cardx"><h3>Como a assinatura é validada?</h3><p>O endpoint confere <code>X-Hub-Signature-256</code> usando o Secret do workspace ou do GitHub App.</p></div></div>
      <div class="col-md-6"><div class="cardx"><h3>Payloads são públicos?</h3><p>Não. O histórico fica no workspace autenticado e passa por sanitização para reduzir exposição de tokens e secrets.</p></div></div>
      <div class="col-md-6"><div class="cardx"><h3>Posso rotacionar secret?</h3><p>Sim. Owner/admin pode gerar um novo secret e atualizar a configuração do webhook no GitHub.</p></div></div>
      <div class="col-md-6"><div class="cardx"><h3>Como peço exclusão de dados?</h3><p>Use o suporte do produto. Durante beta/lançamento inicial, pedidos podem ser tratados manualmente pelo admin.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">AI e planos</div>
    <div class="row g-3">
      <div class="col-md-6"><div class="cardx"><h3>Onde entra a AI?</h3><p>A AI resume eventos, identifica risco, destaca sinais e sugere próximos passos para não depender de leitura manual de payload gigante.</p></div></div>
      <div class="col-md-6"><div class="cardx"><h3>AI avançada é grátis?</h3><p>A AI local é inclusa. AI avançada usa provedor externo, tem custo real e por isso é limitada por plano ou cobrada por uso.</p></div></div>
      <div class="col-md-6"><div class="cardx"><h3>Como funcionam limites?</h3><p>O painel mostra eventos usados, limite mensal, retenção e consumo de AI avançada. Upgrades ficam ligados ao uso real.</p></div></div>
      <div class="col-md-6"><div class="cardx"><h3>O pricing é público?</h3><p>Nesta fase, a conversa comercial fica dentro do painel e do uso real. A página pública foca proposta, confiança e adoção.</p></div></div>
    </div>
  </section>
</x-layout>