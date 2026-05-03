<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class GitHubSubmission extends Page
{
    protected static string $routePath = '/github-submission';

    protected static ?string $title = 'Submissao GitHub';

    protected static ?string $navigationLabel = 'Submissao GitHub';

    protected static string|UnitEnum|null $navigationGroup = 'Produto';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static ?int $navigationSort = 8;

    protected string $view = 'filament.pages.github-submission';
}
