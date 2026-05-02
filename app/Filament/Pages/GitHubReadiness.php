<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class GitHubReadiness extends Page
{
    protected static ?string $slug = 'github-readiness';

    protected static string $routePath = '/github-readiness';

    protected static ?string $title = 'Prontidao GitHub';

    protected static ?string $navigationLabel = 'Prontidao GitHub';

    protected static string | UnitEnum | null $navigationGroup = 'Operacao';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.github-readiness';
}
