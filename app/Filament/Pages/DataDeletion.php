<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DataDeletion extends Page
{
    protected static string $routePath = '/data-deletion';

    protected static ?string $title = 'Exclusao de dados';

    protected static ?string $navigationLabel = 'Exclusao de dados';

    protected static string | UnitEnum | null $navigationGroup = 'Operacao';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedTrash;

    protected static ?int $navigationSort = 33;

    protected string $view = 'filament.pages.data-deletion';
}