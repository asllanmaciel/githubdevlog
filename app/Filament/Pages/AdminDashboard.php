<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AdminDashboard extends Page
{
    protected static string $routePath = '/';

    protected static ?string $title = 'Visao geral';

    protected static ?string $navigationLabel = 'Visao geral';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?int $navigationSort = -2;

    protected string $view = 'filament.pages.admin-dashboard';
}
