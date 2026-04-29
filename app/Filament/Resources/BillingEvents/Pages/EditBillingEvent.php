<?php

namespace App\Filament\Resources\BillingEvents\Pages;

use App\Filament\Resources\BillingEvents\BillingEventResource;
use Filament\Resources\Pages\EditRecord;

class EditBillingEvent extends EditRecord
{
    protected static string $resource = BillingEventResource::class;
}
