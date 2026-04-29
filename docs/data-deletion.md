# Exclusao de dados do workspace

Este fluxo atende pedidos de exclusao, encerramento de workspace ou remocao operacional solicitada pelo suporte.

## Fluxo recomendado

1. Confirmar solicitante e permissao administrativa do workspace.
2. Exportar uma copia, se houver base legal ou necessidade de suporte.
3. Simular exclusao com `--dry-run`.
4. Registrar aprovacao interna.
5. Executar exclusao real com `--force`.

## Exportar antes

```bash
php artisan devlog:export-workspace-data {workspace}
```

## Simular exclusao

```bash
php artisan devlog:purge-workspace-data {workspace} --dry-run
```

## Excluir de verdade

```bash
php artisan devlog:purge-workspace-data {workspace} --force
```

## Saida JSON

```bash
php artisan devlog:purge-workspace-data {workspace} --dry-run --json
```

## Dados removidos

- membros do workspace;
- repositorios;
- instalacoes GitHub App;
- rotacoes de secret;
- assinaturas;
- eventos webhook;
- notas e tarefas dos eventos;
- snapshots de uso;
- faturas internas de uso.

## Protecao contra acidente

Sem `--force`, o comando sempre roda em modo simulacao.