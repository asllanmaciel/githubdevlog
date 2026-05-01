# Docker local

Use este modo para acompanhar o GitHub DevLog AI localmente sem depender do deploy acionado por push.

## Subir o ambiente

```bash
docker compose up -d --build
```

URLs locais:

- App: `http://localhost:3000`
- Mailpit: `http://localhost:8029`
- MySQL no host: `127.0.0.1:3307`

Credenciais do banco local:

```text
Database: devlog_ai
User: devlog
Password: devlog
Root password: devlog_root
```

## Popular demo

```bash
docker compose exec app php artisan devlog:seed-demo
```

Credenciais demo:

```text
Email: demo@devlog.local
Senha: DevlogDemo123!
```

## Checks uteis

```bash
docker compose exec app php artisan devlog:production-check
docker compose exec app php artisan devlog:preflight
docker compose logs -f app
```

## Parar

```bash
docker compose down
```

Para apagar o banco local e recomeçar do zero:

```bash
docker compose down -v
```
