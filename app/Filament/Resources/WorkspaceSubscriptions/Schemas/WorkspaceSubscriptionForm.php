<?php

namespace App\Filament\Resources\WorkspaceSubscriptions\Schemas;

use App\Models\BillingPlan;
use App\Models\Workspace;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WorkspaceSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('workspace_id')
                    ->label('Workspace')
                    ->options(fn () => Workspace::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('billing_plan_id')
                    ->label('Plano')
                    ->options(fn () => BillingPlan::query()->orderBy('price_cents')->pluck('name', 'id'))
                    ->searchable(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'trialing' => 'Trial',
                        'pending' => 'Pendente',
                        'active' => 'Ativa',
                        'past_due' => 'Em atraso',
                        'canceled' => 'Cancelada',
                    ])
                    ->required(),
                TextInput::make('provider')
                    ->label('Provedor')
                    ->default('mercado_pago')
                    ->required(),
                TextInput::make('provider_reference')
                    ->label('Referencia do provedor')
                    ->maxLength(255),
                DateTimePicker::make('trial_ends_at')
                    ->label('Fim do trial'),
                DateTimePicker::make('current_period_ends_at')
                    ->label('Fim do periodo atual'),
            ]);
    }
}
