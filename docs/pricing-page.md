# Planos e uso interno

A rota `/pricing` pode existir como apoio, mas a estrategia atual e nao dar destaque publico para precos antes de validar melhor o uso real.

## Objetivo

- Manter a estrutura de planos preparada.
- Priorizar valor na landing publica.
- Explicar consumo, limites e upgrade dentro do painel autenticado.
- Apoiar demos controladas e validacao de modelo comercial.

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

- Revisar precos finais antes de expor publicamente.
- Definir se havera plano gratis.
- Definir regra de trial.
- Definir politica de excedente.
- Fortalecer o bloco de uso/plano no dashboard.
- Decidir depois se pricing publico sera aberto ou se ficara como "fale conosco".
