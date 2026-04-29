<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LaunchGate extends Page
{
    protected static string $routePath = '/launch-gate';

    protected static ?string $title = 'Gate de lancamento';

    protected static ?string $navigationLabel = 'Gate de lancamento';

    protected static string | UnitEnum | null $navigationGroup = 'Lancamento';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.pages.launch-gate';
}