<?php

namespace App\Filament\Resources\SecretRotations;

use App\Filament\Resources\SecretRotations\Pages\ListSecretRotations;
use App\Models\SecretRotation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SecretRotationResource extends Resource
{
    protected static ?string $model = SecretRotation::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedKey;

    protected static ?string $navigationLabel = 'Rotacao de secrets';

    protected static ?string $modelLabel = 'rotacao de secret';

    protected static ?string $pluralModelLabel = 'rotacoes de secrets';

    protected static \UnitEnum|string|null $navigationGroup = 'Operacao';

    protected static ?int $navigationSort = 3;

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('rotated_at', 'desc')
            ->columns([
                TextColumn::make('rotated_at')
                    ->label('Rotacionado em')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),
                TextColumn::make('secret_type')
                    ->label('Tipo')
                    ->badge()
                    ->searchable(),
                TextColumn::make('rotated_by')
                    ->label('Origem')
                    ->badge()
                    ->searchable(),
                TextColumn::make('workspace_id')
                    ->label('Workspace')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('user_id')
                    ->label('Usuario')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('secret_type')
                    ->options(['workspace_webhook_secret' => 'Workspace webhook secret']),
                SelectFilter::make('rotated_by')
                    ->options([
                        'user_dashboard' => 'Dashboard do usuario',
                        'admin_panel' => 'Painel admin',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSecretRotations::route('/'),
        ];
    }
}
