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

Substituir pelo dominio final:

```text
Homepage URL: https://seudominio.com
Callback URL: https://seudominio.com/github/callback
Webhook URL: https://seudominio.com/webhooks/github-app
Setup URL: https://github.com/apps/seu-app/installations/new
Privacy policy: https://seudominio.com/privacy
Terms: https://seudominio.com/terms
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