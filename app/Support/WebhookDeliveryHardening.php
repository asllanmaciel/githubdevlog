<?php

namespace App\Support;

use App\Models\WebhookEvent;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class WebhookDeliveryHardening
{
    public static function dedupeKey(Workspace $workspace, string $source, ?string $deliveryId, string $rawBody): string
    {
        return hash('sha256', implode('|', [
            $workspace->id,
            $source,
            $deliveryId ?: hash('sha256', $rawBody),
        ]));
    }

    public static function duplicate(Workspace $workspace, string $source, ?string $deliveryId, string $rawBody): ?WebhookEvent
    {
        $query = WebhookEvent::where('workspace_id', $workspace->id)
            ->where('source', $source)
            ->where('signature_valid', true);

        if (self::hasColumn('status')) {
            $query->whereIn('status', ['accepted', 'processed']);
        }

        if ($deliveryId) {
            $query->where('delivery_id', $deliveryId);
        } elseif (self::hasColumn('dedupe_key')) {
            $query->where('dedupe_key', self::dedupeKey($workspace, $source, null, $rawBody));
        } else {
            return null;
        }

        return $query->first();
    }

    public static function acceptedAttributes(Workspace $workspace, Request $request, string $source, array $payload, string $rawBody, string $validationMethod): array
    {
        return self::withHardeningColumns([
            'source' => $source,
            'event_name' => (string) $request->header('X-GitHub-Event', $source),
            'action' => $payload['action'] ?? null,
            'delivery_id' => $request->header('X-GitHub-Delivery'),
            'signature_valid' => true,
            'validation_method' => $validationMethod,
            'headers' => WebhookSanitizer::clean(collect($request->headers->all())->map(fn ($value) => $value[0] ?? null)->all()),
            'payload' => WebhookSanitizer::clean($payload),
            'received_at' => now(),
            'processed_at' => now(),
        ], [
            'status' => 'accepted',
            'failure_reason' => null,
            'retry_count' => 0,
            'dedupe_key' => self::dedupeKey($workspace, $source, $request->header('X-GitHub-Delivery'), $rawBody),
        ]);
    }

    public static function recordRejected(Workspace $workspace, Request $request, string $source, string $rawBody, string $validationMethod, string $reason): WebhookEvent
    {
        return $workspace->webhookEvents()->create(self::withHardeningColumns([
            'source' => $source,
            'event_name' => (string) $request->header('X-GitHub-Event', $source),
            'action' => null,
            'delivery_id' => $request->header('X-GitHub-Delivery'),
            'signature_valid' => false,
            'validation_method' => $validationMethod,
            'headers' => WebhookSanitizer::clean(collect($request->headers->all())->map(fn ($value) => $value[0] ?? null)->all()),
            'payload' => [
                'rejected' => true,
                'reason' => $reason,
                'payload_sha256' => hash('sha256', $rawBody),
            ],
            'received_at' => now(),
        ], [
            'status' => 'rejected',
            'failure_reason' => $reason,
            'retry_count' => 0,
            'dedupe_key' => self::dedupeKey($workspace, $source, $request->header('X-GitHub-Delivery'), $rawBody),
        ]));
    }

    public static function report(): array
    {
        $total = WebhookEvent::count();
        $valid = WebhookEvent::where('signature_valid', true)->count();
        $invalid = WebhookEvent::where('signature_valid', false)->count();
        $lastDayInvalid = WebhookEvent::where('signature_valid', false)->where('created_at', '>=', now()->subDay())->count();

        return [
            'total' => $total,
            'accepted' => self::hasColumn('status') ? WebhookEvent::where('status', 'accepted')->count() : $valid,
            'rejected' => self::hasColumn('status') ? WebhookEvent::where('status', 'rejected')->count() : $invalid,
            'invalid_24h' => $lastDayInvalid,
            'valid_rate' => $total > 0 ? round(($valid / $total) * 100) : 100,
            'needs_attention' => $lastDayInvalid > 0,
            'latest_failures' => WebhookEvent::where('signature_valid', false)->latest()->limit(8)->get(),
        ];
    }

    private static function withHardeningColumns(array $base, array $hardening): array
    {
        foreach ($hardening as $column => $value) {
            if (self::hasColumn($column)) {
                $base[$column] = $value;
            }
        }

        return $base;
    }

    private static function hasColumn(string $column): bool
    {
        return Schema::hasColumn('webhook_events', $column);
    }
}
