<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class AuditTrail extends Page
{
    protected static string $routePath = '/audit-trail';

    protected static ?string $title = 'Trilha de auditoria';

    protected static ?string $navigationLabel = 'Trilha de auditoria';

    protected static string | UnitEnum | null $navigationGroup = 'Operacao';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?int $navigationSort = 34;

    protected string $view = 'filament.pages.audit-trail';
}