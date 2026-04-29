# Retencao de webhooks

O GitHub DevLog AI armazena payloads sanitizados para debugging, auditoria e colaboracao. Mesmo sanitizados, payloads podem conter contexto sensivel do repositorio, por isso cada plano define uma janela de retencao.

## Fonte da regra

A regra usa `billing_plans.event_retention_days`. Quando um workspace nao possui plano definido, o fallback operacional e 30 dias.

## Diagnostico no admin

Acesse:

```text
/admin/webhook-retention
```

A tela mostra:

- total de eventos armazenados;
- eventos fora da janela de retencao;
- retencao por plano;
- corte por workspace;
- comandos para simulacao e limpeza.

## Comandos

Simular limpeza:

```bash
php artisan devlog:prune-webhook-events --dry-run
```

Executar limpeza:

```bash
php artisan devlog:prune-webhook-events
```

Saida JSON:

```bash
php artisan devlog:prune-webhook-events --json
```

## Agendamento

A limpeza roda diariamente as 02:30 pelo scheduler Laravel.

## Cuidado operacional

Antes de reduzir a retencao de um plano em producao, avise usuarios afetados e execute `--dry-run` para estimar o impacto.