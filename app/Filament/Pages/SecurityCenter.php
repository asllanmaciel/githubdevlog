<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SecurityCenter extends Page
{
    protected static string $routePath = '/security-center';

    protected static ?string $title = 'Centro de seguranca';

    protected static ?string $navigationLabel = 'Centro de seguranca';

    protected static string|UnitEnum|null $navigationGroup = 'Operacao';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.security-center';
}
