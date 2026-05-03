<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class WebhookHardeningPage extends Page
{
    protected static ?string $slug = 'webhook-hardening';

    protected static string $routePath = '/webhook-hardening';

    protected static ?string $title = 'Hardening de webhooks';

    protected static ?string $navigationLabel = 'Hardening de webhooks';

    protected static string | UnitEnum | null $navigationGroup = 'Operacao';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedShieldCheck;

    protected static ?int $navigationSort = 6;

    protected string $view = 'filament.pages.webhook-hardening';
}
