# Trilha de auditoria

A trilha de auditoria registra acoes sensiveis do sistema para suporte, compliance e investigacao operacional.

## Acoes registradas

- cadastro de usuario e workspace;
- rotacao de secret do workspace;
- evento manual de teste;
- inicio de checkout;
- instalacao do GitHub App;
- notas e tarefas em webhooks;
- abertura de chamado;
- exportacao de dados;
- exclusao de dados;
- webhooks de billing processados.

## Admin

Acesse:

```text
/admin/audit-trail
```

## Seguranca

Metadados com chaves contendo `secret` ou `token` sao mascarados automaticamente.

## Uso em suporte

Antes de responder incidentes, confira:

1. quem executou a acao;
2. workspace afetado;
3. alvo da acao;
4. horario;
5. metadados do evento.