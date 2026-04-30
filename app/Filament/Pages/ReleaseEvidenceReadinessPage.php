<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ReleaseEvidenceReadinessPage extends Page
{
    protected static string $routePath = '/release-evidence';

    protected static ?string $title = 'Evidências de release';

    protected static ?string $navigationLabel = 'Evidências de release';

    protected static string | UnitEnum | null $navigationGroup = 'Launch';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.release-evidence-readiness';
}