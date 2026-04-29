# Roadmap

- MVP (implementado): webhook de push, validação de assinatura, idempotência, persistência e devlog com fallback.
- Sprint 1: segurança e robustez
  - suporte a retries/exponential backoff,
  - DLQ e logs estruturados,
  - proteção contra payloads muito grandes.
- Sprint 2: produto
  - UI para listar devlogs por repositório,
  - exportação Markdown / JSON / RSS,
  - filtro por período e branch.
- Sprint 3: escala
  - multi-repositório, fila assíncrona e observabilidade,
  - métricas de custo OpenAI + quotas por projeto,
  - autenticação administrativa para visualização.
- Sprint 4: comunidade + GitHub
  - repositório público com exemplos de payload,
  - demonstração pronta (GitHub Action demo),
  - documentação de contribuição e release notes automatizado.
