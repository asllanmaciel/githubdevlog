<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ProductionEnvironment extends Page
{
    protected static string $routePath = '/production-environment';

    protected static ?string $title = 'Ambiente de producao';

    protected static ?string $navigationLabel = 'Ambiente de producao';

    protected static string | UnitEnum | null $navigationGroup = 'Lancamento';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static ?int $navigationSort = 13;

    protected string $view = 'filament.pages.production-environment';
}