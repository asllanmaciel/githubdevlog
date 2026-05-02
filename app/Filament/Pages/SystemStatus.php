<?php

namespace App\Filament\Pages;

use App\Support\SystemHealth;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Log;
use UnitEnum;

class SystemStatus extends Page
{
    protected static string $routePath = '/system-status';

    protected static ?string $title = 'Status do sistema';

    protected static ?string $navigationLabel = 'Status do sistema';

    protected static string | UnitEnum | null $navigationGroup = 'Operacao';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSignal;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.system-status';

    public function getViewData(): array
    {
        return [
            'report' => $this->systemReport(),
        ];
    }

    private function systemReport(): array
    {
        try {
            return $this->normalizeReport(SystemHealth::report());
        } catch (\Throwable $exception) {
            Log::error('Falha ao montar status do sistema no admin.', [
                'exception' => $exception,
            ]);

            return $this->normalizeReport([
                'ok' => false,
                'app' => config('app.name'),
                'checked_at' => now()->toIso8601String(),
                'checks' => [
                    'system_status' => [
                        'ok' => false,
                        'label' => 'Status nao verificavel',
                        'detail' => $exception->getMessage(),
                    ],
                ],
            ]);
        }
    }

    private function normalizeReport(array $report): array
    {
        $checks = collect($report['checks'] ?? [])
            ->map(fn ($check) => [
                'ok' => (bool) ($check['ok'] ?? false),
                'label' => (string) ($check['label'] ?? 'Check sem label'),
                'detail' => is_scalar($check['detail'] ?? null)
                    ? (string) $check['detail']
                    : json_encode($check['detail'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            ])
            ->all();

        return [
            'ok' => (bool) ($report['ok'] ?? false),
            'app' => (string) ($report['app'] ?? config('app.name')),
            'checked_at' => (string) ($report['checked_at'] ?? now()->toIso8601String()),
            'checks' => $checks ?: [
                'system_status' => [
                    'ok' => false,
                    'label' => 'Status vazio',
                    'detail' => 'Nenhum check foi retornado.',
                ],
            ],
        ];
    }
}
