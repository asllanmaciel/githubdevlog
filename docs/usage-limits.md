# Limites de uso por plano

O GitHub DevLog AI usa o campo `monthly_event_limit` do plano para controlar quantos webhooks cada workspace pode receber por mes.

## Como o uso e calculado

A contagem considera eventos em `webhook_events` com `received_at` dentro do mes atual:

```text
inicio do mes <= received_at <= fim do mes
```

A regra fica centralizada em:

```text
app/Support/WorkspaceUsage.php
```

## Onde o limite e aplicado

- Teste manual no dashboard
- Webhook manual de repositorio GitHub
- Webhook do GitHub App

Quando o workspace atinge o limite mensal, novos eventos sao recusados com HTTP `429`.

## Resposta esperada quando o limite e atingido

```json
{
  "error": "Limite mensal de eventos atingido.",
  "usage": 1000,
  "limit": 1000
}
```

## Notificacao ao usuario

Ao atingir o limite, o sistema cria uma notificacao do tipo `billing` no workspace:

```text
Limite mensal de webhooks atingido
```

## Monitoramento admin

Acompanhe em:

```text
/admin/usage-monitor
```

A tela mostra:

- uso global;
- workspaces perto do limite;
- workspaces bloqueados;
- plano atual;
- eventos usados;
- eventos restantes;
- janela mensal.

## Proximo passo natural

Para evoluir o SaaS, podemos adicionar:

- alertas em 80% e 95%;
- upgrade sugerido no dashboard;
- registro historico mensal de uso;
- cobranca por excedente;
- jobs agendados para sumarizar consumo.