# Rascunho de listagem - GitHub Marketplace

Data de referencia: 2026-05-04.

## Status

O GitHub DevLog AI ja esta inscrito no GitHub Developer Program pela conta mantenedora e possui GitHub App publico em:

```text
https://github.com/apps/gh-devlog
```

A listagem no GitHub Marketplace e uma etapa separada. Ela deve ser tratada como publicacao do produto, nao como adesao ao Developer Program.

## Recomendacao

Publicar primeiro como app gratuito ou plano gratuito, enquanto o produto valida instalacoes reais, suporte e onboarding. A publicacao paga deve ficar para uma segunda fase, quando billing, suporte, termos comerciais e requisitos de Marketplace pago estiverem maduros.

## Campos principais

### App name

```text
GH DevLog
```

Alternativa alinhada ao produto:

```text
GitHub DevLog AI
```

### Short description

```text
Private webhook inbox for GitHub Apps and repository events, with signature validation, sanitized payload history and workspace-based debugging.
```

### Full description

```text
GitHub DevLog AI helps developers and teams debug GitHub webhooks with confidence. It receives GitHub App and repository webhook events, validates X-Hub-Signature-256, stores sanitized payloads in isolated workspaces and turns raw deliveries into a readable inbox.

Teams can inspect event type, delivery ID, repository, branch, sender, workflow status, commits, changed files and sanitized payload context. The product also supports notes, tasks, AI-assisted analysis and admin hardening metrics for accepted and rejected deliveries.

It is built for developers creating GitHub Apps, SaaS teams integrating with GitHub, agencies demonstrating automations and engineering teams that need an audit trail for webhook deliveries.
```

### Primary category

```text
Developer tools
```

Possible secondary categories:

```text
Monitoring
Code review / Collaboration
```

### Website

```text
https://ghdevlog.com
```

### GitHub integration page

```text
https://ghdevlog.com/github
```

### Support

```text
contato@asllanmaciel.com.br
https://ghdevlog.com/contact
```

### Privacy policy

```text
https://ghdevlog.com/privacy
```

### Terms of service

```text
https://ghdevlog.com/terms
```

### Status page

```text
https://ghdevlog.com/status
```

### Public GitHub App page

```text
https://github.com/apps/gh-devlog
```

## Integration value beyond authentication

GitHub DevLog AI does not only sign users in with GitHub. It receives and validates GitHub webhook deliveries, links GitHub App installations to workspaces, stores sanitized payloads, shows delivery IDs and event context, and gives teams an operational view of webhook health.

## Suggested free plan

```text
Starter / Free
```

Suggested positioning:

```text
For individual developers testing GitHub webhook flows and validating GitHub App deliveries.
```

Suggested limits:

```text
Limited monthly webhook events, basic retention, one workspace, valid signature history and public support channel.
```

## Screenshots to prepare

1. Public integration page at `https://ghdevlog.com/github`.
2. GitHub App public page at `https://github.com/apps/gh-devlog`.
3. Workspace GitHub App connected screen.
4. Workspace event inbox showing 30 valid events.
5. Event detail showing `workflow_run#35`, valid signature and delivery ID.
6. Admin hardening screen showing 35 accepted, 0 rejected and 100% valid signatures.
7. Privacy and Terms pages.

## Marketplace readiness checklist

- App provides value beyond authentication.
- Public app page exists.
- Website and integration page are live.
- Support email and contact page are live.
- Privacy policy is live.
- Terms page is live.
- Status page or health page is live.
- Pricing plan is defined, even if free.
- Screenshots do not expose secrets, private keys, webhook secrets, raw tokens or private customer payloads.
- GitHub App permissions and subscribed events are minimal and explainable.
- Marketplace webhook handling for plan changes/cancellations is designed before paid listing.

## Notes

The current evidence package supports a future Marketplace listing, but it should not be confused with the Developer Program membership. Developer Program membership is already complete; Marketplace publication is the next optional product distribution step.
