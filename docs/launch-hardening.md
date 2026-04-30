# Hardening local antes do lançamento oficial

Este pacote fecha pontos que não dependem do domínio final estar online.

## O que foi reforçado

- Permissões reais nas rotas sensíveis do workspace.
- Apenas usuários com permissão podem criar eventos de teste.
- Apenas owner/admin de billing podem iniciar checkout ou cancelar assinatura.
- Apenas usuários com permissão de GitHub App podem iniciar instalação.
- Apenas usuários com permissão de secret podem rotacionar o segredo do webhook.
- Notas e tarefas agora respeitam a permissão de anotação do workspace.
- Dashboard do usuário ganhou checklist de ativação para produção.
- Onboarding do webhook foi reescrito com acentuação correta e linguagem de uso real.
- Página pública de integração GitHub foi revisada para submissão e demonstração.

## Ainda depende do go-live externo

- Domínio oficial com HTTPS.
- GitHub App real publicado/configurado.
- Mercado Pago em produção.
- E-mail transacional com domínio autenticado.
- Worker de fila em produção.

## Mensagem operacional

O produto está cada vez mais pronto localmente. O que resta para lançamento oficial é menos código novo e mais ativação de infraestrutura, credenciais finais e evidência pública.