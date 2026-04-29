# Checklist de ambiente de producao

Use este checklist antes de publicar o GitHub DevLog AI em dominio oficial.

## Comando de diagnostico

```bash
php artisan devlog:production-check
```

Para automacao:

```bash
php artisan devlog:production-check --json
```

## Arquivo base

Use `.env.production.example` como referencia segura. Ele nao contem credenciais reais.

## Obrigatorio para lancamento

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` com HTTPS final
- `APP_KEY` gerada no servidor
- MySQL configurado
- sessoes em `database`
- `SESSION_SECURE_COOKIE=true`
- Mercado Pago em `production`
- access token, public key e webhook secret do Mercado Pago
- GitHub App ID, OAuth client, webhook secret e private key path
- URLs finais do GitHub App usando dominio HTTPS oficial

## Fluxo recomendado no servidor

1. Envie o codigo para o servidor.
2. Crie o `.env` a partir de `.env.production.example`.
3. Preencha credenciais reais no servidor, nunca no repositorio.
4. Rode migrations.
5. Rode `php artisan optimize:clear`.
6. Rode `php artisan devlog:production-check`.
7. Rode `php artisan devlog:preflight --strict`.
8. Configure cron/queue conforme o ambiente da hospedagem permitir.

## Observacao sobre HostGator

Como a HostGator costuma oferecer MySQL compartilhado, mantenha `DB_CONNECTION=mysql` e confira host, usuario, senha e nome do banco exatamente como aparecem no painel da hospedagem.