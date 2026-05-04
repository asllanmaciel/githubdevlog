<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class GitHubMarketplace extends Page
{
    protected static ?string $slug = 'github-marketplace';

    protected static string $routePath = '/github-marketplace';

    protected static ?string $title = 'Marketplace GitHub';

    protected static ?string $navigationLabel = 'Marketplace GitHub';

    protected static string|UnitEnum|null $navigationGroup = 'Produto';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?int $navigationSort = 9;

    protected string $view = 'filament.pages.github-marketplace';
}
