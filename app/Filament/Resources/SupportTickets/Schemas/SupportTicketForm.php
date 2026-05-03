<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use App\Support\SupportSla;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('workspace_id')->numeric(),
                TextInput::make('user_id')->numeric(),
                TextInput::make('subject')->required()->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'open' => 'Aberto',
                        'triage' => 'Triagem',
                        'pending' => 'Aguardando',
                        'resolved' => 'Resolvido',
                    ])
                    ->required()
                    ->default('open'),
                Select::make('priority')
                    ->options(SupportSla::priorities())
                    ->required()
                    ->default('normal'),
                Select::make('category')
                    ->options(SupportSla::categories())
                    ->required()
                    ->default('technical'),
                DateTimePicker::make('first_response_due_at')->label('SLA primeira resposta'),
                DateTimePicker::make('resolution_due_at')->label('SLA resolucao'),
                DateTimePicker::make('responded_at')->label('Primeira resposta em'),
                DateTimePicker::make('resolved_at')->label('Resolvido em'),
                Textarea::make('message')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('internal_notes')
                    ->label('Notas internas')
                    ->columnSpanFull(),
            ]);
    }
}
