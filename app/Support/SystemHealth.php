<?php

namespace App\Support;

use App\Models\BillingEvent;
use App\Models\WebhookEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class SystemHealth
{
    public static function report(): array
    {
        $checks = [
            'database' => self::database(),
            'storage' => self::storage(),
            'queue' => self::queue(),
            'billing' => self::billing(),
            'github_webhooks' => self::githubWebhooks(),
            'environment' => self::environment(),
        ];

        $ok = collect($checks)->every(fn ($check) => $check['ok']);

        return [
            'ok' => $ok,
            'app' => config('app.name'),
            'checked_at' => now()->toIso8601String(),
            'checks' => $checks,
        ];
    }

    private static function database(): array
    {
        try {
            DB::select('select 1');

            return self::ok('Banco conectado', config('database.default'));
        } catch (\Throwable $exception) {
            return self::fail('Banco indisponivel', $exception->getMessage());
        }
    }

    private static function storage(): array
    {
        try {
            $path = 'healthcheck.txt';
            Storage::disk('local')->put($path, now()->toIso8601String());
            Storage::disk('local')->delete($path);

            return self::ok('Storage local gravavel', 'disk local');
        } catch (\Throwable $exception) {
            return self::fail('Storage local indisponivel', $exception->getMessage());
        }
    }

    private static function queue(): array
    {
        $connection = config('queue.default');

        try {
            $pending = Schema::hasTable('jobs') ? DB::table('jobs')->count() : 0;
            $failed = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : 0;

            return [
                'ok' => $failed === 0,
                'label' => $failed === 0 ? 'Fila sem falhas registradas' : 'Fila com jobs falhos',
                'detail' => 'driver '.$connection.'; pendentes '.$pending.'; falhos '.$failed,
            ];
        } catch (\Throwable $exception) {
            return self::fail('Fila nao verificavel', $exception->getMessage());
        }
    }

    private static function billing(): array
    {
        $attention = BillingEvent::whereIn('status', ['pending_lookup', 'unmatched'])->count();
        $today = BillingEvent::whereDate('created_at', now()->toDateString())->count();

        return [
            'ok' => $attention === 0,
            'label' => $attention === 0 ? 'Billing sem pendencias criticas' : 'Billing com eventos pendentes',
            'detail' => $today.' evento(s) hoje; '.$attention.' exigem atencao',
        ];
    }

    private static function githubWebhooks(): array
    {
        $valid = WebhookEvent::where('signature_valid', true)->count();
        $invalid = WebhookEvent::where('signature_valid', false)->count();

        return [
            'ok' => $invalid === 0,
            'label' => $invalid === 0 ? 'Webhooks GitHub validos' : 'Ha webhooks com assinatura invalida',
            'detail' => $valid.' valido(s); '.$invalid.' invalido(s)',
        ];
    }

    private static function environment(): array
    {
        $isProductionReady = ! config('app.debug') && str_starts_with((string) config('app.url'), 'https://');

        return [
            'ok' => app()->isLocal() || $isProductionReady,
            'label' => app()->isLocal() ? 'Ambiente local' : ($isProductionReady ? 'Ambiente de producao seguro' : 'Ambiente de producao incompleto'),
            'detail' => 'env '.app()->environment().'; debug '.(config('app.debug') ? 'on' : 'off').'; url '.config('app.url'),
        ];
    }

    private static function ok(string $label, string $detail): array
    {
        return ['ok' => true, 'label' => $label, 'detail' => $detail];
    }

    private static function fail(string $label, string $detail): array
    {
        return ['ok' => false, 'label' => $label, 'detail' => $detail];
    }
}
