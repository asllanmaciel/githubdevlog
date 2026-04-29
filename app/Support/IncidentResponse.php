<?php

namespace App\Support;

use App\Models\AuditLog;
use App\Models\BillingEvent;
use App\Models\SupportTicket;
use App\Models\WebhookEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class IncidentResponse
{
    public static function report(): array
    {
        $failedJobs = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;
        $pendingJobs = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
        $openTickets = SupportTicket::whereIn('status', ['open', 'new', 'pending'])->count();
        $invalidWebhooks = WebhookEvent::where('signature_valid', false)->where('created_at', '>=', now()->subDay())->count();
        $billingAttention = BillingEvent::whereIn('status', ['unmatched', 'pending_lookup', 'usage_invoice_unmatched', 'ignored'])->where('created_at', '>=', now()->subDay())->count();
        $recentSensitiveActions = class_exists(AuditLog::class)
            ? AuditLog::whereIn('action', ['workspace.secret.rotated', 'workspace.data.exported', 'workspace.data.purge_requested'])->where('created_at', '>=', now()->subDay())->count()
            : 0;

        $checks = collect([
            self::check('Filas sem falhas', $failedJobs === 0, $failedJobs.' job(s) falho(s)', 'queue'),
            self::check('Fila sem acumulo anormal', $pendingJobs < 100, $pendingJobs.' job(s) pendente(s)', 'queue'),
            self::check('Suporte controlado', $openTickets < 10, $openTickets.' chamado(s) aberto(s)', 'support'),
            self::check('Webhooks sem invalidos recentes', $invalidWebhooks === 0, $invalidWebhooks.' invalido(s) nas ultimas 24h', 'webhooks'),
            self::check('Billing sem atencao recente', $billingAttention === 0, $billingAttention.' evento(s) de billing exigem revisao', 'billing'),
            self::check('Acoes sensiveis rastreadas', true, $recentSensitiveActions.' acao(oes) sensiveis em 24h', 'audit'),
        ]);

        $incidents = collect([
            ...($failedJobs > 0 ? [[
                'severity' => 'critical',
                'title' => 'Jobs falhos na fila',
                'detail' => $failedJobs.' job(s) falharam. Verifique logs e reprocessamento.',
                'command' => 'php artisan queue:failed',
            ]] : []),
            ...($billingAttention > 0 ? [[
                'severity' => 'high',
                'title' => 'Eventos Mercado Pago precisam revisao',
                'detail' => $billingAttention.' evento(s) recentes ficaram pendentes, ignorados ou sem associacao.',
                'command' => 'Abrir /admin/billing-events',
            ]] : []),
            ...($invalidWebhooks > 0 ? [[
                'severity' => 'high',
                'title' => 'Webhooks com assinatura invalida',
                'detail' => $invalidWebhooks.' evento(s) invalidos nas ultimas 24h.',
                'command' => 'Conferir secrets e entregas recentes no GitHub.',
            ]] : []),
            ...($openTickets >= 10 ? [[
                'severity' => 'medium',
                'title' => 'Fila de suporte alta',
                'detail' => $openTickets.' chamados abertos exigem triagem.',
                'command' => 'Abrir /admin/support-tickets',
            ]] : []),
        ]);

        return [
            'healthy' => $checks->where('done', false)->isEmpty(),
            'checks' => $checks,
            'incidents' => $incidents,
            'metrics' => [
                'failed_jobs' => $failedJobs,
                'pending_jobs' => $pendingJobs,
                'open_tickets' => $openTickets,
                'invalid_webhooks_24h' => $invalidWebhooks,
                'billing_attention_24h' => $billingAttention,
                'sensitive_actions_24h' => $recentSensitiveActions,
            ],
        ];
    }

    private static function check(string $title, bool $done, string $detail, string $area): array
    {
        return compact('title', 'done', 'detail', 'area');
    }
}