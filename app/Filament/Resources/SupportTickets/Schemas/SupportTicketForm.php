<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('workspace_id')
                    ->numeric(),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('subject')
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->default('open'),
                TextInput::make('priority')
                    ->required()
                    ->default('normal'),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                DateTimePicker::make('resolved_at'),
            ]);
    }
}
