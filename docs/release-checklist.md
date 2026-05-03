# Checklist de release

Use este checklist antes de divulgar uma mudança ou abrir o produto para novos usuários.

## Código

- [ ] PR revisado ou mudança pequena justificada.
- [ ] `vendor/bin/pint --test` passou.
- [ ] `php artisan test` passou.
- [ ] `php artisan route:cache` passou.
- [ ] `php artisan view:cache` passou.
- [ ] Migrações revisadas para produção.

## Produto

- [ ] Roadmap atualizado quando a entrega altera status.
- [ ] Changelog/docs atualizados quando houver impacto visível.
- [ ] Fluxo de billing/webhook/GitHub validado quando tocado.
- [ ] Mensagens de erro e estados vazios revisados.

## Produção

- [ ] `git rev-parse --short HEAD` confirmado no servidor.
- [ ] `php artisan optimize:clear` executado.
- [ ] `php artisan config:cache`, `route:cache` e `view:cache` executados.
- [ ] `/health` respondeu 200.
- [ ] `/readiness` revisado para pendências operacionais conhecidas.
- [ ] Painel admin principal abre sem 500.
- [ ] Logs verificados após deploy.

## Rollback

- [ ] Commit anterior conhecido.
- [ ] Migrações com impacto irreversível revisadas.
- [ ] Plano de correção/rollback registrado no PR.
