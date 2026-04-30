# Política de visibilidade do repositório

## Resumo

O código-fonte do GH DevLog é privado por padrão.

A distribuição pública acontece pelo GitHub App, pelo site oficial e por documentação controlada.

## Classificação

| Item | Visibilidade | Motivo |
| --- | --- | --- |
| Repositório Laravel principal | Privado | Contém o produto SaaS e regras de negócio. |
| GitHub App | Público | Necessário para instalação por usuários e organizações. |
| Landing page | Pública | Aquisição, confiança e revisão do produto. |
| Documentação de usuário | Pública | Reduz fricção de instalação e suporte. |
| Documentação admin interna | Privada | Contém operação, decisões e detalhes de gestão. |
| Exemplos de payload | Público, sanitizado | Ajuda integração sem expor dados reais. |
| Screenshots | Público, revisado | Evidência comercial e submissão. |
| Secrets, chaves e envs | Nunca público | Segurança operacional. |

## Regra prática

Se o material ajuda o usuário a instalar, entender ou confiar no produto, pode ser público.

Se o material ajuda alguém a copiar, operar ou explorar o SaaS por dentro, deve permanecer privado.

## Alternativa segura para presença open source

Publicar apenas um repositório público de documentação e exemplos, sem backend.

Esse repositório pode reforçar credibilidade sem entregar o produto:

- Guias de instalação.
- Exemplos de eventos.
- Explicação de permissões do GitHub App.
- Política de retenção e segurança.
- Links para suporte e status.

## Status atual recomendado

- `asllanmaciel/githubdevlog`: privado.
- `github.com/apps/gh-devlog`: público/instalável.
- `ghdevlog.com`: público.
- Futuro `asllanmaciel/ghdevlog-public`: opcional.
