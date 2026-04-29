<?php

namespace App\Filament\Resources\SupportTickets\Tables;

use App\Support\SupportSla;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SupportTicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('subject')->searchable()->wrap(),
                TextColumn::make('workspace.name')->label('Workspace')->searchable(),
                TextColumn::make('category')->label('Categoria')->formatStateUsing(fn (?string $state) => SupportSla::categories()[$state] ?? $state)->searchable(),
                TextColumn::make('priority')->label('Prioridade')->badge()->searchable(),
                TextColumn::make('status')->label('Status')->badge()->searchable(),
                TextColumn::make('sla')->label('SLA')->state(fn ($record) => SupportSla::badge($record))->badge(),
                TextColumn::make('first_response_due_at')->label('1a resposta')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('resolution_due_at')->label('Resolucao')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('created_at')->label('Criado em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
