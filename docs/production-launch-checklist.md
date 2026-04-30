# Checklist de produção

Este checklist consolida o que ainda separa o beta/local do lançamento oficial.

## Comando de prontidão

```bash
php artisan devlog:go-live-check
```

Para saída em JSON:

```bash
php artisan devlog:go-live-check --json
```

O comando retorna exit code `0` quando não há bloqueadores críticos e `1` quando ainda existem pendências.

## Bloqueadores externos atuais

- Domínio oficial com HTTPS.
- GitHub App oficial configurado.
- Mercado Pago em produção.
- E-mail transacional real.

## Variáveis de ambiente para revisar

- `APP_URL`
- `MAIL_MAILER`
- `MAIL_FROM_ADDRESS`
- `GITHUB_APP_ID`
- `GITHUB_APP_PRIVATE_KEY`
- `GITHUB_APP_WEBHOOK_SECRET`
- `GITHUB_APP_CLIENT_ID`
- `GITHUB_APP_CLIENT_SECRET`
- `GITHUB_APP_SETUP_URL`
- `MERCADO_PAGO_ENVIRONMENT`
- `MERCADO_PAGO_ACCESS_TOKEN`
- `MERCADO_PAGO_PUBLIC_KEY`
- `MERCADO_PAGO_WEBHOOK_SECRET`
- `QUEUE_CONNECTION`
- `DEVLOG_SUPPORT_EMAIL`

## Regra prática

Enquanto o comando de go-live listar bloqueadores, o produto pode seguir em beta controlado, demo privada ou validação com usuários convidados.

Quando o comando retornar `0`, o projeto fica apto para preparar release público, submissão final do GitHub App e ativação comercial.
