<?php

namespace App\Filament\Resources\BillingPlans;

use App\Filament\Resources\BillingPlans\Pages\CreateBillingPlan;
use App\Filament\Resources\BillingPlans\Pages\EditBillingPlan;
use App\Filament\Resources\BillingPlans\Pages\ListBillingPlans;
use App\Filament\Resources\BillingPlans\Schemas\BillingPlanForm;
use App\Filament\Resources\BillingPlans\Tables\BillingPlansTable;
use App\Models\BillingPlan;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BillingPlanResource extends Resource
{
    protected static ?string $model = BillingPlan::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Planos';

    protected static ?string $modelLabel = 'plano';

    protected static ?string $pluralModelLabel = 'planos';

    protected static \UnitEnum|string|null $navigationGroup = 'Comercial';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return BillingPlanForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BillingPlansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBillingPlans::route('/'),
            'create' => CreateBillingPlan::route('/create'),
            'edit' => EditBillingPlan::route('/{record}/edit'),
        ];
    }
}
