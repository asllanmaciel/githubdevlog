<?php

namespace App\Filament\Resources\UsageInvoices;

use App\Filament\Resources\UsageInvoices\Pages\ListUsageInvoices;
use App\Models\UsageInvoice;
use App\Services\MercadoPagoBillingService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

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
                TextColumn::make('metadata.checkout_url')->label('Checkout')->placeholder('Nao emitido')->limit(38)->copyable(),
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
            ])
            ->recordActions([
                Action::make('issue')
                    ->label('Emitir Mercado Pago')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (UsageInvoice $record) => $record->status === 'draft' && $record->amount_cents > 0)
                    ->action(function (UsageInvoice $record, MercadoPagoBillingService $billing) {
                        $payerEmail = $record->workspace?->users()->first()?->email
                            ?? Auth::user()?->email
                            ?? 'billing@example.com';
                        $preference = $billing->createUsageInvoicePreference($record, $payerEmail);

                        $record->update([
                            'status' => 'issued',
                            'provider' => 'mercado_pago',
                            'provider_reference' => $preference->id ?? null,
                            'issued_at' => now(),
                            'metadata' => array_merge($record->metadata ?? [], [
                                'checkout_url' => $billing->checkoutUrl($preference),
                                'issued_by' => Auth::user()?->email,
                            ]),
                        ]);
                    }),
                Action::make('void')
                    ->label('Cancelar')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (UsageInvoice $record) => in_array($record->status, ['draft', 'issued'], true))
                    ->action(fn (UsageInvoice $record) => $record->update([
                        'status' => 'void',
                        'metadata' => array_merge($record->metadata ?? [], [
                            'voided_by' => Auth::user()?->email,
                            'voided_at' => now()->toIso8601String(),
                        ]),
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsageInvoices::route('/'),
        ];
    }
}
