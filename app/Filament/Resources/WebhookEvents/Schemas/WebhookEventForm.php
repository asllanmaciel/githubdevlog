<?php

namespace App\Filament\Resources\WebhookEvents\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WebhookEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('workspace_id')
                    ->required()
                    ->numeric(),
                TextInput::make('repository_id')
                    ->numeric(),
                TextInput::make('source')
                    ->required()
                    ->default('github'),
                TextInput::make('event_name')
                    ->required(),
                TextInput::make('action'),
                TextInput::make('delivery_id'),
                TextInput::make('dedupe_key'),
                Toggle::make('signature_valid')
                    ->required(),
                TextInput::make('status')
                    ->default('accepted'),
                TextInput::make('failure_reason'),
                TextInput::make('retry_count')
                    ->numeric()
                    ->default(0),
                TextInput::make('validation_method'),
                TextInput::make('headers'),
                TextInput::make('payload'),
                DateTimePicker::make('received_at')
                    ->required(),
                DateTimePicker::make('processed_at'),
                DateTimePicker::make('last_retried_at'),
                DateTimePicker::make('reprocessed_at'),
            ]);
    }
}
