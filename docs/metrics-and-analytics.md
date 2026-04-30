# Métricas e analytics

O GitHub DevLog AI aceita scripts de métricas por variáveis de ambiente. Todos são opcionais: se a variável ficar vazia, nenhum script é injetado.

## Variáveis disponíveis

- `DEVLOG_GTM_ID`: Google Tag Manager, exemplo `GTM-XXXXXXX`.
- `DEVLOG_GA_MEASUREMENT_ID`: Google Analytics 4 direto, exemplo `G-XXXXXXXXXX`.
- `DEVLOG_META_PIXEL_ID`: Meta Pixel.
- `DEVLOG_HOTJAR_ID`: Hotjar.
- `DEVLOG_CLARITY_ID`: Microsoft Clarity.
- `DEVLOG_PLAUSIBLE_DOMAIN`: domínio no Plausible, exemplo `ghdevlog.com`.

## Recomendação de produção

Use `DEVLOG_GTM_ID` como camada principal quando quiser gerenciar vários pixels sem novo deploy. Evite ativar `DEVLOG_GTM_ID` e `DEVLOG_GA_MEASUREMENT_ID` ao mesmo tempo se o GA4 também estiver dentro do GTM, para não duplicar pageviews.

## Onde conferir

- Scripts são renderizados no layout global em `resources/views/components/layout.blade.php`.
- Configuração fica em `config/devlog.php`.
- Exemplos ficam no `.env.example`.
