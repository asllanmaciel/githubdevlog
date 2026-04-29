<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseArticle extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'summary',
        'body',
        'published',
        'position',
    ];

    protected function casts(): array
    {
        return ['published' => 'boolean'];
    }
}
