<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class WebhookRetention extends Page
{
    protected static string $routePath = '/webhook-retention';

    protected static ?string $title = 'Retencao de webhooks';

    protected static ?string $navigationLabel = 'Retencao de webhooks';

    protected static string | UnitEnum | null $navigationGroup = 'Operacao';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedArchiveBox;

    protected static ?int $navigationSort = 31;

    protected string $view = 'filament.pages.webhook-retention';
}