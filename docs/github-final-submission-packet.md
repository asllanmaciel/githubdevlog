# Pacote final de envio - GitHub Developer Program

Data de referencia: 2026-05-04.

## Resumo para envio

GitHub DevLog AI is a private webhook inbox for GitHub developers. It receives GitHub App and repository webhook events, validates `X-Hub-Signature-256`, stores sanitized payloads in isolated workspaces, and gives teams a readable delivery history with repository context, delivery IDs, notes, tasks and admin hardening metrics for debugging GitHub integrations.

## What does your application do?

GitHub DevLog AI helps developers and teams debug GitHub webhooks with confidence. Instead of relying on local logs, temporary request bins or screenshots from terminal sessions, users get a private workspace where GitHub webhook deliveries are received, signature-validated, sanitized and organized into an inbox.

The product shows event type, delivery ID, repository, branch, sender, workflow status, commits, changed files and sanitized payload details. It also supports notes, tasks, AI-assisted event analysis and admin hardening views for auditing accepted/rejected deliveries.

## Who is the target audience?

The target audience is developers building GitHub Apps, SaaS teams integrating with GitHub webhooks, agencies demonstrating GitHub automations, and engineering teams that need a reliable audit trail for webhook deliveries.

## How does it integrate with GitHub?

Users can connect GitHub through the GitHub App flow or by configuring repository webhooks. The production GitHub App webhook endpoint is:

```text
https://ghdevlog.com/webhooks/github-app
```

The app receives GitHub events such as `push`, `workflow_run` and `installation`, validates signatures using HMAC SHA-256, maps deliveries to the correct workspace, stores sanitized payloads and displays a scoped event inbox for the workspace user.

## Security and privacy answer

GitHub DevLog AI validates GitHub webhook signatures before accepting deliveries, isolates data by workspace, avoids exposing raw secrets in the UI, sanitizes webhook payloads, supports secret rotation and records accepted/rejected delivery state. Admin hardening views show delivery health without requiring users to inspect sensitive raw logs.

Public legal pages are available:

```text
https://ghdevlog.com/privacy
https://ghdevlog.com/terms
```

## Operational evidence

Production domain:

```text
https://ghdevlog.com
```

Evidence captured on 2026-05-04:

- Workspace dashboard `/dashboard/events` shows 30 workspace events and 30 valid signatures.
- Event detail `/dashboard/events/35` shows a real `workflow_run` event with valid signature, `github-app` source, repository `AM-TIIX/TIIX-Global`, branch `main`, workflow `changelog-automation` and delivery `878c00d0-47b8-11f1-86e7-e2ec2588e77b`.
- Admin `/admin/webhook-events` shows 35 total webhook records.
- Admin `/admin/webhook-hardening` shows 35 total events, 35 accepted, 0 rejected and 100% valid signatures.
- Admin overview shows 35 received webhooks and event types including `workflow_run`, `push`, `installation` and `pull_request`.
- Privacy and Terms pages are published and visible.

## Dashboard x admin reconciliation

The admin webhook table currently contains 35 received events. The active workspace dashboard intentionally shows 30 events, all with valid signatures. The 5 records not shown in the workspace inbox belong to another workspace or are demo/installation events without useful repository linkage for the current workspace feed.

This means the evidence is consistent:

```text
35 total events received in the database.
30 valid events visible in the current workspace inbox.
5 records outside the workspace inbox because they are from another workspace or are demo/installation records.
```

## Screenshot checklist

- `dashboard-events-30-validos`: workspace inbox showing 30 events and 30 valid signatures.
- `dashboard-event-35-workflow-run`: event detail showing `workflow_run#35`, valid HMAC result and delivery ID.
- `admin-webhook-events-35-total`: admin table showing 35 total records.
- `admin-webhook-hardening-35-accepted`: hardening page showing 35 total, 35 accepted, 0 rejected, 100% validity.
- `admin-overview-webhook-metrics`: admin overview showing 35 webhooks and event type distribution.
- `public-privacy`: privacy page.
- `public-terms`: terms page.

## Short demo script

1. Open `https://ghdevlog.com/dashboard/events`.
2. Show the workspace inbox with 30 events and 30 valid signatures.
3. Open event `workflow_run#35`.
4. Show valid HMAC result, GitHub App source, repository, branch, workflow and delivery ID.
5. Open `/admin/webhook-events`.
6. Show 35 total records across the database.
7. Open `/admin/webhook-hardening`.
8. Show 35 accepted, 0 rejected and 100% valid signatures.
9. Close by showing `/privacy` and `/terms` are published.

## Final one-paragraph submission note

GitHub DevLog AI is already running on `https://ghdevlog.com` with a real GitHub App webhook endpoint at `https://ghdevlog.com/webhooks/github-app`. On 2026-05-04, the app received and validated real GitHub App deliveries from `AM-TIIX/TIIX-Global`, including `push` and `workflow_run` events. The active workspace inbox shows 30 valid events, while the admin hardening panel shows 35 total received records, 35 accepted, 0 rejected and 100% valid signatures. The 5 records outside the workspace inbox are expected admin/test/other-workspace records, so the user-facing inbox remains scoped to the current workspace.

