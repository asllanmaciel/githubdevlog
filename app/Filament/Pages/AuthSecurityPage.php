<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AuthSecurityPage extends Page
{
    protected static ?string $slug = 'auth-security';

    protected static string $routePath = '/auth-security';

    protected static ?string $title = 'Seguranca de autenticacao';

    protected static ?string $navigationLabel = 'Seguranca de login';

    protected static string|UnitEnum|null $navigationGroup = 'Seguranca';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.auth-security';
}
