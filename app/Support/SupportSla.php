<?php

namespace App\Support;

use App\Models\SupportTicket;
use Illuminate\Support\Carbon;

class SupportSla
{
    public static function categories(): array
    {
        return [
            'technical' => 'Webhooks e integracoes',
            'billing' => 'Billing e planos',
            'github_app' => 'GitHub App',
            'account' => 'Conta e acesso',
            'security' => 'Seguranca',
        ];
    }

    public static function priorities(): array
    {
        return [
            'low' => 'Baixa',
            'normal' => 'Normal',
            'high' => 'Alta',
            'urgent' => 'Urgente',
        ];
    }

    public static function targets(string $priority): array
    {
        return match ($priority) {
            'urgent' => ['first_response_hours' => 2, 'resolution_hours' => 12],
            'high' => ['first_response_hours' => 6, 'resolution_hours' => 24],
            'low' => ['first_response_hours' => 24, 'resolution_hours' => 120],
            default => ['first_response_hours' => 12, 'resolution_hours' => 72],
        };
    }

    public static function apply(string $priority, ?Carbon $from = null): array
    {
        $from ??= now();
        $targets = self::targets($priority);

        return [
            'first_response_due_at' => $from->copy()->addHours($targets['first_response_hours']),
            'resolution_due_at' => $from->copy()->addHours($targets['resolution_hours']),
        ];
    }

    public static function badge(SupportTicket $ticket): string
    {
        if ($ticket->status === 'resolved' || $ticket->resolved_at) {
            return 'resolvido';
        }

        if ($ticket->resolution_due_at && $ticket->resolution_due_at->isPast()) {
            return 'sla vencido';
        }

        if ($ticket->first_response_due_at && ! $ticket->responded_at && $ticket->first_response_due_at->isPast()) {
            return 'resposta vencida';
        }

        if ($ticket->priority === 'urgent') {
            return 'urgente';
        }

        return 'em dia';
    }

    public static function report(): array
    {
        $openStatuses = ['open', 'new', 'pending', 'triage'];
        $open = SupportTicket::whereIn('status', $openStatuses);
        $now = now();

        return [
            'open' => (clone $open)->count(),
            'urgent' => SupportTicket::whereIn('status', $openStatuses)->where('priority', 'urgent')->count(),
            'first_response_overdue' => SupportTicket::whereIn('status', $openStatuses)
                ->whereNull('responded_at')
                ->whereNotNull('first_response_due_at')
                ->where('first_response_due_at', '<', $now)
                ->count(),
            'resolution_overdue' => SupportTicket::whereIn('status', $openStatuses)
                ->whereNotNull('resolution_due_at')
                ->where('resolution_due_at', '<', $now)
                ->count(),
            'by_category' => SupportTicket::whereIn('status', $openStatuses)
                ->selectRaw('category, count(*) as total')
                ->groupBy('category')
                ->orderByDesc('total')
                ->get(),
            'recent' => SupportTicket::latest()->limit(8)->get(),
        ];
    }
}
