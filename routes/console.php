<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('devlog:check-usage-limits')->hourly();

Schedule::command('devlog:snapshot-usage')->dailyAt('23:50');
Schedule::command('devlog:prune-webhook-events')->dailyAt('02:30');
