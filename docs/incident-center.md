# Centro de incidentes

O Centro de Incidentes resume os sinais operacionais que podem afetar usuarios durante beta, demo ou lancamento.

## Admin

```text
/admin/incident-center
```

## CLI

```bash
php artisan devlog:incident-check
php artisan devlog:incident-check --json
```

## Sinais monitorados

- jobs falhos;
- jobs pendentes;
- chamados abertos;
- webhooks com assinatura invalida nas ultimas 24h;
- eventos Mercado Pago pendentes, ignorados ou sem associacao;
- acoes sensiveis recentes na auditoria.

## Rotina recomendada

Antes de demo ou deploy:

1. Rode `php artisan devlog:incident-check`.
2. Abra `/admin/incident-center`.
3. Resolva filas e billing antes de testar fluxos publicos.
4. Confira chamados abertos.
5. So avance para `devlog:preflight --strict` quando o centro estiver saudavel.