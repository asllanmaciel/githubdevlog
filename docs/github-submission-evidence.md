# Evidencias para submissao ao GitHub Developer Program

Este arquivo organiza o material visual e textual necessario para apresentar o GitHub DevLog AI como uma ferramenta para desenvolvedores que integram com GitHub.

## Posicionamento

GitHub DevLog AI e um inbox privado para webhooks do GitHub. Ele ajuda devs a validar entregas, conferir payloads, verificar assinatura HMAC e transformar eventos recebidos em contexto acionavel para debugging, suporte e colaboracao.

## Screenshots recomendados

1. Landing publica
   - Mostrar headline, proposta de valor e CTA para criar workspace.
   - Objetivo: provar que existe uma narrativa clara para devs.

2. Dashboard do usuario
   - Mostrar endpoint, secret, onboarding e lista de eventos.
   - Objetivo: provar que o usuario entende como conectar um repositorio.

3. Evento GitHub validado
   - Mostrar event name, delivery id, repository, assinatura valida e payload.
   - Objetivo: provar a utilidade central do produto.

4. Nota e tarefa em webhook
   - Mostrar colaboracao em cima de um evento.
   - Objetivo: diferenciar de logs brutos/request bins genericos.

5. Admin Launch Gate
   - Mostrar preflight strict, bloqueadores e readiness.
   - Objetivo: demonstrar maturidade operacional.

6. Security Center
   - Mostrar checks de seguranca, secrets e headers.
   - Objetivo: reforcar privacidade e seguranca.

7. Billing e planos
   - Mostrar modelo SaaS por uso.
   - Objetivo: provar sustentabilidade do produto.

## Evidencias reais coletadas

Data de referencia: 2026-05-04.

- Dominio de producao: `https://ghdevlog.com`.
- Dashboard de eventos: `https://ghdevlog.com/dashboard/events`.
- Configuracao GitHub do workspace: `https://ghdevlog.com/dashboard/github`.
- Admin de eventos: `https://ghdevlog.com/admin/webhook-events`.
- Admin de hardening: `https://ghdevlog.com/admin/webhook-hardening`.
- GitHub App instalado e vinculado ao workspace pela instalacao `128516060`.
- Webhook GitHub App oficial recebendo por `https://ghdevlog.com/webhooks/github-app`.
- Validacao registrada como `github-app-x-hub-signature-256`.
- Hardening com 35 eventos no banco, 35 aceitos, 0 rejeitados e 100% de assinaturas validas.
- Eventos reais observados: `push`, `workflow_run`, `installation`.
- Repositorio real observado: `AM-TIIX/TIIX-Global`.
- Workflow real observado: `changelog-automation`.
- Entregas recentes para usar como evidencia de auditoria: `748cd20c-47b8-11f1-8edb-37f3ef415db9`, `7604d9e0-47b8-11f1-8f44-1f032ed253fc`, `86a1004e-47b8-11f1-9870-af3be485e4ae`, `878c00d0-47b8-11f1-86e7-e2ec2588e77b`.
- Nao anexar nem transcrever secrets em prints, videos, issues ou formularios de submissao.

## Checklist de captura com evidencias reais

- Print da tela `dashboard/github` mostrando o GitHub App vinculado e o webhook oficial.
- Print da tela `dashboard/events` mostrando eventos `push` e `workflow_run` com assinatura valida.
- Print de um detalhe de evento, preferencialmente `workflow_run completed`, mostrando delivery id, repositorio, branch, sha e payload sanitizado.
- Print do admin `webhook-events` mostrando registros aceitos, metodo de validacao e datas de recebimento/processamento.
- Print do admin `webhook-hardening` mostrando 35 aceitos, 0 rejeitados e validade 100%.
- Video curto do fluxo: instalar GitHub App, gerar push em repositorio real, abrir evento no inbox e conferir assinatura valida.
- Evidencia tecnica local: teste automatizado `WebhookHardeningTest` cobrindo GitHub App, vinculacao de repositorio e exibicao na dashboard.

## Respostas para formulario

### What does your application do?

GitHub DevLog AI is a private webhook inbox for GitHub developers. It validates GitHub webhook signatures, stores sanitized payloads in isolated workspaces and gives teams a readable delivery history for debugging push, pull_request, workflow_run and issue events.

### Who is the target audience?

Developers building GitHub Apps, SaaS teams integrating with GitHub webhooks, agencies demonstrating GitHub automations and teams that need a reliable audit trail for webhook deliveries.

### How does it integrate with GitHub?

Users configure a repository webhook or install the GitHub App. The platform receives GitHub events, validates `X-Hub-Signature-256`, maps each event to the correct workspace and displays delivery metadata, repository context and sanitized payloads.

### How do you handle security and privacy?

Workspaces are isolated, secrets are unique per workspace, webhook signatures are validated, sensitive payload fields are sanitized, secrets can be rotated and the admin panel includes production, security and launch readiness checks.

## GitHub App URLs

```text
Homepage URL: https://ghdevlog.com
Callback URL: https://ghdevlog.com/github/callback
Webhook URL: https://ghdevlog.com/webhooks/github-app
Setup URL: https://github.com/apps/seu-app/installations/new
Privacy policy: https://ghdevlog.com/privacy
Terms: https://ghdevlog.com/terms
```

## Permissoes sugeridas

Comecar com o menor escopo possivel:

- Metadata: read-only
- Pull requests: read-only, se eventos de PR forem exibidos
- Issues: read-only, se eventos de issues forem exibidos
- Contents: read-only opcional para evolucao de contexto de arquivos
- Webhook events: receber push, pull_request, workflow_run e issues conforme plano do usuario

## Checklist antes de enviar

- Dominio final em HTTPS.
- `php artisan devlog:preflight --strict` aprovado.
- `/admin/production-environment` sem pendencias obrigatorias.
- GitHub App real criado.
- Webhook do GitHub App com resposta 200.
- Instalar app em repositorio real de teste.
- Capturar screenshots recomendados.
- Mercado Pago sandbox/producao demonstravel.
- Politica de privacidade e termos publicados.
## Controle no admin

Para criar o checklist gerenciavel de screenshots e videos:

```bash
php artisan devlog:seed-submission-assets
```

Depois acompanhe em:

```text
/admin/submission-assets
/admin/launch-tests
```

Use o campo `evidence` do QA de lancamento para colar links, caminhos locais ou referencias de Drive/Figma/Notion.
