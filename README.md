# GitHub DevLog AI

Projeto para gerar devlogs automáticos a partir de eventos de push do GitHub.

## Objetivo

Transformar commits recebidos via webhook em um resumo técnico em português, com persistência no banco e saída pronta para acompanhamento de evolução do produto.

## Arquitetura atual do MVP

- `public/index.php`: ponto de entrada HTTP com endpoint `/webhook/github`.
- `app/Http/Controllers/GitHubWebhookController.php`: valida assinatura, parseia evento e coordena o fluxo.
- `app/Services/GitHubService.php`: tratamento do payload do GitHub.
- `app/Services/OpenAIService.php`: geração do resumo com fallback quando a API não está configurada.
- `app/Services/DevLogService.php`: persistência de repositório, evento, commits e devlogs.
- `app/Core/Database.php` e `app/Core/Env.php`: infraestrutura de runtime.
- `database/schema.sql`: modelo relacional do MVP.

## Como rodar

1. Copie `.env.example` para `.env` e ajuste as variáveis.
2. Instale as dependências do PHP no ambiente (extensões `pdo`, `pdo_sqlite` ou `pdo_mysql`, `curl`).
3. Rode o servidor embutido:
   - `php -S 127.0.0.1:8080 -t public`
4. Configure o webhook do GitHub para enviar eventos `push` para:
   - `http://seu-host:8080/webhook/github`
5. Acesse a documentação local no navegador:
   - `http://localhost:8080/docs`

### Rodando com Docker

1. Copie `.env.example` para `.env`.
2. Suba o container:
   - `docker compose up --build`
3. Endpoints:
   - Health: `http://localhost:8080/`
   - Webhook: `http://localhost:8080/webhook/github`
   - Documentação: `http://localhost:8080/docs`

## Variáveis de ambiente

- `APP_ENV`: `local` (padrão) ou `production`.
- `DB_DSN`: DSN do PDO (opcional; padrão `sqlite:database/devlog.sqlite`).
- `DB_USER`: usuário do banco (se aplicável).
- `DB_PASS`: senha do banco (se aplicável).
- `OPENAI_API_KEY`: token da OpenAI.
- `OPENAI_MODEL`: padrão `gpt-4o-mini`.
- `GITHUB_WEBHOOK_SECRET`: segredo configurado no webhook.

## Estado atual

O MVP já contempla:
- Recebimento e validação de `push`.
- Persistência de evento e commits.
- Geração de devlog com OpenAI e fallback local.
- Idempotência por `X-GitHub-Delivery`.

## Leitor de documentação

- O endpoint `GET /docs` abre uma interface simples para leitura dos arquivos `.md` da pasta `docs`.
- `GET /api/docs/list` retorna a lista de arquivos markdown.
- `GET /api/docs/raw?file=<nome.md>` retorna o conteúdo bruto de um documento.

## Observabilidade visual da documentacao

- A pagina `GET /docs` foi redesenhada com UX premium: sidebar com fundo em vidro, busca de arquivos, metadados e preview responsivo.

## Versionamento e GitHub

- O projeto esta versionado com Git neste diretorio com commit inicial:
  - `feat: inicializa MVP DevLog AI com webhook, docs e Docker`
- Arquivos sensiveis ou de runtime estao no `.gitignore`:
  - `.env`
  - `database/devlog.sqlite`
- Para publicar no GitHub:
  1. `git remote add origin https://github.com/<SEU_USUARIO>/<SEU_REPO>.git`
  2. `git branch -M master`
  3. `git push -u origin master`
