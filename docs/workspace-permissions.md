# Permissoes por papel no workspace

O produto agora possui uma matriz de permissao aplicada nas rotas sensiveis e refletida no dashboard.

## Papeis

- Owner: controla workspace, membros, billing, secrets, GitHub App, testes, notas e tarefas.
- Admin: opera o workspace com quase todos os poderes do owner, exceto protecoes especificas como remover owner.
- Developer: investiga eventos, cria testes, notas e tarefas, mas nao altera billing, secret ou equipe.
- Viewer: acesso de leitura para acompanhar eventos e abrir suporte, sem acoes sensiveis.

## Acoes protegidas

- Convidar/remover membros: owner/admin.
- Rotacionar secret: owner/admin.
- Alterar plano ou cancelar assinatura: owner/admin.
- Conectar GitHub App: owner/admin.
- Criar evento de teste: owner/admin/developer.
- Criar notas e tarefas: owner/admin/developer.

## Valor para lancamento

Times precisam colaborar sem compartilhar credenciais e sem dar poderes excessivos a todos. Essa matriz reduz risco operacional e ajuda a vender o produto como SaaS multiusuario real.