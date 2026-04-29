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

- alertas em 80% e 95% implementados via `devlog:check-usage-limits`;
- upgrade sugerido no dashboard;
- registro historico mensal de uso;
- cobranca por excedente;
- jobs agendados para sumarizar consumo.
## Alertas preventivos

O comando abaixo verifica todos os workspaces e cria notificacoes preventivas quando o uso chega a 80%, 95% ou 100% do limite mensal:

```bash
php artisan devlog:check-usage-limits
```

Para automacao:

```bash
php artisan devlog:check-usage-limits --json
```

O agendamento padrao roda de hora em hora via `routes/console.php`:

```php
Schedule::command('devlog:check-usage-limits')->hourly();
```

Para que isso funcione em producao, o cron do servidor precisa executar o Laravel Scheduler:

```bash
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```

Tipos de notificacao criados:

- `usage_limit_80`
- `usage_limit_95`
- `usage_limit_reached`

As notificacoes sao deduplicadas por workspace, tipo e mes, evitando spam para o usuario.