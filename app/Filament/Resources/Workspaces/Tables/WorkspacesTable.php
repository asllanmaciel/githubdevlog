<?php

namespace App\Filament\Resources\Workspaces\Tables;

use App\Models\SecretRotation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WorkspacesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('webhook_secret')
                    ->label('Webhook secret')
                    ->formatStateUsing(fn () => '••••••••••••')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('webhook_secret_rotated_at')
                    ->label('Secret rotacionado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('github_app_installation_id')
                    ->searchable(),
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
                Action::make('rotate_secret')
                    ->label('Rotacionar secret')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'webhook_secret' => 'dlog_'.\Illuminate\Support\Str::random(48),
                            'webhook_secret_rotated_at' => now(),
                        ]);

                        SecretRotation::create([
                            'workspace_id' => $record->id,
                            'user_id' => auth()->id(),
                            'secret_type' => 'workspace_webhook_secret',
                            'rotated_by' => 'admin_panel',
                            'metadata' => ['workspace' => $record->name],
                            'rotated_at' => now(),
                        ]);
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
