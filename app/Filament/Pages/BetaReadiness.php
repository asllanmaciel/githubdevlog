<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class BetaReadiness extends Page
{
    protected static string $routePath = '/beta-readiness';

    protected static ?string $title = 'Prontidao beta';

    protected static ?string $navigationLabel = 'Prontidao beta';

    protected static string|UnitEnum|null $navigationGroup = 'Launch';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.beta-readiness';
}
