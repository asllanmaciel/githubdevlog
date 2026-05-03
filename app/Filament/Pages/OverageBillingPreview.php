<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class OverageBillingPreview extends Page
{
    protected static string $routePath = '/overage-billing';

    protected static ?string $title = 'Excedentes';

    protected static ?string $navigationLabel = 'Excedentes';

    protected static string|UnitEnum|null $navigationGroup = 'Financeiro';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?int $navigationSort = 21;

    protected string $view = 'filament.pages.overage-billing-preview';
}
