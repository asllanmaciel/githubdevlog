# Mercado Pago em producao

Este guia fecha o bloco de billing antes de divulgar o produto para publico externo.

## Variaveis

```env
MERCADO_PAGO_ENVIRONMENT=production
MERCADO_PAGO_PRODUCTION_ACCESS_TOKEN=
MERCADO_PAGO_PRODUCTION_PUBLIC_KEY=
MERCADO_PAGO_WEBHOOK_SECRET=
MERCADO_PAGO_WEBHOOK_TOLERANCE_SECONDS=900
```

Use credenciais de producao da conta Mercado Pago. Nao reutilize tokens de sandbox.

## URLs

Configure no painel do Mercado Pago:

```text
Webhook URL: https://ghdevlog.com/webhooks/mercado-pago
Return URL: https://ghdevlog.com/billing/return
Pagina de precos: https://ghdevlog.com/pricing
```

O endpoint `GET /webhooks/mercado-pago` existe apenas para teste de disponibilidade. O Mercado Pago deve enviar notificacoes via `POST /webhooks/mercado-pago`.

## Validacao

1. Abrir `/admin/mercado-pago-readiness`.
2. Confirmar ambiente `production`, token, public key e webhook secret.
3. Revisar planos ativos em `/admin/billing-plans`.
4. Fazer um checkout real de baixo valor.
5. Confirmar o evento em `/admin/billing-events`.
6. Verificar se `signature_valid` ficou verdadeiro.
7. Conferir se a assinatura do workspace foi atualizada.

Tambem e possivel diagnosticar pelo terminal:

```bash
php artisan devlog:mercado-pago-check
php artisan devlog:mercado-pago-check --json
```
