<x-layout title="Docs para usuários - GitHub DevLog AI">
  <section class="hero" style="padding-top:0">
    <span class="eyebrow">Documentação para devs</span>
    <h1>Use o GitHub DevLog AI como inbox privado e auditável dos seus webhooks.</h1>
    <p class="lead">Este guia é para quem vai usar a ferramenta no dia a dia: configurar GitHub, validar assinatura, entender eventos, colaborar com o time, controlar uso e abrir suporte quando algo não bater.</p>
    <div class="d-flex gap-2 flex-wrap mt-4">
      <a class="btnx primary" href="{{ route('register') }}">Criar workspace</a>
      <a class="btnx" href="#primeiro-webhook">Configurar primeiro webhook</a>
      <a class="btnx" href="#troubleshooting">Resolver problemas</a>
    </div>
  </section>

  <section class="band" id="visao-geral">
    <div class="kicker">Visão geral</div>
    <h2>O que a ferramenta resolve.</h2>
    <p class="lead">Webhooks do GitHub são poderosos, mas difíceis de depurar quando você só tem logs locais, payloads enormes e respostas espalhadas. O DevLog AI centraliza esses eventos em workspaces privados, valida a assinatura e transforma cada entrega em uma timeline legível.</p>
    <div class="row g-3 mt-3">
      <div class="col-md-4"><div class="cardx"><h3>Inbox privado</h3><p>Cada workspace tem endpoint e secret próprios. Um dev não vê webhooks de outro workspace.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Assinatura validada</h3><p>Eventos GitHub são aceitos com <code>X-Hub-Signature-256</code> e payload sanitizado.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Colaboração</h3><p>Dev, admin e viewer podem trabalhar com papéis diferentes sem compartilhar senha ou secret.</p></div></div>
    </div>
  </section>

  <section class="band steps" id="primeiro-webhook">
    <div class="kicker">Primeiro webhook</div>
    <h2>Do GitHub ao primeiro evento recebido.</h2>
    <div class="row g-3">
      <div class="col-lg-4"><div class="cardx step"><h3>Crie ou acesse um workspace</h3><p>Entre no painel e confirme se você está no workspace correto. O painel mostra seu papel, plano, uso mensal e status da assinatura.</p></div></div>
      <div class="col-lg-4"><div class="cardx step"><h3>Copie Payload URL e Secret</h3><p>Na área de configuração GitHub, copie o endpoint privado e o secret do workspace. Somente owner/admin podem rotacionar o secret.</p></div></div>
      <div class="col-lg-4"><div class="cardx step"><h3>Configure no GitHub</h3><p>No repositório, vá em <strong>Settings → Webhooks → Add webhook</strong>, use <code>application/json</code>, cole a URL e o secret.</p></div></div>
    </div>
    <div class="row g-3 mt-1">
      <div class="col-lg-4"><div class="cardx step"><h3>Escolha eventos</h3><p>Comece com <code>push</code> e <code>pull_request</code>. Depois adicione <code>issues</code>, <code>workflow_run</code> ou outros conforme a integração.</p></div></div>
      <div class="col-lg-4"><div class="cardx step"><h3>Envie um ping</h3><p>O GitHub envia um evento <code>ping</code> quando o webhook é criado. Se ele chegou, você verá delivery id, repositório e payload no painel.</p></div></div>
      <div class="col-lg-4"><div class="cardx step"><h3>Investigue no painel</h3><p>Abra o evento para ver assinatura, origem, action, commits, arquivos alterados, notas, tarefas e payload bruto sanitizado.</p></div></div>
    </div>
  </section>

  <section class="band" id="github-app">
    <div class="kicker">GitHub App</div>
    <h2>Quando usar webhook manual e quando usar GitHub App.</h2>
    <div class="row g-3 mt-2">
      <div class="col-md-6"><div class="cardx"><h3>Webhook manual</h3><p>Bom para testar rápido em um repositório específico. Você copia URL e secret, configura no GitHub e acompanha eventos imediatamente.</p></div></div>
      <div class="col-md-6"><div class="cardx"><h3>GitHub App</h3><p>Melhor para uso recorrente, times e organizações. O app permite instalação controlada e roteamento por instalação vinculada ao workspace.</p></div></div>
    </div>
  </section>

  <section class="band" id="equipe">
    <div class="kicker">Equipe e permissões</div>
    <h2>Colaboração sem compartilhar credenciais.</h2>
    <div class="row g-3 mt-2">
      <div class="col-md-3"><div class="cardx"><h3>Owner</h3><p>Gerencia billing, equipe, secrets, GitHub App, testes e investigação.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Admin</h3><p>Opera o workspace e gerencia membros, billing, secrets e GitHub App.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Developer</h3><p>Investiga eventos, cria testes, notas e tarefas, sem mexer em billing ou secret.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Viewer</h3><p>Acompanha eventos e pode abrir suporte, mas não executa ações sensíveis.</p></div></div>
    </div>
  </section>

  <section class="band" id="eventos">
    <div class="kicker">Eventos, notas e tarefas</div>
    <h2>Transforme payload em diagnóstico.</h2>
    <div class="hero-grid">
      <section class="terminal" aria-label="Exemplo de payload">
        <div class="bar"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
        <pre>{
  "event_name": "push",
  "repository": "acme/api",
  "delivery_id": "8189e9a8...",
  "signature": "valid",
  "branch": "main",
  "commits": 3
}</pre>
      </section>
      <aside class="panel">
        <div class="signal"><strong>Notas</strong><span>registre hipótese, decisão ou contexto do incidente</span></div>
        <div class="signal"><strong>Tarefas</strong><span>crie ações para investigar deploy, CI ou payload</span></div>
        <div class="signal"><strong>Payload sanitizado</strong><span>tokens e secrets são mascarados antes do armazenamento</span></div>
        <div class="signal"><strong>Delivery id</strong><span>use para comparar com a entrega exibida no GitHub</span></div>
      </aside>
    </div>
  </section>

  <section class="band" id="billing">
    <div class="kicker">Planos e uso</div>
    <h2>Controle consumo antes de perder eventos.</h2>
    <div class="row g-3 mt-2">
      <div class="col-md-4"><div class="cardx"><h3>Limite mensal</h3><p>Cada plano define quantidade mensal de eventos. O painel mostra uso e alertas preventivos.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Retenção</h3><p>Eventos antigos seguem a política de retenção do plano. Isso ajuda a controlar custo e privacidade.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Cancelamento</h3><p>Owner/admin podem cancelar a assinatura no dashboard. A ação fica auditada e registrada.</p></div></div>
    </div>
  </section>

  <section class="band" id="troubleshooting">
    <div class="kicker">Troubleshooting</div>
    <h2>Se algo não funcionar, comece por aqui.</h2>
    <div class="row g-3 mt-2">
      <div class="col-lg-6"><div class="cardx"><h3>Webhook não chegou</h3><p>Confirme se a URL está pública, se o GitHub recebeu HTTP 200, se o endpoint está correto e se o limite mensal não foi atingido.</p></div></div>
      <div class="col-lg-6"><div class="cardx"><h3>Assinatura inválida</h3><p>Confira se o Secret no GitHub é exatamente o mesmo exibido no workspace. Se necessário, rotacione o secret e envie novo ping.</p></div></div>
      <div class="col-lg-6"><div class="cardx"><h3>Evento aparece incompleto</h3><p>Verifique se o GitHub está enviando <code>application/json</code> e se o evento selecionado inclui os campos esperados.</p></div></div>
      <div class="col-lg-6"><div class="cardx"><h3>Billing ou plano</h3><p>Veja a central da assinatura, referência Mercado Pago e notificações. Se algo não bater, abra chamado com a referência.</p></div></div>
    </div>
  </section>

  <section class="band" id="suporte">
    <div class="kicker">Suporte</div>
    <h2>Abra chamado com contexto técnico suficiente.</h2>
    <p class="lead">Inclua repositório, delivery id, horário aproximado, evento GitHub, URL configurada, resposta HTTP do GitHub/Mercado Pago e o resultado esperado. Isso reduz ida e volta e acelera a resolução.</p>
    <div class="d-flex gap-2 flex-wrap mt-3">
      <a class="btnx primary" href="{{ route('support') }}">Abrir suporte</a>
      <a class="btnx" href="{{ route('status') }}">Ver status público</a>
    </div>
  </section>
</x-layout>