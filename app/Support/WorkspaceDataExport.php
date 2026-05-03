<?php

namespace App\Support;

use App\Models\UsageInvoice;
use App\Models\WebhookEventNote;
use App\Models\WebhookEventTask;
use App\Models\Workspace;
use App\Models\WorkspaceUsageSnapshot;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkspaceDataExport
{
    public static function build(Workspace $workspace): array
    {
        $workspace->load(['members.user', 'subscription.plan', 'repositories', 'githubInstallations', 'secretRotations']);
        $eventIds = $workspace->webhookEvents()->pluck('id');

        return [
            'meta' => [
                'exported_at' => now()->toIso8601String(),
                'workspace_id' => $workspace->id,
                'workspace_uuid' => $workspace->uuid,
                'format_version' => '2026-04-29.1',
            ],
            'workspace' => [
                'name' => $workspace->name,
                'slug' => $workspace->slug,
                'uuid' => $workspace->uuid,
                'created_at' => optional($workspace->created_at)->toIso8601String(),
                'webhook_secret_rotated_at' => optional($workspace->webhook_secret_rotated_at)->toIso8601String(),
            ],
            'members' => $workspace->members->map(fn ($member) => [
                'name' => $member->user?->name,
                'email' => $member->user?->email,
                'role' => $member->role,
                'joined_at' => optional($member->created_at)->toIso8601String(),
            ])->values(),
            'subscription' => [
                'status' => $workspace->subscription?->status,
                'provider' => $workspace->subscription?->provider,
                'plan' => $workspace->subscription?->plan?->only(['name', 'slug', 'price_cents', 'currency', 'monthly_event_limit', 'event_retention_days', 'overage_price_cents']),
                'current_period_ends_at' => optional($workspace->subscription?->current_period_ends_at)->toIso8601String(),
            ],
            'repositories' => $workspace->repositories->map(fn ($repository) => $repository->only(['id', 'full_name', 'provider', 'private', 'default_branch', 'created_at', 'updated_at']))->values(),
            'github_installations' => $workspace->githubInstallations->map(fn ($installation) => $installation->only(['installation_id', 'account_login', 'account_type', 'permissions', 'events', 'installed_at']))->values(),
            'webhook_events' => $workspace->webhookEvents()->latest()->limit(1000)->get()->map(fn ($event) => [
                'id' => $event->id,
                'source' => $event->source,
                'event_name' => $event->event_name,
                'action' => $event->action,
                'delivery_id' => $event->delivery_id,
                'signature_valid' => $event->signature_valid,
                'validation_method' => $event->validation_method,
                'received_at' => optional($event->received_at)->toIso8601String(),
                'processed_at' => optional($event->processed_at)->toIso8601String(),
                'payload' => $event->payload,
            ])->values(),
            'notes' => WebhookEventNote::whereIn('webhook_event_id', $eventIds)->latest()->get()->map(fn ($note) => [
                'webhook_event_id' => $note->webhook_event_id,
                'user_id' => $note->user_id,
                'body' => $note->body,
                'created_at' => optional($note->created_at)->toIso8601String(),
            ])->values(),
            'tasks' => WebhookEventTask::whereIn('webhook_event_id', $eventIds)->latest()->get()->map(fn ($task) => [
                'webhook_event_id' => $task->webhook_event_id,
                'title' => $task->title,
                'status' => $task->status,
                'created_at' => optional($task->created_at)->toIso8601String(),
            ])->values(),
            'usage_snapshots' => class_exists(WorkspaceUsageSnapshot::class)
                ? WorkspaceUsageSnapshot::where('workspace_id', $workspace->id)->latest()->get()->map(fn ($snapshot) => $snapshot->toArray())->values()
                : [],
            'usage_invoices' => class_exists(UsageInvoice::class)
                ? UsageInvoice::where('workspace_id', $workspace->id)->latest()->get()->map(fn ($invoice) => $invoice->toArray())->values()
                : [],
        ];
    }

    public static function store(Workspace $workspace, ?string $output = null): string
    {
        $payload = self::build($workspace);
        $filename = $output ?: 'exports/workspace-'.$workspace->id.'-'.Str::slug($workspace->slug ?: $workspace->name).'-'.now()->format('Ymd-His').'.json';

        Storage::disk('local')->put($filename, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return storage_path('app/'.$filename);
    }
}
