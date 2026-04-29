# Seed demo operacional

O comando `devlog:seed-demo` cria um cenario completo para apresentacao, QA, gravacao de screenshots e submissao ao GitHub Developer Program.

## O que o comando cria

- Usuario demo super admin
- Workspace demo privado
- Plano Growth Demo
- Assinatura ativa de exemplo
- Repositorio GitHub fake de demo
- Eventos GitHub de exemplo
- Nota e tarefa em evento
- Notificacao para usuario
- Ticket de suporte
- Artigos publicados na base de conhecimento
- Item de roadmap concluido

## Uso padrao

```bash
php artisan devlog:seed-demo
```

Credenciais padrao:

```text
Email: demo@devlog.local
Senha: DevlogDemo123!
```

## Personalizar credenciais

```bash
php artisan devlog:seed-demo --email=voce@dominio.com --password="SenhaForte123!"
```

## Recriar a demo do zero

O modo `--fresh` remove apenas dados identificaveis da demo e recria o cenario.

```bash
php artisan devlog:seed-demo --fresh
```

## Quando usar

- Ambiente local novo
- Ambiente de staging
- Antes de capturar screenshots
- Antes de demonstrar o painel para outro dev
- Antes de validar o gate de lancamento

## Cuidado

Nao use `--fresh` apontando para email ou workspace de usuario real. O comando foi desenhado para ser seguro por padrao, mas o modo fresh remove o usuario informado e o workspace `workspace-demo`.