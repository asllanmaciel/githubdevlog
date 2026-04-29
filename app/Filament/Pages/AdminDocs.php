<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AdminDocs extends Page
{
    protected static ?string $slug = 'docs';

    protected static ?string $title = 'Docs admin';

    protected static ?string $navigationLabel = 'Docs admin';

    protected static string | UnitEnum | null $navigationGroup = 'Produto';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 70;

    protected string $view = 'filament.pages.admin-docs';
}
