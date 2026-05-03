<?php

namespace App\Support;

use App\Models\BillingEvent;
use App\Models\SupportTicket;
use App\Models\User;
use App\Models\WebhookEvent;
use App\Models\Workspace;
use App\Models\WorkspaceSubscription;
use Illuminate\Support\Facades\Schema;

class ProductMetrics
{
    public static function report(): array
    {
        $users = Schema::hasTable('users') ? User::count() : 0;
        $workspaces = Schema::hasTable('workspaces') ? Workspace::count() : 0;
        $webhooks = Schema::hasTable('webhook_events') ? WebhookEvent::count() : 0;
        $validWebhooks = Schema::hasTable('webhook_events') ? WebhookEvent::where('signature_valid', true)->count() : 0;
        $activeSubscriptions = Schema::hasTable('workspace_subscriptions')
            ? WorkspaceSubscription::with('plan')->where('status', 'active')->get()
            : collect();
        $mrrCents = $activeSubscriptions->sum(fn ($subscription) => (int) ($subscription->plan?->price_cents ?? 0));
        $paidWorkspaces = $activeSubscriptions->where('plan.price_cents', '>', 0)->count();
        $billingEvents = Schema::hasTable('billing_events') ? BillingEvent::where('provider', 'mercado_pago')->count() : 0;
        $billingApproved = Schema::hasTable('billing_events') ? BillingEvent::where('provider', 'mercado_pago')->where('status', 'processed_active')->count() : 0;
        $openTickets = Schema::hasTable('support_tickets') ? SupportTicket::whereIn('status', ['open', 'pending'])->count() : 0;
        $activatedWorkspaces = Schema::hasTable('webhook_events')
            ? WebhookEvent::where('signature_valid', true)->distinct('workspace_id')->count('workspace_id')
            : 0;

        return [
            'metrics' => [
                ['label' => 'Usuarios', 'value' => $users, 'detail' => 'contas criadas no produto'],
                ['label' => 'Workspaces', 'value' => $workspaces, 'detail' => 'ambientes privados criados'],
                ['label' => 'Ativacao', 'value' => self::percent($activatedWorkspaces, $workspaces).'%', 'detail' => $activatedWorkspaces.' workspace(s) com webhook valido'],
                ['label' => 'MRR', 'value' => 'R$ '.number_format($mrrCents / 100, 2, ',', '.'), 'detail' => $paidWorkspaces.' workspace(s) pagos ativos'],
                ['label' => 'Webhooks', 'value' => $webhooks, 'detail' => $validWebhooks.' assinatura(s) validada(s)'],
                ['label' => 'Mercado Pago', 'value' => $billingApproved.'/'.$billingEvents, 'detail' => 'pagamentos confirmados / eventos recebidos'],
            ],
            'funnel' => [
                ['label' => 'Conta criada', 'count' => $users, 'percent' => 100],
                ['label' => 'Workspace criado', 'count' => $workspaces, 'percent' => self::percent($workspaces, $users)],
                ['label' => 'Webhook valido recebido', 'count' => $activatedWorkspaces, 'percent' => self::percent($activatedWorkspaces, $workspaces)],
                ['label' => 'Assinatura ativa', 'count' => $activeSubscriptions->count(), 'percent' => self::percent($activeSubscriptions->count(), $workspaces)],
                ['label' => 'Plano pago ativo', 'count' => $paidWorkspaces, 'percent' => self::percent($paidWorkspaces, $workspaces)],
            ],
            'risks' => [
                ['label' => 'Suporte aberto', 'value' => $openTickets, 'state' => $openTickets > 0 ? 'atenção' : 'ok'],
                ['label' => 'Webhooks invalidos', 'value' => max($webhooks - $validWebhooks, 0), 'state' => $webhooks > $validWebhooks ? 'atenção' : 'ok'],
                ['label' => 'Billing sem conversao', 'value' => $billingEvents > 0 && $billingApproved === 0 ? 1 : 0, 'state' => $billingEvents > 0 && $billingApproved === 0 ? 'atenção' : 'ok'],
            ],
        ];
    }

    private static function percent(int $part, int $total): int
    {
        return $total > 0 ? (int) round(($part / $total) * 100) : 0;
    }
}
