<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class MercadoPagoReadiness extends Page
{
    protected static ?string $slug = 'mercado-pago-readiness';

    protected static string $routePath = '/mercado-pago-readiness';

    protected static ?string $title = 'Mercado Pago';

    protected static ?string $navigationLabel = 'Mercado Pago';

    protected static string|UnitEnum|null $navigationGroup = 'Operacao';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.mercado-pago-readiness';
}
