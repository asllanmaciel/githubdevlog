# Operacao de suporte

O suporte do GitHub DevLog AI deve funcionar como uma fila SaaS, nao como mensagens soltas.

## Campos do chamado

- Categoria: separa webhooks, billing, GitHub App, conta e seguranca.
- Prioridade: define prazo de primeira resposta e resolucao.
- Primeira resposta: marca quando o usuario recebeu retorno inicial.
- Resolucao: marca encerramento real do caso.
- Notas internas: registram diagnostico sem expor detalhes ao usuario.

## SLA inicial

- Urgente: primeira resposta em 2h, resolucao em 12h.
- Alta: primeira resposta em 6h, resolucao em 24h.
- Normal: primeira resposta em 12h, resolucao em 72h.
- Baixa: primeira resposta em 24h, resolucao em 120h.

## Rotina operacional

1. Abrir `/admin/support-operations` no inicio do turno.
2. Priorizar chamados urgentes e vencidos.
3. Registrar `responded_at` quando houver primeira resposta.
4. Registrar nota interna com causa provavel, acao tomada e proximo passo.
5. Encerrar com `resolved_at` apenas quando o usuario tiver caminho claro ou problema resolvido.

## Valor para lancamento

Essa camada mostra maturidade operacional para beta, usuarios reais e avaliacao externa: o produto nao apenas recebe webhooks, mas tambem cria uma experiencia confiavel quando algo falha.
