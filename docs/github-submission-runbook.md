# Runbook GitHub Developer Program e evidencias

Este runbook organiza a adesao ao GitHub Developer Program e o pacote de evidencias do produto sem expor secrets.

## Objetivo imediato

Manter o GitHub DevLog AI pronto para revisao externa usando evidencias reais do dominio `ghdevlog.com` e do GitHub App instalado.

Status atual: a adesao ao GitHub Developer Program ja foi concluida na conta do mantenedor. O programa e uma associacao da conta, nao uma revisao obrigatoria do projeto. A listagem publica do GitHub App/Marketplace fica como etapa seguinte e separada.

## Fontes oficiais verificadas

- Developer Program: aberto a devs/empresas com uma integracao GitHub em producao ou desenvolvimento e um email de suporte para usuarios GitHub.
- Marketplace: exige valor para a comunidade GitHub, contato valido, descricao relevante, plano de preco, politica de privacidade, suporte, links funcionais, imagens e integracao alem de autenticacao.
- Webhooks: GitHub recomenda validar `X-Hub-Signature-256` com HMAC-SHA256, usando comparacao segura e sem hardcode de segredo.

## Evidencias que ja existem

- Dominio: `https://ghdevlog.com`.
- GitHub Developer Program: conta inscrita, suporte `contato@asllanmaciel.com.br`, site `https://ghdevlog.com`.
- GitHub App publico: `https://github.com/apps/gh-devlog`.
- Pagina publica da integracao: `https://ghdevlog.com/github`.
- Webhook GitHub App: `https://ghdevlog.com/webhooks/github-app`.
- Callback: `https://ghdevlog.com/github/callback`.
- Instalacao vinculada: `128516060`.
- Eventos reais recebidos: `push`, `workflow_run`, `installation`.
- Repositorio real: `AM-TIIX/TIIX-Global`.
- Validacao: `github-app-x-hub-signature-256`.
- Hardening: 35 eventos, 35 aceitos, 0 rejeitados, validade 100%.
- Dashboard do workspace atual: 30 eventos visiveis, 30 assinaturas validas.
- Conciliacao admin x dashboard: 35 eventos totais no admin; 30 aparecem no workspace atual; 5 pertencem a outro workspace ou sao demo/instalacao sem repositorio util para o feed.
- Teste automatizado local: `php artisan test tests\Feature\WebhookHardeningTest.php`.

## Capturas obrigatorias

1. GitHub Developer Program em Settings
   - Mostrar suporte `contato@asllanmaciel.com.br`.
   - Mostrar website `https://ghdevlog.com`.

2. `https://github.com/apps/gh-devlog`
   - Mostrar pagina publica do GitHub App, sem secrets.

3. `https://ghdevlog.com/github`
   - Mostrar a pagina publica explicando a integracao.

4. `https://ghdevlog.com/dashboard/github`
   - Mostrar GitHub App conectado e instalacao vinculada.
   - Mascarar o secret manual se aparecer na tela.

5. `https://ghdevlog.com/dashboard/events`
   - Mostrar eventos reais `push` e `workflow_run`.
   - Mostrar assinatura valida, delivery id, repositorio e contador de 30 eventos do workspace.

6. `https://ghdevlog.com/dashboard/events/{id}`
   - Abrir um `workflow_run completed`.
   - Mostrar resumo, payload sanitizado, branch, sha e delivery id.

7. `https://ghdevlog.com/admin/webhook-events`
   - Mostrar source `github-app`, status `accepted`, assinatura valida, metodo de validacao e datas.
   - Mostrar que o admin lista 35 registros totais, incluindo eventos de outros workspaces/testes.

8. `https://ghdevlog.com/admin/webhook-hardening`
   - Mostrar totais: 35 aceitos, 0 rejeitados, 100% valido.

9. Paginas publicas
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

## Resposta curta se houver formulario/ticket

GitHub DevLog AI is a private webhook inbox for GitHub developers. It receives GitHub App and repository webhook events, validates `X-Hub-Signature-256`, stores sanitized payloads in isolated workspaces, and gives teams a readable delivery history with repository context, delivery IDs, notes, tasks and admin hardening metrics for debugging GitHub integrations.

## Resposta de evidencia operacional

The admin webhook table currently contains 35 received events. The active workspace dashboard intentionally shows 30 events, all with valid signatures. The 5 records not shown in the workspace inbox belong to another workspace or are demo/installation events without repository linkage, so the user-facing inbox remains scoped to useful events for the current workspace.

## Pacote final

Ver tambem: `docs/github-final-submission-packet.md`.

## Resposta de seguranca

The app validates GitHub webhook signatures with HMAC-SHA256 before accepting deliveries, uses workspace isolation, stores secrets outside source code, sanitizes headers and payloads before storage, records accepted/rejected delivery state, deduplicates deliveries by delivery id/dedupe key, and exposes audit/hardening views without displaying raw secrets.

## Bloqueadores antes de Marketplace/listagem publica

- Confirmar que a pagina publica de suporte ou email de suporte esta visivel.
- Confirmar que Privacy e Terms respondem 200 em producao.
- Conferir todos os prints para remover secrets.
- Se a submissao for Marketplace paga, nao enviar ainda sem validar publisher verificado, billing Marketplace e maturidade de instalacoes.
