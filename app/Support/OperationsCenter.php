<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class OperationsCenter
{
    public static function report(): array
    {
        return [
            'environment' => self::environment(),
            'logs' => self::logs(),
            'migrations' => self::migrations(),
            'queues' => self::queues(),
            'cache_commands' => self::cacheCommands(),
        ];
    }

    private static function environment(): array
    {
        return [
            ['label' => 'APP_ENV', 'value' => app()->environment(), 'state' => app()->isProduction() ? 'ok' : 'atenção'],
            ['label' => 'APP_DEBUG', 'value' => config('app.debug') ? 'ativo' : 'desativado', 'state' => config('app.debug') ? 'atenção' : 'ok'],
            ['label' => 'APP_URL', 'value' => (string) config('app.url'), 'state' => str_starts_with((string) config('app.url'), 'https://') ? 'ok' : 'atenção'],
            ['label' => 'PHP', 'value' => PHP_VERSION, 'state' => 'ok'],
            ['label' => 'Laravel', 'value' => app()->version(), 'state' => 'ok'],
            ['label' => 'Queue', 'value' => (string) config('queue.default'), 'state' => config('queue.default') === 'sync' ? 'atenção' : 'ok'],
            ['label' => 'Mail', 'value' => (string) config('mail.default'), 'state' => 'info'],
            ['label' => 'Mercado Pago', 'value' => (string) config('services.mercado_pago.environment'), 'state' => config('services.mercado_pago.environment') === 'production' ? 'ok' : 'atenção'],
            ['label' => 'GitHub App', 'value' => filled(config('services.github_app.app_id')) ? 'configurado' : 'pendente', 'state' => filled(config('services.github_app.app_id')) ? 'ok' : 'atenção'],
        ];
    }

    private static function logs(): array
    {
        $path = storage_path('logs/laravel.log');

        if (! File::exists($path)) {
            return [
                'exists' => false,
                'path' => $path,
                'size' => 0,
                'updated_at' => null,
                'recent_errors' => [],
                'tail' => [],
            ];
        }

        $lines = collect(explode("\n", File::get($path)))->filter(fn ($line) => trim($line) !== '');
        $recentErrors = $lines
            ->filter(fn ($line) => str_contains($line, 'production.ERROR') || str_contains($line, 'local.ERROR'))
            ->take(-10)
            ->values()
            ->all();

        return [
            'exists' => true,
            'path' => $path,
            'size' => File::size($path),
            'updated_at' => date('d/m/Y H:i:s', File::lastModified($path)),
            'recent_errors' => $recentErrors,
            'tail' => $lines->take(-30)->values()->all(),
        ];
    }

    private static function migrations(): array
    {
        if (! Schema::hasTable('migrations')) {
            return ['total' => 0, 'latest_batch' => null, 'latest' => []];
        }

        $latestBatch = DB::table('migrations')->max('batch');
        $latest = DB::table('migrations')
            ->orderByDesc('batch')
            ->orderByDesc('migration')
            ->limit(12)
            ->get(['migration', 'batch'])
            ->map(fn ($migration) => ['name' => $migration->migration, 'batch' => $migration->batch])
            ->all();

        return [
            'total' => DB::table('migrations')->count(),
            'latest_batch' => $latestBatch,
            'latest' => $latest,
        ];
    }

    private static function queues(): array
    {
        return [
            'jobs' => Schema::hasTable('jobs') ? DB::table('jobs')->count() : null,
            'failed_jobs' => Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : null,
            'batches' => Schema::hasTable('job_batches') ? DB::table('job_batches')->count() : null,
        ];
    }

    private static function cacheCommands(): array
    {
        return [
            ['label' => 'Limpar tudo', 'method' => 'clearOptimize', 'command' => 'php artisan optimize:clear'],
            ['label' => 'Recriar config', 'method' => 'cacheConfig', 'command' => 'php artisan config:cache'],
            ['label' => 'Recriar rotas', 'method' => 'cacheRoutes', 'command' => 'php artisan route:cache'],
            ['label' => 'Recriar views', 'method' => 'cacheViews', 'command' => 'php artisan view:cache'],
        ];
    }
}
