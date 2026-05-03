<?php

namespace App\Filament\Pages;

use App\Models\BugReport;
use App\Support\BugMonitor as BugMonitorSupport;
use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class BugMonitor extends Page
{
    protected static string $routePath = '/bug-monitor';

    protected static ?string $title = 'Monitor de bugs';

    protected static ?string $navigationLabel = 'Monitor de bugs';

    protected static string|UnitEnum|null $navigationGroup = 'Operacao';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBugAnt;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.bug-monitor';

    public function getViewData(): array
    {
        return [
            'report' => BugMonitorSupport::report(),
        ];
    }

    public function resolveBug(int $bugId): void
    {
        BugReport::query()->whereKey($bugId)->update(['resolved_at' => now()]);

        Notification::make()
            ->title('Bug marcado como resolvido.')
            ->success()
            ->send();
    }

    public function reopenBug(int $bugId): void
    {
        BugReport::query()->whereKey($bugId)->update(['resolved_at' => null]);

        Notification::make()
            ->title('Bug reaberto.')
            ->warning()
            ->send();
    }
}
