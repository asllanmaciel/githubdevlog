# Kit público para perfil GitHub

Este documento define o que pode ser publicado no GitHub sem expor o código-fonte do SaaS.

## Decisão

O repositório principal do GH DevLog deve permanecer privado.

O GitHub App, a landing page, a documentação pública, os exemplos de uso e os materiais de submissão podem ser públicos.

## Por que manter o repositório privado

- O produto contém lógica de billing, uso, AI, auditoria, webhooks e administração SaaS.
- Um repositório público facilitaria cópia direta da implementação.
- O GitHub App não exige que o código-fonte do produto seja público.
- Usuários precisam instalar o App e confiar na plataforma, não clonar o backend.

## O que pode ser público

- Perfil público do GitHub App: `https://github.com/apps/gh-devlog`.
- Site oficial: `https://ghdevlog.com`.
- Página pública do App: `https://ghdevlog.com/github`.
- Documentação de instalação do GitHub App.
- FAQ de segurança e privacidade.
- Changelog público.
- Status público.
- Exemplos de payloads e guias de webhook.
- Screenshots de landing, dashboard, AI e trilha de auditoria.

## O que não deve ser público

- Código-fonte do SaaS.
- `.env`, secrets, private keys e tokens.
- Implementação de billing e regras internas de cobrança.
- Modelos internos de análise AI quando forem diferencial competitivo.
- Scripts de produção com caminhos, usuários ou detalhes sensíveis.
- Dumps de banco, payloads reais de clientes ou logs internos.

## Repositório público opcional

Se quisermos presença pública no GitHub, criar um repositório separado, por exemplo:

`asllanmaciel/ghdevlog-public`

Conteúdo recomendado:

- `README.md` com proposta, links e screenshots.
- `docs/install-github-app.md`.
- `docs/security.md`.
- `docs/webhook-events.md`.
- `examples/push-event.json`.
- `examples/pull-request-event.json`.
- `CHANGELOG.md` resumido.

Este repositório não deve conter código de aplicação Laravel.

## Checklist antes de publicar

- Confirmar que o App está público/instalável.
- Confirmar que o domínio oficial usa HTTPS.
- Confirmar que o webhook do GitHub App responde por POST.
- Confirmar que a página `/github` explica permissões e eventos.
- Confirmar que screenshots não exibem secrets, emails privados indevidos ou payloads sensíveis.
- Confirmar que o repositório principal continua privado.

## Mensagem curta para o perfil

GH DevLog transforma eventos do GitHub em um histórico privado por workspace, com validação de assinatura, auditoria, visão operacional e análise AI para equipes que precisam rastrear o que aconteceu sem depender de logs soltos.
