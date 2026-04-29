<?php

namespace App\Filament\Resources\SecretRotations\Pages;

use App\Filament\Resources\SecretRotations\SecretRotationResource;
use Filament\Resources\Pages\ListRecords;

class ListSecretRotations extends ListRecords
{
    protected static string $resource = SecretRotationResource::class;
}
