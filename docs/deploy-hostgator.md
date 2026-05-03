# Runbook de deploy HostGator

Este runbook descreve o caminho recomendado para publicar o GitHub DevLog AI em ambiente HostGator com MySQL.

## 1. Pre-requisitos

- Dominio apontado para a hospedagem.
- HTTPS ativo no dominio.
- Banco MySQL criado no painel HostGator.
- PHP compativel com Laravel 13 e extensoes necessarias habilitadas.
- Acesso SSH ou fluxo de upload com Composer executado localmente.
- Credenciais Mercado Pago sandbox/producao.
- GitHub App criado com URLs finais.

## 2. Variaveis obrigatorias

Configure o `.env` de producao com:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seudominio.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario_do_banco
DB_PASSWORD=senha_do_banco

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database

MERCADO_PAGO_ENVIRONMENT=production
MERCADO_PAGO_PRODUCTION_ACCESS_TOKEN=
MERCADO_PAGO_PRODUCTION_PUBLIC_KEY=
MERCADO_PAGO_WEBHOOK_SECRET=

GITHUB_APP_ID=
GITHUB_APP_CLIENT_ID=
GITHUB_APP_CLIENT_SECRET=
GITHUB_APP_WEBHOOK_SECRET=
GITHUB_APP_PRIVATE_KEY_PATH=
GITHUB_APP_CALLBACK_URL="${APP_URL}/github/callback"
GITHUB_APP_SETUP_URL=
GITHUB_APP_WEBHOOK_URL="${APP_URL}/webhooks/github-app"
```

## 3. Build local antes do upload

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 4. Upload

Envie para o servidor:

- `app`
- `bootstrap`
- `config`
- `database`
- `public`
- `resources`
- `routes`
- `storage`
- `vendor`
- `composer.json`
- `composer.lock`
- `artisan`
- `.env` de producao

Nao envie:

- `.git`
- `.env` local
- logs locais
- arquivos de backup
- `node_modules`

## 5. Public root

O document root do dominio deve apontar para a pasta `public`.

Se a HostGator nao permitir alterar o document root, use um subdiretorio seguro e redirecione para `public/index.php`, evitando expor `.env`, `vendor` e `storage`.

## 6. Pos-deploy

Execute no servidor:

```bash
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 7. Filas e agendador

Se usar jobs:

```bash
php artisan queue:work --tries=3
```

Se houver cron disponivel, configure para rodar scheduler, alertas de uso e futuras automacoes:

```bash
* * * * * cd /caminho/do/projeto && php artisan schedule:run >> /dev/null 2>&1
```

## 8. Webhooks finais

Configure no Mercado Pago:

```text
https://seudominio.com/webhooks/mercado-pago
```

Configure no GitHub App:

```text
https://seudominio.com/webhooks/github-app
https://seudominio.com/github/callback
```

Para webhooks manuais de repositorio:

```text
https://seudominio.com/webhooks/github/{workspace_uuid}
```

## 8.1 Webhook de deploy GitHub

Para atualizar a HostGator automaticamente após push na branch `master`, configure no `.env` de produção:

```env
DEPLOY_WEBHOOK_SECRET=gere_um_segredo_forte
DEPLOY_WEBHOOK_BRANCH=master
DEPLOY_WEBHOOK_PATH=/home/ghdevlog/app
DEPLOY_WEBHOOK_PHP_BINARY=php
DEPLOY_WEBHOOK_TIMEOUT=180
```

No GitHub, abra `Settings > Webhooks > Add webhook`:

```text
Payload URL: https://ghdevlog.com/webhooks/deploy/github
Content type: application/json
Secret: mesmo valor de DEPLOY_WEBHOOK_SECRET
Events: Just the push event
```

O endpoint valida `X-Hub-Signature-256`, ignora branches diferentes da configurada e executa apenas o fluxo fixo:

```bash
git pull --ff-only origin master
php artisan migrate --force
php artisan devlog:sync-plans
php artisan devlog:sync-roadmap
php artisan devlog:sync-knowledge-base
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Os logs ficam em:

```text
storage/logs/deploy-webhook.log
```

## 9. Teste de fumaca

Depois do deploy:

- Rodar `php artisan devlog:preflight` para diagnostico.
- Rodar `php artisan devlog:preflight --strict` antes de liberar o dominio para usuarios.
- Rodar `php artisan devlog:mercado-pago-check` antes de ativar cobranca real.
- Abrir `/health` para vida básica da aplicação.
- Abrir `/readiness` para prontidão operacional completa.
- Criar login.
- Acessar `/dashboard`.
- Criar workspace.
- Enviar webhook GitHub ping.
- Simular webhook Mercado Pago.
- Verificar `/admin/launch-center`.
- Confirmar que `APP_DEBUG=false`.

Para automacao, use:

```bash
php artisan devlog:preflight --json
php artisan devlog:preflight --strict --json
```

O modo `--strict` reprova quando existir qualquer bloqueador obrigatorio, mesmo que a pontuacao geral esteja acima do minimo. Use esse modo para deploy oficial, submissao ao GitHub Developer Program e validacao antes de campanhas.

## 10. Rollback

- Manter backup do banco antes de migrations.
- Manter pacote anterior publicado.
- Se falhar, restaurar arquivos anteriores e backup do banco.
- Rodar `php artisan optimize:clear`.
## 11. Demo e staging

Para montar um cenario de apresentacao em ambiente local ou staging, rode:

```bash
php artisan devlog:seed-demo
```

Use `php artisan devlog:seed-demo --fresh` apenas quando quiser recriar a demo do zero. Evite rodar o modo fresh em producao com usuarios reais.
O scheduler executa, entre outras rotinas:

```bash
php artisan devlog:check-usage-limits
```

Esse comando cria alertas preventivos de uso em 80%, 95% e 100% do limite mensal do plano.
O scheduler tambem gera snapshots mensais de uso:

```bash
php artisan devlog:snapshot-usage
```

Esses snapshots alimentam auditoria financeira e historico de consumo por workspace.
