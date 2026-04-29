<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SupportOperations extends Page
{
    protected static string $routePath = '/support-operations';

    protected static ?string $title = 'Operacao de suporte';

    protected static ?string $navigationLabel = 'Operacao de suporte';

    protected static string | UnitEnum | null $navigationGroup = 'Operacao';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedLifebuoy;

    protected static ?int $navigationSort = 34;

    protected string $view = 'filament.pages.support-operations';
}
