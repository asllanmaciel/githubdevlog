# Modelo de prontidão de go-live

O painel `/admin/go-live` separa dois tipos de critério:

- `local`: preparação estrutural do produto, documentação, suporte, billing interno, auditoria, status e dashboards;
- `externo`: dependências que só fecham no ambiente real, como domínio HTTPS, GitHub App oficial, credenciais de produção, fila supervisionada e e-mail transacional.

## Por que separar

Sem essa divisão, o produto parece incompleto quando na prática o que falta é publicação e credenciais reais.

A leitura correta é:

- preparação local em 100% significa que o produto está pronto para ir ao ambiente final;
- prontidão total em 100% só acontece depois do domínio, GitHub App, Mercado Pago e infraestrutura final estarem validados.