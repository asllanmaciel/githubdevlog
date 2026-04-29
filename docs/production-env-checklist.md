# Checklist de ambiente de producao

Use este checklist antes de publicar o GitHub DevLog AI em dominio definitivo.

## Objetivo

Garantir que o ambiente de producao esteja pronto para receber usuarios, processar assinaturas, validar webhooks do GitHub e receber notificacoes do Mercado Pago sem expor credenciais locais.

## Arquivo base

Copie `.env.production.example` para `.env` no servidor e preencha apenas no ambiente de producao.

Nunca envie para o servidor:

- `.env` local
- tokens de sandbox usados em teste pessoal, se nao forem necessarios
- backups com credenciais
- logs com payloads sensiveis

## Aplicacao

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://seudominio.com`
- `APP_KEY` gerada com `php artisan key:generate --show` ou configurada pelo deploy

## Banco e sessoes

- `DB_CONNECTION=mysql`
- Banco MySQL criado no servidor
- Usuario MySQL com permissao apenas no banco do projeto
- `SESSION_DRIVER=database`
- `SESSION_SECURE_COOKIE=true` quando estiver em HTTPS

## Mercado Pago

No painel Mercado Pago, configure:

- Credenciais de producao no `.env`
- Webhook URL: `https://seudominio.com/webhooks/mercado-pago`
- Secret do webhook em `MERCADO_PAGO_WEBHOOK_SECRET`
- Evento de teste deve retornar `200 OK`

## GitHub App

No GitHub Developer Settings, configure:

- Callback URL: `https://seudominio.com/github/callback`
- Webhook URL: `https://seudominio.com/webhooks/github-app`
- Webhook secret igual ao `GITHUB_APP_WEBHOOK_SECRET`
- Private key salva fora da pasta publica, preferencialmente em `storage/app/private/github-app.pem`
- Setup URL apontando para a instalacao do app

## Validacao final

Execute:

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan devlog:preflight --strict
```

No painel admin, conferir:

- `/admin/production-environment`
- `/admin/launch-gate`
- `/admin/system-status`
- `/admin/security-center`
- `/admin/github-submission`

## Criterio de pronto

O ambiente pode ser considerado pronto para release quando:

- Gate strict aprovado
- Ambiente de producao sem pendencias obrigatorias
- Mercado Pago webhook com `200 OK`
- GitHub App instalado em pelo menos um repositorio real de teste
- Login, cadastro, dashboard e billing testados em HTTPS