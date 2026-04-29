# Pagina publica de status

A pagina publica de status resume a saude dos principais componentes sem expor detalhes sensiveis do ambiente.

## URL

```text
/status
```

## Componentes exibidos

- aplicacao web;
- banco de dados;
- webhooks GitHub;
- Mercado Pago;
- filas e jobs;
- suporte.

## Fonte dos dados

A pagina usa os mesmos sinais de `SystemHealth` e `IncidentResponse`, mas mostra apenas informacoes seguras para usuarios e avaliadores externos.

## Uso recomendado

Inclua o link no rodape publico, docs e materiais de lancamento.