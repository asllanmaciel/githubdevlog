<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class LaunchCenter extends Page
{
    protected static string $routePath = '/launch-center';

    protected static ?string $title = 'Centro de lancamento';

    protected static ?string $navigationLabel = 'Centro de lancamento';

    protected static string | UnitEnum | null $navigationGroup = 'Produto';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.launch-center';
}
