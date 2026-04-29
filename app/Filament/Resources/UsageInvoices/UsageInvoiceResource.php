<?php

namespace App\Filament\Resources\UsageInvoices;

use App\Filament\Resources\UsageInvoices\Pages\ListUsageInvoices;
use App\Models\UsageInvoice;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UsageInvoiceResource extends Resource
{
    protected static ?string $model = UsageInvoice::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static ?string $navigationLabel = 'Faturas de uso';

    protected static ?string $modelLabel = 'fatura de uso';

    protected static ?string $pluralModelLabel = 'faturas de uso';

    protected static \UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 22;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('period')->label('Periodo')->sortable()->searchable(),
                TextColumn::make('workspace.name')->label('Workspace')->sortable()->searchable(),
                TextColumn::make('plan.name')->label('Plano')->placeholder('Sem plano')->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('overage_count')->label('Excedente')->numeric()->sortable(),
                TextColumn::make('overage_price_cents')->label('Preco/evento')->money('BRL', divideBy: 100)->sortable(),
                TextColumn::make('amount_cents')->label('Valor')->money('BRL', divideBy: 100)->sortable(),
                TextColumn::make('provider_reference')->label('Ref. provedor')->placeholder('Nao emitida')->searchable(),
                TextColumn::make('issued_at')->label('Emitida em')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('paid_at')->label('Paga em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('period')->label('Periodo')->options(fn () => UsageInvoice::query()->orderByDesc('period')->pluck('period', 'period')->all()),
                SelectFilter::make('status')->options([
                    'draft' => 'Rascunho',
                    'issued' => 'Emitida',
                    'paid' => 'Paga',
                    'void' => 'Cancelada',
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsageInvoices::route('/'),
        ];
    }
}