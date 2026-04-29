<?php

namespace App\Filament\Resources\WorkspaceSubscriptions\Tables;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WorkspaceSubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('workspace.name')
                    ->label('Workspace')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('plan.name')
                    ->label('Plano')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('provider')
                    ->label('Provedor')
                    ->badge()
                    ->searchable(),
                TextColumn::make('provider_reference')
                    ->label('Referencia')
                    ->limit(28)
                    ->searchable(),
                TextColumn::make('trial_ends_at')
                    ->label('Fim do trial')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('current_period_ends_at')
                    ->label('Renova em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('canceled_at')
                    ->label('Cancelada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Atualizada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'trialing' => 'Trial',
                        'pending' => 'Pendente',
                        'active' => 'Ativa',
                        'past_due' => 'Em atraso',
                        'canceled' => 'Cancelada',
                    ]),
                SelectFilter::make('provider')
                    ->options(['mercado_pago' => 'Mercado Pago']),
            ])
            ->recordActions([
                Action::make('activate')
                    ->label('Ativar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'status' => 'active',
                        'current_period_ends_at' => now()->addMonth(),
                    ])),
                Action::make('pending')
                    ->label('Pendente')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'pending'])),
                Action::make('cancel')
                    ->label('Cancelar')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'canceled', 'canceled_at' => now(), 'cancel_reason' => 'Cancelado pelo admin'])),
                EditAction::make(),
            ]);
    }
}
