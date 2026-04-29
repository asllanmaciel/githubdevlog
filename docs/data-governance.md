# Governanca e exportacao de dados

O GitHub DevLog AI precisa oferecer rastreabilidade e portabilidade dos dados de cada workspace.

## Exportar workspace

```bash
php artisan devlog:export-workspace-data {workspace}
```

O parametro aceita:

- ID do workspace;
- UUID;
- slug.

## Saida JSON para automacao

```bash
php artisan devlog:export-workspace-data {workspace} --json
```

## Caminho customizado

```bash
php artisan devlog:export-workspace-data {workspace} --output=exports/workspace-demo.json
```

## Conteudo exportado

- metadados da exportacao;
- dados do workspace;
- membros e papeis;
- assinatura/plano;
- repositorios;
- instalacoes GitHub App;
- ate 1000 webhooks recentes com payload sanitizado;
- notas;
- tarefas;
- snapshots de uso;
- faturas internas de excedente.

## Cuidados

- O pacote pode conter contexto sensivel do repositorio mesmo com sanitizacao.
- Entregue exportacoes apenas para administradores autorizados do workspace.
- Remova exportacoes antigas do storage depois do atendimento.