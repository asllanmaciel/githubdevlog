# Pacote de submissao - GitHub Developer Program

Este documento centraliza a narrativa e os materiais para apresentar o GitHub DevLog AI ao GitHub Developer Program.

## Nome do produto

GitHub DevLog AI

## Categoria

Developer tool / Webhook debugging / SaaS para integracoes GitHub.

## Proposta curta

O GitHub DevLog AI e um inbox privado para webhooks do GitHub. Ele recebe eventos, valida assinatura HMAC, organiza payloads em workspaces isolados e ajuda devs a depurar integracoes sem depender de logs locais ou prints de terminal.

## Descricao completa

O GitHub DevLog AI ajuda desenvolvedores e times a entenderem com clareza o que acontece quando o GitHub envia um webhook. Em vez de procurar logs no terminal, expor endpoints improvisados ou misturar payloads de varios projetos, o dev cria um workspace privado, configura um endpoint no GitHub e acompanha cada evento em um painel com assinatura validada, delivery id, repositorio, branch, commits, arquivos alterados e payload sanitizado.

O produto tambem adiciona uma camada operacional: notificacoes, notas, tarefas, suporte, base de conhecimento, billing por uso e painel admin para auditar webhooks, assinaturas, seguranca, saude do sistema e readiness de lancamento.

## Dor resolvida

- "O GitHub chamou meu endpoint?"
- "Qual payload chegou?"
- "A assinatura estava valida?"
- "Qual repositorio/branch gerou o evento?"
- "Como compartilho esse contexto com o time?"
- "Como debugo webhooks sem expor segredos?"

## Publico-alvo

- Desenvolvedores que criam integracoes com GitHub.
- Times que usam webhooks para automacao interna.
- SaaS que precisa auditar eventos GitHub.
- Consultorias e dev shops que precisam demonstrar integracoes.
- Criadores de GitHub Apps que precisam depurar eventos reais.

## Principais funcionalidades

- Workspaces privados por usuario/time.
- Endpoint unico por workspace.
- Secret por workspace.
- Validacao `X-Hub-Signature-256`.
- Captura de eventos GitHub.
- Payload sanitizado.
- Historico privado de eventos.
- Notas e tarefas por webhook.
- GitHub App install/callback/webhook scaffold.
- Billing via Mercado Pago.
- Auditoria de eventos de cobranca.
- Rotacao de secrets com historico.
- Centro de lancamento.
- Centro de seguranca.
- Status operacional.
- Base de conhecimento.
- Centro de demo.

## Como integra com GitHub

### Webhook manual

O usuario configura no repositorio:

```text
Payload URL: https://app.devlog.ai/webhooks/github/{workspace_uuid}
Content type: application/json
Secret: secret gerado no workspace
```

O DevLog AI valida:

```text
X-Hub-Signature-256
```

### GitHub App

O produto tambem possui fluxo de GitHub App:

- `/github/install`
- `/github/callback`
- `/webhooks/github-app`

O app usa `installation_id` para vincular eventos ao workspace correto.

## Seguranca

- Workspaces isolados.
- Secrets por workspace.
- Rotacao auditada de secrets.
- Validacao HMAC de webhooks GitHub.
- Validacao HMAC de webhooks Mercado Pago.
- Payload sanitizado antes de armazenamento.
- Headers de seguranca globais.
- CSRF ativo no app web.
- Webhooks fora de CSRF por necessidade tecnica, protegidos por assinatura.
- Centro admin de postura de seguranca.

## Modelo SaaS

Planos por quantidade de eventos, retencao e recursos de colaboracao.

Exemplo:

- Free: limite baixo para teste.
- Pro: mais eventos e maior retencao.
- Team: uso colaborativo e suporte prioritario.

## URLs importantes

Substituir pelas URLs finais de producao:

- Landing: `https://app.devlog.ai`
- Docs usuarios: `https://app.devlog.ai/docs/usuarios`
- Privacidade: `https://app.devlog.ai/privacy`
- Termos: `https://app.devlog.ai/terms`
- Webhook GitHub App: `https://app.devlog.ai/webhooks/github-app`
- Callback GitHub App: `https://app.devlog.ai/github/callback`

## Demo sugerida

1. Abrir landing.
2. Criar ou abrir workspace demo.
3. Mostrar launch checklist.
4. Copiar Payload URL e Secret.
5. Configurar webhook em repositorio GitHub.
6. Enviar ping/push.
7. Mostrar evento recebido com assinatura valida.
8. Criar nota e tarefa.
9. Abrir admin: launch center, status, seguranca e billing.

## Checklist antes de submeter

- Dominio final com HTTPS.
- GitHub App real criado.
- App ID, Client ID, Client Secret e Webhook Secret configurados.
- Private key configurada.
- Politica de privacidade publicada.
- Termos publicados.
- Demo funcional.
- Screenshots do dashboard e admin.
- Webhook real validado.
- Mercado Pago em producao ou sandbox demonstravel.

