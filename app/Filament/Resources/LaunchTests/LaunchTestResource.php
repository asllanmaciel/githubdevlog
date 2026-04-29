<?php

namespace App\Filament\Resources\LaunchTests;

use App\Filament\Resources\LaunchTests\Pages\CreateLaunchTest;
use App\Filament\Resources\LaunchTests\Pages\EditLaunchTest;
use App\Filament\Resources\LaunchTests\Pages\ListLaunchTests;
use App\Models\LaunchTest;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LaunchTestResource extends Resource
{
    protected static ?string $model = LaunchTest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'QA de lancamento';

    protected static ?string $modelLabel = 'teste de lancamento';

    protected static ?string $pluralModelLabel = 'QA de lancamento';

    protected static \UnitEnum|string|null $navigationGroup = 'Produto';

    protected static ?int $navigationSort = 9;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->label('Titulo')->required()->maxLength(180),
            Select::make('area')->options([
                'Infra' => 'Infra',
                'Billing' => 'Billing',
                'GitHub App' => 'GitHub App',
                'Webhooks' => 'Webhooks',
                'Security' => 'Security',
                'Support' => 'Support',
                'Demo' => 'Demo',
            ])->required(),
            Select::make('priority')->options([
                'high' => 'Alta',
                'medium' => 'Media',
                'low' => 'Baixa',
            ])->required(),
            Select::make('status')->options([
                'pending' => 'Pendente',
                'running' => 'Em execucao',
                'passed' => 'Aprovado',
                'failed' => 'Falhou',
                'blocked' => 'Bloqueado',
            ])->required(),
            Textarea::make('instructions')->label('Instrucoes')->rows(5)->columnSpanFull(),
            Textarea::make('expected_result')->label('Resultado esperado')->rows(4)->columnSpanFull(),
            Textarea::make('evidence')->label('Evidencia')->rows(5)->columnSpanFull(),
            TextInput::make('executed_by')->label('Executado por'),
            DateTimePicker::make('executed_at')->label('Executado em'),
            TextInput::make('position')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->columns([
                TextColumn::make('position')->label('#')->numeric()->sortable(),
                TextColumn::make('title')->label('Teste')->searchable()->sortable(),
                TextColumn::make('area')->badge()->searchable(),
                TextColumn::make('priority')->label('Prioridade')->badge()->sortable(),
                TextColumn::make('status')->label('Status')->badge()->sortable(),
                TextColumn::make('executed_at')->label('Executado em')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('executed_by')->label('Por')->searchable(),
            ])
            ->filters([
                SelectFilter::make('area')->options([
                    'Infra' => 'Infra',
                    'Billing' => 'Billing',
                    'GitHub App' => 'GitHub App',
                    'Webhooks' => 'Webhooks',
                    'Security' => 'Security',
                    'Support' => 'Support',
                    'Demo' => 'Demo',
                ]),
                SelectFilter::make('status')->options([
                    'pending' => 'Pendente',
                    'running' => 'Em execucao',
                    'passed' => 'Aprovado',
                    'failed' => 'Falhou',
                    'blocked' => 'Bloqueado',
                ]),
            ])
            ->recordActions([
                Action::make('pass')
                    ->label('Aprovar')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'status' => 'passed',
                        'executed_by' => auth()->user()?->email,
                        'executed_at' => now(),
                    ])),
                Action::make('fail')
                    ->label('Falhou')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'status' => 'failed',
                        'executed_by' => auth()->user()?->email,
                        'executed_at' => now(),
                    ])),
                Action::make('block')
                    ->label('Bloquear')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update([
                        'status' => 'blocked',
                        'executed_by' => auth()->user()?->email,
                        'executed_at' => now(),
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLaunchTests::route('/'),
            'create' => CreateLaunchTest::route('/create'),
            'edit' => EditLaunchTest::route('/{record}/edit'),
        ];
    }
}
