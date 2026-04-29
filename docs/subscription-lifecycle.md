# Ciclo de vida de assinatura

O GitHub DevLog AI agora possui um fluxo operacional para cancelamento de assinatura por workspace.

## O que acontece ao cancelar

- A assinatura muda para `canceled`.
- `canceled_at` registra data e hora.
- `cancel_reason` guarda o motivo informado.
- `lifecycle_metadata` registra usuario/e-mail que solicitou.
- Uma notificacao de billing e criada no workspace.
- Um evento de auditoria `billing.subscription.canceled` e registrado.

## Observacao Mercado Pago

O cancelamento interno marca o estado do workspace no SaaS. Antes do lancamento oficial, devemos decidir se o plano sera assinatura recorrente gerenciada pelo Mercado Pago ou checkout manual mensal. Se houver recorrencia externa ativa, o operador deve revisar a assinatura tambem no painel Mercado Pago.

## Valor para lancamento

Usuarios precisam ter saida clara. Mesmo antes de automacao completa de recorrencia, o sistema ja registra cancelamento, motivo, auditoria e orientacao operacional.