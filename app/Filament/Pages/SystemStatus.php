<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SystemStatus extends Page
{
    protected static string $routePath = '/system-status';

    protected static ?string $title = 'Status do sistema';

    protected static ?string $navigationLabel = 'Status do sistema';

    protected static string | UnitEnum | null $navigationGroup = 'Operacao';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedSignal;

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.system-status';
}
