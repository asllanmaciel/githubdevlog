# Análise AI de eventos GitHub

O DevLog AI agora possui uma primeira camada de análise inteligente para webhooks recebidos.

## O que a análise gera

- resumo humano do evento;
- nível de risco (`low`, `medium`, `high`);
- sinais detectados no payload, commits e arquivos alterados;
- próximos passos sugeridos para triagem.

## Como usar

No dashboard do workspace, abra qualquer evento recebido e clique em `Gerar análise AI`.

A análise fica persistida no próprio evento, com data de geração e provider usado.

## Provider atual

O provider inicial é `local-devlog-ai-v1`: um analisador determinístico de sinais, útil para o MVP e seguro para operar sem depender de chave externa.

A arquitetura permite evoluir depois para um provider LLM externo mantendo os mesmos campos no banco e a mesma experiência no dashboard.
## Camada paga com LLM

A análise local é a camada gratuita. A análise avançada usa LLM externo e deve ser limitada por plano, pois gera custo variável para a plataforma.

Quando o usuário solicita AI avançada, o sistema verifica:

1. se `OPENAI_API_KEY` está configurada;
2. se o plano do workspace possui limite mensal de análises avançadas;
3. se ainda há saldo no mês;
4. se a chamada ao provider retorna JSON válido.

O resultado fica persistido no evento com `ai_analysis_type = llm`, provider, custo estimado e tokens quando disponíveis.