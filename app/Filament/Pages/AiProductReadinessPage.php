<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AiProductReadinessPage extends Page
{
    protected static string $routePath = '/ai-product';

    protected static ?string $title = 'Produto AI';

    protected static ?string $navigationLabel = 'Produto AI';

    protected static string | UnitEnum | null $navigationGroup = 'Launch';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.ai-product-readiness';
}
