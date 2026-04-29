<?php

namespace App\Support;

use App\Models\Notification;
use App\Models\Workspace;
use App\Models\WorkspaceSubscription;
use App\Models\User;

class SubscriptionLifecycle
{
    public static function cancel(Workspace $workspace, User $user, ?string $reason = null): ?WorkspaceSubscription
    {
        $subscription = $workspace->subscription()->first();

        if (! $subscription) {
            return null;
        }

        $metadata = $subscription->lifecycle_metadata ?? [];
        $metadata['canceled_by_user_id'] = $user->id;
        $metadata['canceled_by_email'] = $user->email;
        $metadata['canceled_from'] = 'dashboard';

        $subscription->update([
            'status' => 'canceled',
            'canceled_at' => now(),
            'cancel_reason' => $reason ?: 'Cancelamento solicitado pelo workspace.',
            'lifecycle_metadata' => $metadata,
        ]);

        Notification::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'title' => 'Assinatura cancelada',
            'body' => 'A assinatura foi marcada como cancelada. O historico permanece disponivel conforme a politica de retencao do plano.',
            'type' => 'billing',
        ]);

        AuditTrail::record('billing.subscription.canceled', $subscription, $workspace, [
            'reason' => $subscription->cancel_reason,
            'provider' => $subscription->provider,
            'provider_reference' => $subscription->provider_reference,
        ]);

        return $subscription;
    }

    public static function summary(?WorkspaceSubscription $subscription): array
    {
        if (! $subscription) {
            return ['status' => 'missing', 'label' => 'Sem assinatura', 'action' => 'Escolher um plano'];
        }

        return match ($subscription->status) {
            'active' => ['status' => 'active', 'label' => 'Ativa', 'action' => 'Pode cancelar ao fim do ciclo operacional'],
            'pending' => ['status' => 'pending', 'label' => 'Pendente', 'action' => 'Aguardando webhook do Mercado Pago'],
            'past_due' => ['status' => 'past_due', 'label' => 'Em atraso', 'action' => 'Regularizar pagamento'],
            'canceled' => ['status' => 'canceled', 'label' => 'Cancelada', 'action' => 'Assinar novo plano para retomar'],
            default => ['status' => $subscription->status, 'label' => ucfirst($subscription->status), 'action' => 'Monitorar ciclo'],
        };
    }
}