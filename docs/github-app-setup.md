# Setup do GitHub App

Este documento acompanha os bloqueadores atuais de lancamento oficial no GitHub Developer Program.

## Objetivo

Transformar o GitHub DevLog AI de receptor manual de webhooks em uma integracao oficial instalavel no GitHub, com OAuth, webhook assinado e instalacoes vinculadas a workspaces.

## URLs para criar o app

Use o painel admin em `Admin > Lancamento > Prontidao GitHub` para copiar os valores atuais:

- Homepage URL: pagina publica `/github`.
- Webhook URL: `/webhooks/github-app`.
- Callback URL: `/github/callback`.
- Setup URL: `/github/install`.

Em producao, todas precisam usar HTTPS e dominio oficial.

## Variaveis obrigatorias

- `GITHUB_APP_ID`
- `GITHUB_APP_CLIENT_ID`
- `GITHUB_APP_CLIENT_SECRET`
- `GITHUB_APP_WEBHOOK_SECRET`
- `GITHUB_APP_PRIVATE_KEY_PATH`
- `GITHUB_APP_WEBHOOK_URL`
- `GITHUB_APP_CALLBACK_URL`
- `GITHUB_APP_SETUP_URL`

## Permissoes recomendadas

- Repository contents: Read-only
- Metadata: Read-only
- Pull requests: Read-only
- Issues: Read-only
- Commit statuses: Read-only
- Actions: Read-only

## Eventos recomendados

- `push`
- `pull_request`
- `workflow_run`
- `issues`
- `installation`
- `installation_repositories`

## Criterio de aceite

O preflight strict so deve ficar sem bloqueadores quando:

1. App ID estiver no `.env`.
2. OAuth GitHub estiver configurado.
3. Webhook secret do GitHub App estiver configurado.
4. Houver ao menos uma instalacao vinculada a um workspace.
5. Ambiente de producao estiver com `APP_ENV=production`, `APP_DEBUG=false`, Mercado Pago production e dominio HTTPS oficial.

## Diagnostico via terminal

Use o comando abaixo para conferir rapidamente o que falta antes da submissao:

```bash
php artisan devlog:github-app-check
```

Para automacao ou preflight em CI, use:

```bash
php artisan devlog:github-app-check --json
```

O comando retorna falha enquanto houver variaveis obrigatorias pendentes ou nenhuma instalacao vinculada.
