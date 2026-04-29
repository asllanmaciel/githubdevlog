<?php

namespace App\Filament\Resources\BillingEvents\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BillingEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Recebido em')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('provider')
                    ->label('Provedor')
                    ->badge()
                    ->searchable(),
                TextColumn::make('event_type')
                    ->label('Tipo')
                    ->searchable(),
                TextColumn::make('action')
                    ->label('Acao')
                    ->searchable(),
                TextColumn::make('resource_id')
                    ->label('Recurso')
                    ->searchable()
                    ->limit(24),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),
                IconColumn::make('signature_valid')
                    ->label('Assinatura')
                    ->boolean(),
                TextColumn::make('workspace_id')
                    ->label('Workspace')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('billing_plan_id')
                    ->label('Plano')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('processed_at')
                    ->label('Processado em')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('provider')
                    ->options(['mercado_pago' => 'Mercado Pago']),
                SelectFilter::make('status')
                    ->options([
                        'received' => 'Recebido',
                        'ignored' => 'Ignorado',
                        'pending_lookup' => 'Consulta pendente',
                        'unmatched' => 'Sem assinatura',
                        'processed_pending' => 'Processado pendente',
                        'processed_active' => 'Processado ativo',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
