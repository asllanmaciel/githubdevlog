# Roteiro de demo para GitHub Developer Program

Este roteiro demonstra o DevLog AI como uma ferramenta SaaS para receber, validar, investigar e resumir webhooks do GitHub.

## Objetivo da demo

Mostrar que um dev consegue:

1. Criar um workspace privado.
2. Conectar um repositório GitHub.
3. Receber um webhook real.
4. Validar assinatura HMAC.
5. Ver payload sanitizado.
6. Gerar uma análise AI do evento.
7. Criar nota ou tarefa a partir do webhook.
8. Acompanhar uso, plano, suporte e readiness.

## Preparação

- Usar domínio HTTPS definitivo ou tunnel ativo.
- Ter um workspace demo.
- Ter um repositório GitHub de teste.
- Ter Secret do workspace configurado no webhook do GitHub.
- Ter pelo menos um evento `ping` e um `push`.
- Ter um plano ativo ou trial visível no dashboard.
- Ter `/admin/demo-center` e `/admin/launch-gate` revisados antes da apresentação.

## Fluxo sugerido em 7 minutos

### 1. Pitch em uma frase

> Debug e auditoria de webhooks GitHub sem expor payload, sem depender de terminal e sem misturar clientes.

### 2. Landing e confiança

Mostre:

- Proposta GitHub-first.
- Privacidade por workspace.
- Segurança por assinatura.
- Página de status.
- Contato e suporte.

### 3. Workspace demo

Mostre:

- Login/cadastro.
- Workspace isolado.
- Checklist de ativação.
- Plano e consumo.
- Payload URL e Secret.

### 4. Configurar webhook GitHub

No dashboard:

- Copie Payload URL.
- Copie Secret.
- Mostre onde usar no GitHub.

No GitHub:

- Settings -> Webhooks -> Add webhook.
- Content type: `application/json`.
- Secret: secret do workspace.
- Evento: `push` ou `ping`.

### 5. Enviar evento

Faça um `ping` ou `push`.

No DevLog AI, mostre:

- Evento recebido.
- Status de assinatura válida.
- Delivery id.
- Repositório.
- Branch.
- Commits.
- Arquivos tocados.
- Payload sanitizado.

### 6. AI e operação

Mostre:

- Análise AI grátis.
- Análise AI avançada quando houver plano/chave.
- Nível de risco.
- Próximos passos.
- Notas internas.
- Tarefas a partir de webhook.

### 7. Admin SaaS

Mostre:

- Centro de demo.
- Gate de lançamento.
- Roadmap visual.
- Status do sistema.
- Centro de segurança.
- Assinaturas e billing.
- Suporte e base de conhecimento.

## Checklist antes de apresentar

- `/health` está OK.
- `/admin/demo-center` acima de 80%.
- `/admin/launch-gate` mostra pendências externas com clareza.
- `/admin/system-status` sem falhas críticas.
- `/admin/security-center` sem bloqueadores graves.
- Webhook GitHub real validado.
- Webhook Mercado Pago testado em sandbox ou produção.
- Base de conhecimento publicada.
- Suporte funcionando.