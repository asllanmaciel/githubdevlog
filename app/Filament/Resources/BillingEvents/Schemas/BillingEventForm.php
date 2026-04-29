<?php

namespace App\Filament\Resources\BillingEvents\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BillingEventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('provider')->disabled(),
                TextInput::make('provider_event_id')->disabled(),
                TextInput::make('request_id')->disabled(),
                TextInput::make('event_type')->disabled(),
                TextInput::make('action')->disabled(),
                TextInput::make('resource_id')->disabled(),
                TextInput::make('external_reference')->disabled(),
                TextInput::make('status')->disabled(),
                Toggle::make('signature_valid')->disabled(),
                TextInput::make('workspace_id')->numeric()->disabled(),
                TextInput::make('workspace_subscription_id')->numeric()->disabled(),
                TextInput::make('billing_plan_id')->numeric()->disabled(),
                Textarea::make('error_message')->disabled()->columnSpanFull(),
                Textarea::make('payload')
                    ->formatStateUsing(fn ($state) => is_array($state)
                        ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                        : $state)
                    ->disabled()
                    ->columnSpanFull(),
                DateTimePicker::make('processed_at')->disabled(),
            ]);
    }
}
