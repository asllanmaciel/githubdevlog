<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class IncidentCenter extends Page
{
    protected static string $routePath = '/incident-center';

    protected static ?string $title = 'Centro de incidentes';

    protected static ?string $navigationLabel = 'Centro de incidentes';

    protected static string|UnitEnum|null $navigationGroup = 'Operacao';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?int $navigationSort = 35;

    protected string $view = 'filament.pages.incident-center';
}
