<?php

namespace App\Filament\Resources\Workspaces\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WorkspaceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('uuid')
                    ->label('UUID')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('webhook_secret')
                    ->required(),
                TextInput::make('github_app_installation_id'),
            ]);
    }
}
