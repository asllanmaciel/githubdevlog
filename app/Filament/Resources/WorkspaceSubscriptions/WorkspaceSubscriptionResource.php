<?php

namespace App\Filament\Resources\WorkspaceSubscriptions;

use App\Filament\Resources\WorkspaceSubscriptions\Pages\EditWorkspaceSubscription;
use App\Filament\Resources\WorkspaceSubscriptions\Pages\ListWorkspaceSubscriptions;
use App\Filament\Resources\WorkspaceSubscriptions\Schemas\WorkspaceSubscriptionForm;
use App\Filament\Resources\WorkspaceSubscriptions\Tables\WorkspaceSubscriptionsTable;
use App\Models\WorkspaceSubscription;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WorkspaceSubscriptionResource extends Resource
{
    protected static ?string $model = WorkspaceSubscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static ?string $navigationLabel = 'Assinaturas';

    protected static ?string $modelLabel = 'assinatura';

    protected static ?string $pluralModelLabel = 'assinaturas';

    protected static \UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return WorkspaceSubscriptionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkspaceSubscriptionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkspaceSubscriptions::route('/'),
            'edit' => EditWorkspaceSubscription::route('/{record}/edit'),
        ];
    }
}
