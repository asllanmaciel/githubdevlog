<?php

namespace App\Filament\Resources\KnowledgeBaseArticles\Pages;

use App\Filament\Resources\KnowledgeBaseArticles\KnowledgeBaseArticleResource;
use Filament\Resources\Pages\ListRecords;

class ListKnowledgeBaseArticles extends ListRecords
{
    protected static string $resource = KnowledgeBaseArticleResource::class;
}
