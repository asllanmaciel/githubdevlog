# Roadmap com pendência apenas de go-live

O roadmap foi normalizado para separar maturidade estrutural do produto dos bloqueadores externos de lançamento.

## Regra adotada

Itens já cobertos por implementação local, documentação, painel administrativo, suporte, billing interno, segurança, GitHub App estrutural, auditoria, evidências ou dashboard foram marcados como concluídos.

Itens que dependem de ambiente externo final foram agrupados como `Go-live externo` e continuam pendentes.

## Pendências de go-live externo

- domínio oficial com HTTPS e `APP_URL` final;
- GitHub App oficial criado no GitHub e conectado às credenciais reais;
- Mercado Pago em produção com credenciais definitivas;
- e-mail transacional real com domínio autenticado;
- screenshots finais somente depois do domínio/ambiente definitivo.

## Como usar

O painel `/admin/roadmap` agora deve ser lido como indicador de execução do produto. O painel `/admin/go-live` continua sendo o lugar correto para bloquear ou liberar lançamento público.