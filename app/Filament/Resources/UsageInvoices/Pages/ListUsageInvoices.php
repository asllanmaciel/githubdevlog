<?php

namespace App\Filament\Resources\UsageInvoices\Pages;

use App\Filament\Resources\UsageInvoices\UsageInvoiceResource;
use Filament\Resources\Pages\ListRecords;

class ListUsageInvoices extends ListRecords
{
    protected static string $resource = UsageInvoiceResource::class;
}
