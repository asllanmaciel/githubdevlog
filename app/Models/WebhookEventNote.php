<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookEventNote extends Model
{
    protected $fillable = ['webhook_event_id', 'user_id', 'body'];
}
