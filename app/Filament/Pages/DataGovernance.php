<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DataGovernance extends Page
{
    protected static string $routePath = '/data-governance';

    protected static ?string $title = 'Governanca de dados';

    protected static ?string $navigationLabel = 'Governanca de dados';

    protected static string | UnitEnum | null $navigationGroup = 'Operacao';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?int $navigationSort = 32;

    protected string $view = 'filament.pages.data-governance';
}