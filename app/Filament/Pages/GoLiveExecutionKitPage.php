<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class GoLiveExecutionKitPage extends Page
{
    protected static string $routePath = '/go-live-kit';

    protected static ?string $title = 'Kit de Go-live';

    protected static ?string $navigationLabel = 'Kit de Go-live';

    protected static string|UnitEnum|null $navigationGroup = 'Launch';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.go-live-execution-kit';
}
