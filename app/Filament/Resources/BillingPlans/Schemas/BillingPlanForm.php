<?php

namespace App\Filament\Resources\BillingPlans\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BillingPlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('price_cents')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('currency')
                    ->required()
                    ->default('BRL'),
                TextInput::make('event_retention_days')
                    ->required()
                    ->numeric()
                    ->default(30),
                TextInput::make('monthly_event_limit')
                    ->required()
                    ->numeric()
                    ->default(1000),
                TextInput::make('overage_price_cents')
                    ->label('Preco por excedente (centavos)')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('features'),
                Toggle::make('active')
                    ->required(),
            ]);
    }
}
