<?php

namespace App\Filament\Resources\KnowledgeBaseArticles;

use App\Filament\Resources\KnowledgeBaseArticles\Pages\CreateKnowledgeBaseArticle;
use App\Filament\Resources\KnowledgeBaseArticles\Pages\EditKnowledgeBaseArticle;
use App\Filament\Resources\KnowledgeBaseArticles\Pages\ListKnowledgeBaseArticles;
use App\Models\KnowledgeBaseArticle;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class KnowledgeBaseArticleResource extends Resource
{
    protected static ?string $model = KnowledgeBaseArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Base de conhecimento';

    protected static ?string $modelLabel = 'artigo';

    protected static ?string $pluralModelLabel = 'base de conhecimento';

    protected static \UnitEnum|string|null $navigationGroup = 'Operacao';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Titulo')
                ->required()
                ->maxLength(180)
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug((string) $state))),
            TextInput::make('slug')
                ->required()
                ->maxLength(180)
                ->unique(ignoreRecord: true),
            Select::make('category')
                ->label('Categoria')
                ->options([
                    'webhooks' => 'Webhooks',
                    'security' => 'Seguranca',
                    'billing' => 'Billing',
                    'github-app' => 'GitHub App',
                    'account' => 'Conta',
                ])
                ->required(),
            Textarea::make('summary')
                ->label('Resumo')
                ->rows(3)
                ->columnSpanFull(),
            Textarea::make('body')
                ->label('Conteudo')
                ->required()
                ->rows(12)
                ->columnSpanFull(),
            Toggle::make('published')
                ->label('Publicado'),
            TextInput::make('position')
                ->numeric()
                ->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('position')
            ->columns([
                TextColumn::make('title')->label('Titulo')->searchable()->sortable(),
                TextColumn::make('category')->label('Categoria')->badge()->searchable(),
                IconColumn::make('published')->label('Publicado')->boolean(),
                TextColumn::make('position')->label('Ordem')->numeric()->sortable(),
                TextColumn::make('updated_at')->label('Atualizado em')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                SelectFilter::make('category')->options([
                    'webhooks' => 'Webhooks',
                    'security' => 'Seguranca',
                    'billing' => 'Billing',
                    'github-app' => 'GitHub App',
                    'account' => 'Conta',
                ]),
                SelectFilter::make('published')->options(['1' => 'Publicado', '0' => 'Rascunho']),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKnowledgeBaseArticles::route('/'),
            'create' => CreateKnowledgeBaseArticle::route('/create'),
            'edit' => EditKnowledgeBaseArticle::route('/{record}/edit'),
        ];
    }
}
