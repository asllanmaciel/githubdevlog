<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use UnitEnum;

class OperationsCenter extends Page
{
    protected static string $routePath = '/operations-center';

    protected static ?string $title = 'Operação';

    protected static ?string $navigationLabel = 'Operação';

    protected static string|UnitEnum|null $navigationGroup = 'Operação';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCommandLine;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.operations-center';

    public function clearOptimize(): void
    {
        $this->runArtisan('optimize:clear', 'Caches limpos com sucesso.');
    }

    public function cacheConfig(): void
    {
        $this->runArtisan('config:cache', 'Cache de configuração recriado.');
    }

    public function cacheRoutes(): void
    {
        $this->runArtisan('route:cache', 'Cache de rotas recriado.');
    }

    public function cacheViews(): void
    {
        $this->runArtisan('view:cache', 'Cache de views recriado.');
    }

    private function runArtisan(string $command, string $message): void
    {
        try {
            Artisan::call($command);

            Notification::make()
                ->title($message)
                ->success()
                ->send();
        } catch (\Throwable $exception) {
            Notification::make()
                ->title('Falha ao executar '.$command)
                ->body($exception->getMessage())
                ->danger()
                ->send();
        }
    }
}
