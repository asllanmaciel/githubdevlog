<x-layout title="Confiança - GitHub DevLog AI">
  <main class="hero">
    <span class="eyebrow">Trust Center</span>
    <h1>Webhooks do GitHub carregam contexto sensível. O DevLog AI trata isso como produto, não como detalhe.</h1>
    <p class="lead">Esta página reúne os compromissos públicos de segurança, privacidade, operação e suporte para devs, times e revisores entenderem como a plataforma protege eventos, secrets e payloads.</p>
    <div class="d-flex gap-2 flex-wrap mt-4">
      <a class="btnx primary" href="{{ route('security') }}">Ver segurança</a>
      <a class="btnx" href="{{ route('privacy') }}">Privacidade</a>
      <a class="btnx" href="{{ route('status') }}">Status</a>
    </div>
  </main>

  <section class="band">
    <div class="kicker">Princípios</div>
    <h2>O padrão é privado, assinado e auditável.</h2>
    <div class="row g-3 mt-2">
      <div class="col-md-4"><div class="cardx"><h3>Isolamento por workspace</h3><p>Eventos pertencem ao workspace autenticado. Um dev não enxerga payloads de outro workspace.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Assinatura HMAC</h3><p>Webhooks GitHub são validados com <code>X-Hub-Signature-256</code> antes de entrar no histórico confiável.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Payload sanitizado</h3><p>Headers e campos sensíveis passam por limpeza para reduzir exposição acidental de tokens e secrets.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Operação</div>
    <h2>O produto foi pensado para sair da demo e virar rotina de time.</h2>
    <div class="row g-3 mt-2">
      <div class="col-md-3"><div class="cardx"><h3>Status público</h3><p>Saúde do sistema e incidentes relevantes devem ser acompanhados em uma página dedicada.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Suporte</h3><p>Usuários têm canal para chamados técnicos, billing, GitHub App, conta e segurança.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Auditoria</h3><p>Ações críticas como convites, secrets, billing e análises AI entram em trilha operacional.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Limites claros</h3><p>Uso mensal, retenção e AI avançada ficam visíveis no painel do workspace.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Dados e retenção</div>
    <h2>Menos surpresa, mais controle.</h2>
    <p class="lead">Planos podem definir limites de eventos, retenção e análises AI. O objetivo é dar previsibilidade para o dev: saber o que chegou, por quanto tempo fica disponível e quando o uso exige upgrade.</p>
    <div class="row g-3 mt-2">
      <div class="col-md-4"><div class="cardx"><h3>Secrets rotacionáveis</h3><p>O workspace permite trocar o secret quando houver suspeita, troca de equipe ou rotina de segurança.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>Exclusão e privacidade</h3><p>Pedidos de exportação, correção ou exclusão devem ser tratados pelo suporte e pela rotina administrativa.</p></div></div>
      <div class="col-md-4"><div class="cardx"><h3>AI com governança</h3><p>AI local é gratuita. AI avançada usa provedor externo, fica limitada por plano e registra custo estimado.</p></div></div>
    </div>
  </section>

  <section class="band">
    <div class="kicker">Antes do lançamento oficial</div>
    <h2>O que será fechado no go-live.</h2>
    <div class="row g-3 mt-2">
      <div class="col-md-3"><div class="cardx"><h3>Domínio HTTPS</h3><p>URL final pública para landing, webhooks, callback GitHub App e Mercado Pago.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>GitHub App real</h3><p>Permissões mínimas, webhook secret, callback e instalação oficial.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>Billing produção</h3><p>Mercado Pago com credenciais reais, assinatura de webhook e testes de baixo valor.</p></div></div>
      <div class="col-md-3"><div class="cardx"><h3>E-mail e fila</h3><p>Convites, notificações e suporte processados com infraestrutura de produção.</p></div></div>
    </div>
  </section>
</x-layout>