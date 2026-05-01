<?php

namespace App\Filament\Pages;

use App\Support\RoadmapCatalog;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class Roadmap extends Page
{
    protected static string $routePath = '/roadmap';

    protected static ?string $title = 'Roadmap visual';

    protected static ?string $navigationLabel = 'Roadmap visual';

    protected static string | UnitEnum | null $navigationGroup = 'Produto';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedPresentationChartLine;

    protected static ?int $navigationSort = 60;

    protected string $view = 'filament.pages.roadmap';

    public function mount(): void
    {
        RoadmapCatalog::sync();
    }
}
