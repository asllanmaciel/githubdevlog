<?php

namespace App\Filament\Resources\WebhookEvents\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebhookEventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('workspace_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('repository_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('source')
                    ->searchable(),
                TextColumn::make('event_name')
                    ->searchable(),
                TextColumn::make('action')
                    ->searchable(),
                TextColumn::make('delivery_id')
                    ->searchable(),
                IconColumn::make('signature_valid')
                    ->boolean(),
                TextColumn::make('validation_method')
                    ->searchable(),
                TextColumn::make('received_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
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
