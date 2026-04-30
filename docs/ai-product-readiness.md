# Prontidão de Produto AI

O painel `Admin > Produto AI` mostra se a camada de inteligência do DevLog AI está pronta para uso real, demonstração comercial e evolução paga.

## O que o painel mede

- Eventos recebidos no ambiente.
- Eventos que já possuem análise AI salva.
- Quantidade de análises avançadas geradas por LLM.
- Custo estimado das análises avançadas.
- Cobertura percentual de eventos analisados.
- Distribuição de risco dos eventos.
- Checklist técnico de schema, ação no dashboard, auditoria e documentação.

## Estratégia de produto

A V1 da AI deve ser gratuita e inclusa nos planos de entrada. Ela usa o analisador local `local-devlog-ai-v1`, não depende de chave externa e entrega resumo, risco, sinais e próximas ações.

A V2 deve ser paga por uso ou limitada por plano, porque utiliza LLM externo e gera custo variável. O produto já registra provider, modelo, custo estimado e tipo de análise para permitir cobrança, auditoria e margem.

## Por que isso importa para lançamento

O nome DevLog AI precisa representar um ganho claro, não apenas marketing. A camada AI ajuda o dev a entender rapidamente o que aconteceu em um webhook, quais riscos existem e qual ação tomar sem ler payloads gigantes linha por linha.

## Próximas evoluções

- Configurar chave OpenAI em produção.
- Definir limites comerciais por plano.
- Exibir consumo de AI no painel do usuário.
- Transformar análises recorrentes em alertas automáticos.
- Criar recomendações por tipo de evento e repositório.