<?php

namespace App\Filament\Resources\WorkspaceUsageSnapshots\Pages;

use App\Filament\Resources\WorkspaceUsageSnapshots\WorkspaceUsageSnapshotResource;
use Filament\Resources\Pages\ListRecords;

class ListWorkspaceUsageSnapshots extends ListRecords
{
    protected static string $resource = WorkspaceUsageSnapshotResource::class;
}
