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