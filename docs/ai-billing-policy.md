# Política de cobrança da AI

O DevLog AI passa a operar com duas camadas de análise:

## AI grátis

- Provider: `local-devlog-ai-v1`.
- Disponível para todos os planos.
- Não chama API externa.
- Não gera custo variável para a operação.
- Ideal para triagem rápida, risco básico, sinais e próximos passos.

## AI avançada

- Provider: OpenAI via Responses API.
- Usa `OPENAI_API_KEY` e modelo configurado em `OPENAI_AI_ANALYSIS_MODEL`.
- Possui custo real para a plataforma.
- É limitada por plano em `monthly_ai_analysis_limit`.
- Registra `ai_estimated_cost_cents`, provider, tipo, tokens e eventuais erros no evento.

## Regra comercial

A AI avançada deve ser vendida com margem acima do custo estimado.

Campos relevantes em `billing_plans`:

- `monthly_ai_analysis_limit`: quantidade mensal inclusa no plano;
- `ai_analysis_overage_price_cents`: preço estimado por análise excedente;
- `features.ai_advanced_analysis_limit`: espelho legível para exibição/admin.

## Variáveis de ambiente

```env
OPENAI_API_KEY=
OPENAI_BASE_URL=https://api.openai.com/v1
OPENAI_AI_ANALYSIS_MODEL=gpt-5.4-mini
OPENAI_AI_ANALYSIS_TIMEOUT=20
OPENAI_AI_ANALYSIS_COST_CENTS=15
```

## Comportamento esperado

- Sem `OPENAI_API_KEY`, o botão de AI avançada fica indisponível.
- Se o plano não tiver limite de AI avançada, o usuário usa apenas AI grátis.
- Se o limite mensal acabar, o usuário precisa fazer upgrade ou contratar mais uso.