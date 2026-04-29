<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SubmissionAssetsPage extends Page
{
    protected static string $routePath = '/submission-assets';

    protected static ?string $title = 'Assets de submissao';

    protected static ?string $navigationLabel = 'Assets de submissao';

    protected static string | UnitEnum | null $navigationGroup = 'Lancamento';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?int $navigationSort = 16;

    protected string $view = 'filament.pages.submission-assets';
}