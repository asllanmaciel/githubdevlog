<?php

namespace App\Filament\Resources\WorkspaceUsageSnapshots;

use App\Filament\Resources\WorkspaceUsageSnapshots\Pages\ListWorkspaceUsageSnapshots;
use App\Models\WorkspaceUsageSnapshot;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class WorkspaceUsageSnapshotResource extends Resource
{
    protected static ?string $model = WorkspaceUsageSnapshot::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartPie;

    protected static ?string $navigationLabel = 'Historico de uso';

    protected static ?string $modelLabel = 'snapshot de uso';

    protected static ?string $pluralModelLabel = 'historico de uso';

    protected static \UnitEnum|string|null $navigationGroup = 'Financeiro';

    protected static ?int $navigationSort = 19;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('period', 'desc')
            ->columns([
                TextColumn::make('period')->label('Periodo')->sortable()->searchable(),
                TextColumn::make('workspace.name')->label('Workspace')->sortable()->searchable(),
                TextColumn::make('plan.name')->label('Plano')->placeholder('Free/fallback')->sortable(),
                TextColumn::make('events_count')->label('Eventos')->numeric()->sortable(),
                TextColumn::make('monthly_limit')->label('Limite')->numeric()->sortable(),
                TextColumn::make('usage_percent')->label('Uso %')->suffix('%')->numeric()->sortable(),
                TextColumn::make('overage_count')->label('Excedente')->numeric()->sortable(),
                TextColumn::make('captured_at')->label('Capturado em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('period')->label('Periodo')->options(fn () => WorkspaceUsageSnapshot::query()->orderByDesc('period')->pluck('period', 'period')->all()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListWorkspaceUsageSnapshots::route('/'),
        ];
    }
}