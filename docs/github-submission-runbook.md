# Runbook de submissao GitHub

Este runbook organiza a execucao pratica da submissao/certificacao sem expor secrets.

## Objetivo imediato

Submeter o GitHub DevLog AI ao GitHub Developer Program usando evidencias reais do dominio `ghdevlog.com` e do GitHub App instalado. A listagem Marketplace deve ficar como etapa seguinte, principalmente se houver plano pago.

## Fontes oficiais verificadas

- Developer Program: exige uma integracao GitHub em producao ou desenvolvimento e um email de suporte para usuarios.
- Marketplace: exige valor para a comunidade GitHub, contato valido, descricao relevante, plano de preco, politica de privacidade, suporte, links funcionais, imagens e integracao alem de autenticacao.
- Webhooks: GitHub recomenda validar `X-Hub-Signature-256` com HMAC-SHA256, usando comparacao segura e sem hardcode de segredo.

## Evidencias que ja existem

- Dominio: `https://ghdevlog.com`.
- Webhook GitHub App: `https://ghdevlog.com/webhooks/github-app`.
- Callback: `https://ghdevlog.com/github/callback`.
- Instalacao vinculada: `128516060`.
- Eventos reais recebidos: `push`, `workflow_run`, `installation`.
- Repositorio real: `AM-TIIX/TIIX-Global`.
- Validacao: `github-app-x-hub-signature-256`.
- Hardening: 35 eventos, 35 aceitos, 0 rejeitados, validade 100%.
- Teste automatizado local: `php artisan test tests\Feature\WebhookHardeningTest.php`.

## Capturas obrigatorias

1. `https://ghdevlog.com/dashboard/github`
   - Mostrar GitHub App conectado e instalacao vinculada.
   - Mascarar o secret manual se aparecer na tela.

2. `https://ghdevlog.com/dashboard/events`
   - Mostrar eventos reais `push` e `workflow_run`.
   - Mostrar assinatura valida, delivery id e repositorio.

3. `https://ghdevlog.com/dashboard/events/{id}`
   - Abrir um `workflow_run completed`.
   - Mostrar resumo, payload sanitizado, branch, sha e delivery id.

4. `https://ghdevlog.com/admin/webhook-events`
   - Mostrar source `github-app`, status `accepted`, assinatura valida, metodo de validacao e datas.

5. `https://ghdevlog.com/admin/webhook-hardening`
   - Mostrar totais: 35 aceitos, 0 rejeitados, 100% valido.

6. Paginas publicas
   - `https://ghdevlog.com/privacy`
   - `https://ghdevlog.com/terms`
   - Pagina de suporte ou contato publicado.

## Video curto sugerido

Gravar de 60 a 90 segundos:

1. Abrir a pagina de configuracao GitHub do workspace.
2. Mostrar GitHub App instalado.
3. Fazer ou apontar um push real no repositorio de teste.
4. Abrir o inbox de eventos.
5. Entrar no evento novo.
6. Mostrar assinatura valida, delivery id e payload sanitizado.
7. Fechar no hardening com 0 rejeicoes.

## Resposta curta para formulario

GitHub DevLog AI is a private webhook inbox for GitHub developers. It receives GitHub App and repository webhook events, validates `X-Hub-Signature-256`, stores sanitized payloads in isolated workspaces, and gives teams a readable delivery history with repository context, delivery IDs, notes, tasks and admin hardening metrics for debugging GitHub integrations.

## Resposta de seguranca

The app validates GitHub webhook signatures with HMAC-SHA256 before accepting deliveries, uses workspace isolation, stores secrets outside source code, sanitizes headers and payloads before storage, records accepted/rejected delivery state, deduplicates deliveries by delivery id/dedupe key, and exposes audit/hardening views without displaying raw secrets.

## Bloqueadores antes do envio

- Confirmar que a pagina publica de suporte ou email de suporte esta visivel.
- Confirmar que Privacy e Terms respondem 200 em producao.
- Conferir todos os prints para remover secrets.
- Se a submissao for Marketplace paga, nao enviar ainda sem validar publisher verificado, billing Marketplace e maturidade de instalacoes.
