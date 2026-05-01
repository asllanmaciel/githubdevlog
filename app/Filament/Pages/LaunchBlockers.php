<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LaunchBlockers extends Page
{
    protected static string $routePath = '/launch-blockers';

    protected static ?string $title = 'Bloqueadores';

    protected static ?string $navigationLabel = 'Bloqueadores';

    protected static string | UnitEnum | null $navigationGroup = 'Lancamento';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 11;

    protected string $view = 'filament.pages.launch-blockers';
}
