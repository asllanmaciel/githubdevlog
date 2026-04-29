<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class GitHubReadiness extends Page
{
    protected static string $routePath = '/github-readiness';

    protected static ?string $title = 'Prontidão GitHub';

    protected static ?string $navigationLabel = 'Prontidão GitHub';

    protected static string | UnitEnum | null $navigationGroup = 'Lançamento';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.pages.github-readiness';
}
