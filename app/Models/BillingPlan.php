<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingPlan extends Model
{
    protected $fillable = ['name', 'slug', 'price_cents', 'currency', 'event_retention_days', 'monthly_event_limit', 'features', 'active'];

    protected function casts(): array
    {
        return ['features' => 'array', 'active' => 'boolean'];
    }
}