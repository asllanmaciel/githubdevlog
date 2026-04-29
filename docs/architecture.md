# Arquitetura

O projeto segue uma arquitetura modular pequena (single service style), com três camadas:

- Entrada HTTP (`public/index.php`) recebendo webhooks do GitHub.
- Domínio de integração (`app/Services`) com parser do webhook e gerador de resumo.
- Persistência (`app/Core/Database.php` + `database/schema.sql`) para eventos e resumos.

## Fluxo

1. GitHub envia `push` para `POST /webhook/github`.
2. Controller valida a assinatura HMAC (`X-Hub-Signature-256`) quando `GITHUB_WEBHOOK_SECRET` existir.
3. `GitHubService` parseia o payload e normaliza commits.
4. `DevLogService` persiste repositório + evento + commits.
5. `OpenAIService` gera devlog (ou fallback local).
6. `devlog` é salvo e retornado ao cliente do webhook.

## Segurança

- Assinatura com secret de webhook.
- Idempotência por `X-GitHub-Delivery`.
- Persistência local para rastrear histórico de geração e diagnóstico.
