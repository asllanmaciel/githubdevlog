<?php

namespace App\Filament\Resources\BillingEvents;

use App\Filament\Resources\BillingEvents\Pages\EditBillingEvent;
use App\Filament\Resources\BillingEvents\Pages\ListBillingEvents;
use App\Filament\Resources\BillingEvents\Schemas\BillingEventForm;
use App\Filament\Resources\BillingEvents\Tables\BillingEventsTable;
use App\Models\BillingEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BillingEventResource extends Resource
{
    protected static ?string $model = BillingEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Eventos de cobranca';

    protected static ?string $modelLabel = 'evento de cobranca';

    protected static ?string $pluralModelLabel = 'eventos de cobranca';

    protected static \UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 20;

    public static function form(Schema $schema): Schema
    {
        return BillingEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BillingEventsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBillingEvents::route('/'),
            'edit' => EditBillingEvent::route('/{record}/edit'),
        ];
    }
}
