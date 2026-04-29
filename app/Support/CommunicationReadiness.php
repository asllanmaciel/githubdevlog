<?php

namespace App\Support;

use App\Models\WorkspaceInvite;
use Illuminate\Support\Facades\Schema;

class CommunicationReadiness
{
    public static function report(): array
    {
        $mailer = (string) config('mail.default');
        $fromAddress = (string) config('mail.from.address');
        $fromName = (string) config('mail.from.name');
        $queue = (string) config('queue.default');
        $appUrl = (string) config('app.url');

        $checks = collect([
            self::check('Mailer definido', filled($mailer), 'MAIL_MAILER='.$mailer, 'config'),
            self::check('Remetente valido', filled($fromAddress) && ! str_contains($fromAddress, 'example.com'), 'MAIL_FROM_ADDRESS='.$fromAddress, 'config'),
            self::check('Nome do remetente', filled($fromName), 'MAIL_FROM_NAME='.$fromName, 'config'),
            self::check('URL publica configurada', str_starts_with($appUrl, 'https://') || app()->isLocal(), 'APP_URL='.$appUrl, 'config'),
            self::check('Fila preparada', in_array($queue, ['database', 'redis', 'sqs', 'sync'], true), 'QUEUE_CONNECTION='.$queue, 'queue'),
            self::check('Tabela de convites', Schema::hasTable('workspace_invites'), 'workspace_invites', 'database'),
        ]);

        $pendingInvites = Schema::hasTable('workspace_invites')
            ? WorkspaceInvite::where('status', 'pending')->count()
            : 0;
        $failedDeliveries = Schema::hasTable('workspace_invites')
            ? WorkspaceInvite::where('status', 'pending')->whereNotNull('delivery_error')->count()
            : 0;

        return [
            'ready' => $checks->where('done', false)->isEmpty(),
            'percent' => (int) round(($checks->where('done', true)->count() / max($checks->count(), 1)) * 100),
            'checks' => $checks,
            'metrics' => [
                'pending_invites' => $pendingInvites,
                'failed_deliveries' => $failedDeliveries,
                'mailer' => $mailer,
                'from_address' => $fromAddress,
                'queue' => $queue,
            ],
            'next_steps' => self::nextSteps($checks, $failedDeliveries),
        ];
    }

    private static function check(string $title, bool $done, string $detail, string $area): array
    {
        return compact('title', 'done', 'detail', 'area');
    }

    private static function nextSteps($checks, int $failedDeliveries): array
    {
        $steps = $checks
            ->where('done', false)
            ->map(fn ($check) => 'Corrigir: '.$check['title'].' ('.$check['detail'].')')
            ->values()
            ->all();

        if ($failedDeliveries > 0) {
            $steps[] = 'Reenviar ou copiar manualmente links de '.$failedDeliveries.' convite(s) com falha de envio.';
        }

        if ($steps === []) {
            $steps[] = 'Comunicacao pronta para beta. Antes do lancamento, disparar convite real de ponta a ponta.';
        }

        return $steps;
    }
}