<?php

namespace App\Support;

use App\Models\RoadmapItem;
use Illuminate\Support\Facades\Schema;

class PublicChangelog
{
    public static function entries(): array
    {
        if (! Schema::hasTable('roadmap_items')) {
            return [];
        }

        return RoadmapItem::where('status', 'done')
            ->orderByDesc('completed_at')
            ->orderByDesc('updated_at')
            ->limit(24)
            ->get()
            ->map(fn (RoadmapItem $item) => [
                'title' => $item->title,
                'area' => $item->area ?: 'Produto',
                'priority' => $item->priority ?: 'media',
                'description' => $item->description,
                'date' => optional($item->completed_at ?? $item->updated_at)->format('d/m/Y'),
            ])
            ->all();
    }
}
