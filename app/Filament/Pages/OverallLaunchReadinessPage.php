<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class OverallLaunchReadinessPage extends Page
{
    protected static string $routePath = '/launch-overview';

    protected static ?string $title = 'Launch Overview';

    protected static ?string $navigationLabel = 'Launch Overview';

    protected static string | UnitEnum | null $navigationGroup = 'Launch';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 0;

    protected string $view = 'filament.pages.overall-launch-readiness';
}
