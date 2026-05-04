# Pacote GitHub - Developer Program, App e Marketplace

Este documento centraliza a narrativa e os materiais para apresentar o GitHub DevLog AI em contextos do ecossistema GitHub.

## Escopo correto

Ha tres trilhas diferentes:

- GitHub Developer Program: adesao da conta/dev ao programa. Requer uma integracao GitHub em producao ou desenvolvimento e um email de suporte para usuarios GitHub. Status atual: concluido.
- GitHub App publico: pagina publica de instalacao/configuracao do app em `https://github.com/apps/gh-devlog`. Status atual: existente.
- GitHub Marketplace: adequado para publicar uma listagem publica do app. Exige descricao, contato, politica de privacidade, suporte, plano de preco, imagens e integracao alem de autenticacao. Apps pagos tambem exigem publisher verificado, billing via Marketplace e maturidade adicional.

Recomendacao atual: manter evidencias reais do GitHub App em `ghdevlog.com`, fortalecer a pagina publica `/github` e preparar a listagem Marketplace como proximo marco separado, inicialmente gratuita, ate haver volume suficiente para uma oferta paga.

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
Payload URL: https://ghdevlog.com/webhooks/github/{workspace_uuid}
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

- Landing: `https://ghdevlog.com`
- Pagina publica da integracao GitHub: `https://ghdevlog.com/github`
- GitHub App publico: `https://github.com/apps/gh-devlog`
- Docs usuarios: `https://ghdevlog.com/docs/usuarios`
- Privacidade: `https://ghdevlog.com/privacy`
- Termos: `https://ghdevlog.com/terms`
- Status/saude: `https://ghdevlog.com/health`
- Webhook GitHub App: `https://ghdevlog.com/webhooks/github-app`
- Callback GitHub App: `https://ghdevlog.com/github/callback`
- Instalacao GitHub App: `https://ghdevlog.com/github/install`

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

## Checklist de maturidade

- Dominio final com HTTPS em `ghdevlog.com`.
- Conta inscrita no GitHub Developer Program.
- GitHub App real criado e instalado em uma conta/repositorio real.
- App ID, Client ID, Client Secret e Webhook Secret configurados.
- Private key configurada fora da pasta publica.
- Politica de privacidade publicada.
- Termos publicados.
- Email ou link de suporte publicado e testado.
- Demo funcional com evento `push` e `workflow_run` reais.
- Screenshots do dashboard, detalhe de evento e admin.
- Webhook real validado por `X-Hub-Signature-256`.
- Evidencia de hardening: aceitos, rejeitados, metodo de validacao e delivery ids.
- Segredos removidos/mascarados de todos os prints e videos.
- Para Marketplace pago: confirmar requisitos de publisher verificado, billing Marketplace e volume minimo antes de solicitar publicacao paga.


## Evidencias e screenshots

Ver tambem: docs/github-submission-evidence.md.

## Resumo executivo

O GitHub DevLog AI e uma ferramenta de debugging e auditoria de webhooks GitHub. O produto ja possui GitHub App instalado em ambiente real, endpoint assinado em `https://ghdevlog.com/webhooks/github-app`, validacao por `X-Hub-Signature-256`, isolamento por workspace, payload sanitizado, historico de delivery ids, notas, tarefas e painel admin de hardening. Em 2026-05-04, o ambiente demonstrou 35 eventos aceitos, 0 rejeitados e 100% de assinaturas validas no painel de hardening. A dashboard do workspace atual exibiu 30 eventos validos; os 5 registros restantes no admin pertencem a outro workspace ou sao eventos demo/instalacao sem vinculo util com repositorio para o feed do workspace.
