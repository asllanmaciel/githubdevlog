<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ProductMetricsPage extends Page
{
    protected static ?string $slug = 'product-metrics';

    protected static string $routePath = '/product-metrics';

    protected static ?string $title = 'Metricas de produto';

    protected static ?string $navigationLabel = 'Metricas de produto';

    protected static string|UnitEnum|null $navigationGroup = 'Operacao';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBarSquare;

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.product-metrics';
}
