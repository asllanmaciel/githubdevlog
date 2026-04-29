# Bloqueadores atuais de lancamento

Este documento resume o que impede o `php artisan devlog:preflight --strict` de aprovar o projeto para lancamento oficial.

## O que ja foi resolvido localmente

- Cenario demo criado com `php artisan devlog:seed-demo`.
- Assinatura demo ativa criada para remover o bloqueador local de assinaturas rastreadas.
- Webhooks GitHub manuais ja recebem eventos validos.
- Mercado Pago webhook ja respondeu `200 OK` no teste sandbox.

## Bloqueadores que dependem de credenciais reais

Preencher no `.env` do ambiente final:

```env
GITHUB_APP_ID=
GITHUB_APP_CLIENT_ID=
GITHUB_APP_CLIENT_SECRET=
GITHUB_APP_WEBHOOK_SECRET=
GITHUB_APP_PRIVATE_KEY_PATH=
GITHUB_APP_CALLBACK_URL="${APP_URL}/github/callback"
GITHUB_APP_SETUP_URL=
GITHUB_APP_WEBHOOK_URL="${APP_URL}/webhooks/github-app"
```

## Bloqueadores que dependem de ambiente final

Em producao:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com
MERCADO_PAGO_ENVIRONMENT=production
```

## Bloqueador de instalacao GitHub App

Depois de criar o GitHub App real:

1. Abrir `/dashboard`.
2. Clicar em conectar GitHub App.
3. Instalar em um repositorio real de teste.
4. Confirmar retorno em `/github/callback`.
5. Verificar `/admin/github-readiness` e `/admin/launch-gate`.

## Onde acompanhar no admin

```text
/admin/launch-blockers
/admin/launch-gate
/admin/production-environment
/admin/github-readiness
```

## Comando final

```bash
php artisan devlog:preflight --strict
```