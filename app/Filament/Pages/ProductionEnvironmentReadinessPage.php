<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ProductionEnvironmentReadinessPage extends Page
{
    protected static string $routePath = '/production-env';

    protected static ?string $title = 'Ambiente produção';

    protected static ?string $navigationLabel = 'Ambiente produção';

    protected static string|UnitEnum|null $navigationGroup = 'Launch';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.production-environment-readiness';
}
