# Processo GitHub

Este projeto usa `master` como branch de produção. Mudanças devem passar por PR quando não forem hotfix operacional.

## Branches

- `master`: branch principal e fonte do deploy.
- `codex/<descricao>`: trabalho assistido por Codex.
- `fix/<descricao>`: correções pequenas.
- `feat/<descricao>`: funcionalidades.

## Commits

Use Conventional Commits em português do Brasil:

- `feat: adiciona painel de metricas`
- `fix: corrige retorno do Mercado Pago`
- `docs: documenta processo de deploy`
- `chore: ajusta pipeline de CI`

## Pull requests

Todo PR deve informar:

- resumo do valor entregue;
- testes executados;
- risco principal;
- plano de validação em produção;
- rollback possível.

## Gate mínimo de qualidade

Antes de mergear em `master`, validar:

```bash
php artisan test
php artisan route:cache
php artisan view:cache
```

Para arquivos PHP novos ou alterados, rode Pint no escopo da mudança:

```bash
vendor/bin/pint app/Support/MeuArquivo.php tests/Feature/MeuTeste.php
```

O baseline geral de Pint ainda precisa ser normalizado em uma entrega separada antes de virar bloqueio obrigatório no CI.

Quando o ambiente local usa cache em banco e o MySQL não está ativo, use:

```bash
$env:CACHE_STORE='array'; php artisan optimize:clear
```

## Deploy atual

O servidor acompanha `master`. Quando o webhook não puxar automaticamente, rode:

```bash
cd /home/ghdevlog/app
git pull origin master
php artisan migrate --force
php artisan devlog:sync-plans
php artisan devlog:sync-roadmap
php artisan devlog:sync-knowledge-base
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
