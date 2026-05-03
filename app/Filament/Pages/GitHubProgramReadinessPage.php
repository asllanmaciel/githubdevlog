<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class GitHubProgramReadinessPage extends Page
{
    protected static string $routePath = '/github-program';

    protected static ?string $title = 'GitHub Program';

    protected static ?string $navigationLabel = 'GitHub Program';

    protected static string|UnitEnum|null $navigationGroup = 'Launch';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRocketLaunch;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.github-program-readiness';
}
