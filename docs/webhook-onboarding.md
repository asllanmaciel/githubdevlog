# Onboarding de Webhook

Este guia mostra como conectar um repositorio GitHub ao GitHub DevLog AI e validar se os eventos estao chegando no workspace correto.

## 1. Copie os dados do workspace

No dashboard do workspace, use:

- Payload URL: endpoint exclusivo do workspace.
- Content type: `application/json`.
- Secret: segredo privado usado para validar `X-Hub-Signature-256`.

Cada workspace tem endpoint e secret proprios. Um usuario nao ve eventos de outro workspace.

## 2. Configure o webhook no GitHub

No repositorio GitHub:

1. Acesse `Settings > Webhooks > Add webhook`.
2. Cole o Payload URL do dashboard.
3. Selecione `application/json`.
4. Cole o Secret do workspace.
5. Selecione os eventos recomendados: `push`, `pull_request`, `workflow_run` e `issues`.
6. Salve o webhook.

## 3. Valide o ping inicial

Ao salvar, o GitHub envia um evento `ping`. Se tudo estiver correto, ele aparece no historico privado do dashboard com:

- tipo do evento;
- repositorio de origem;
- delivery id;
- horario de recebimento;
- status de assinatura.

## 4. Gere um evento real

Faca um push no repositorio ou abra uma pull request. O evento deve aparecer no dashboard em poucos segundos.

## 5. Teste localmente com assinatura

O dashboard mostra um comando `curl` pronto para simular um evento com `X-Hub-Signature-256`. Use esse teste para validar o endpoint antes de depender de um evento real do GitHub.

## 6. Diagnostico rapido

Se o evento nao aparecer:

- confirme se o tunnel ou dominio publico esta ativo;
- confira se a URL configurada no GitHub e igual ao Payload URL do workspace;
- verifique se o Secret do GitHub e igual ao Secret do workspace;
- veja o status da entrega em `Recent Deliveries` no GitHub;
- confira se o plano do workspace ainda permite receber eventos.
