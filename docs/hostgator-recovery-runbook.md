# Runbook de retorno da HostGator

Use este roteiro quando o servidor voltar após instabilidade da hospedagem.

## 1. Confirmar acesso ao servidor

```bash
ssh ghdevlog@dedi-15012710.m3marketingdigital.com.br
cd /home/ghdevlog/app
pwd
git branch --show-current
git rev-parse --short HEAD
```

O branch esperado é `master`.

## 2. Atualizar código e banco

```bash
git pull origin master
php artisan migrate --force
php artisan devlog:sync-plans
php artisan devlog:sync-roadmap
php artisan devlog:sync-knowledge-base
```

## 3. Recriar caches

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 4. Validar endpoints públicos

```bash
curl -I https://ghdevlog.com/
curl -I https://ghdevlog.com/login
curl -I https://ghdevlog.com/admin
curl https://ghdevlog.com/health
curl https://ghdevlog.com/readiness
```

Interpretação:

- `/health` deve responder 200 quando aplicação, banco e storage estão vivos.
- `/readiness` pode responder 503 se houver pendência operacional, como GitHub App, e-mail, fila, billing ou webhooks inválidos.

## 5. Verificar logs

```bash
tail -n 160 storage/logs/laravel.log
```

Se houver erro recente, validar a rota afetada antes de divulgar que o serviço voltou.

## 6. Smoke test autenticado

1. Entrar em `/admin`.
2. Abrir `/admin/system-status`.
3. Abrir `/admin/roadmap`.
4. Abrir `/dashboard` com usuário demo ou conta real.
5. Conferir `/admin/billing-events` se Mercado Pago tiver recebido webhook durante a instabilidade.

## 7. Validar integrações críticas

- GitHub webhook manual ou GitHub App: confirmar novo evento no dashboard.
- Mercado Pago: rodar `php artisan devlog:mercado-pago-check`.
- Deploy webhook: se existir webhook de deploy, confirmar logs do endpoint antes de confiar em auto-pull.

## 8. Registrar estado final

```bash
git rev-parse --short HEAD
php artisan devlog:preflight
```

Se o preflight reprovar apenas itens externos conhecidos, registrar no painel e seguir monitorando.
