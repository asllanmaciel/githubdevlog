<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class GoLiveReadiness extends Page
{
    protected static string $routePath = '/go-live';

    protected static ?string $title = 'Go-live';

    protected static ?string $navigationLabel = 'Go-live';

    protected static string | UnitEnum | null $navigationGroup = 'Launch';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.go-live-readiness';
}
