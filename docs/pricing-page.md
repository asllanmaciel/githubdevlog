# Pagina publica de planos

A pagina `/pricing` apresenta o modelo comercial do GitHub DevLog AI antes do cadastro.

## Objetivo

- Explicar que o SaaS cobra por uso.
- Mostrar limites mensais de eventos.
- Mostrar retencao por plano.
- Dar contexto para devs escolherem um perfil.
- Apoiar go-live, demos e submissao ao GitHub Developer Program.

## Origem dos dados

Os planos ativos vem da tabela `billing_plans`.

Campos usados:

- `name`
- `slug`
- `price_cents`
- `monthly_event_limit`
- `event_retention_days`
- `overage_price_cents`
- `features`

## Antes do live

- Revisar precos finais.
- Definir se havera plano gratis.
- Definir regra de trial.
- Definir politica de excedente.
- Conectar CTA de plano pago diretamente ao checkout quando fizer sentido.
