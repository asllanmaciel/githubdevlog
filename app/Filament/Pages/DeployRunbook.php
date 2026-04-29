<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class DeployRunbook extends Page
{
    protected static string $routePath = '/deploy-runbook';

    protected static ?string $title = 'Runbook de deploy';

    protected static ?string $navigationLabel = 'Runbook de deploy';

    protected static string | UnitEnum | null $navigationGroup = 'Produto';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.deploy-runbook';
}
