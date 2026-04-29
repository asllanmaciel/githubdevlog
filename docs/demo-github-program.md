# Roteiro de demo para GitHub Developer Program

Este roteiro ajuda a demonstrar o DevLog AI como ferramenta SaaS para receber, validar e investigar webhooks do GitHub.

## Objetivo da demo

Mostrar que um dev consegue:

1. Criar um workspace privado.
2. Conectar um repositorio GitHub.
3. Receber um webhook real.
4. Validar assinatura HMAC.
5. Ver payload sanitizado.
6. Criar nota ou tarefa a partir do evento.
7. Acompanhar uso, plano e suporte.

## Preparacao

- Usar dominio HTTPS definitivo ou tunnel ativo.
- Ter um workspace demo.
- Ter um repositorio GitHub de teste.
- Ter Secret do workspace configurado no webhook do GitHub.
- Ter pelo menos um evento `ping` e um `push`.
- Ter um plano ativo ou trial visivel no dashboard.

## Fluxo sugerido

### 1. Abrir landing

Mostre a proposta:

- Inbox privado para webhooks GitHub.
- Validacao por assinatura.
- Debug e auditoria sem depender de terminal.
- SaaS por workspace.

### 2. Criar conta ou abrir workspace demo

Mostre:

- Login/cadastro.
- Workspace isolado.
- Central da assinatura.
- Launch checklist.

### 3. Configurar webhook GitHub

No dashboard:

- Copie Payload URL.
- Copie Secret.
- Mostre onde usar no GitHub.

No GitHub:

- Settings -> Webhooks -> Add webhook.
- Content type: `application/json`.
- Secret: secret do workspace.
- Evento: `push` ou `ping`.

### 4. Enviar evento

Faça um `ping` ou `push`.

No DevLog AI, mostre:

- Evento recebido.
- Status de assinatura valida.
- Delivery id.
- Repositorio.
- Branch.
- Commits.
- Arquivos tocados.
- Payload sanitizado.

### 5. Operacao

Mostre:

- Notificacoes do workspace.
- Notas internas.
- Tarefas a partir de webhook.
- Suporte.
- Base de conhecimento.

### 6. Admin

Mostre:

- Centro de Lancamento.
- Status do sistema.
- Centro de seguranca.
- Assinaturas.
- Eventos de cobranca.
- Roadmap visual.

## Ponto de venda

O DevLog AI resolve uma dor comum:

> "Recebi ou nao recebi o webhook? A assinatura estava valida? Qual payload chegou? Quem viu isso? Como eu mostro para o time?"

## Checklist antes de apresentar

- `/health` esta OK.
- `/admin/launch-center` com score alto.
- `/admin/system-status` sem falhas criticas.
- `/admin/security-center` sem bloqueadores graves.
- Webhook GitHub real validado.
- Webhook Mercado Pago testado.
- Base de conhecimento publicada.
- Suporte funcionando.

