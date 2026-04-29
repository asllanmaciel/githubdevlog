<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class UsageMonitor extends Page
{
    protected static string $routePath = '/usage-monitor';

    protected static ?string $title = 'Uso e limites';

    protected static ?string $navigationLabel = 'Uso e limites';

    protected static string | UnitEnum | null $navigationGroup = 'Financeiro';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?int $navigationSort = 18;

    protected string $view = 'filament.pages.usage-monitor';
}