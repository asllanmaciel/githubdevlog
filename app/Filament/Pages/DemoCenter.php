<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DemoCenter extends Page
{
    protected static string $routePath = '/demo-center';

    protected static ?string $title = 'Centro de demo';

    protected static ?string $navigationLabel = 'Centro de demo';

    protected static string | UnitEnum | null $navigationGroup = 'Produto';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedPlayCircle;

    protected static ?int $navigationSort = 7;

    protected string $view = 'filament.pages.demo-center';
}
