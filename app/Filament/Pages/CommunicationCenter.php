<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class CommunicationCenter extends Page
{
    protected static string $routePath = '/communication-center';

    protected static ?string $title = 'Comunicacao transacional';

    protected static ?string $navigationLabel = 'Comunicacao';

    protected static string|UnitEnum|null $navigationGroup = 'Operacao';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?int $navigationSort = 33;

    protected string $view = 'filament.pages.communication-center';
}
