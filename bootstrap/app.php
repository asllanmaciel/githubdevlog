<?php

use App\Console\Commands\DevlogBugMonitor;
use App\Console\Commands\DevlogCheckUsageLimits;
use App\Console\Commands\DevlogGenerateUsageInvoices;
use App\Console\Commands\DevlogPreflight;
use App\Console\Commands\DevlogSeedDemo;
use App\Console\Commands\DevlogSeedSubmissionAssets;
use App\Console\Commands\DevlogSnapshotUsage;
use App\Console\Commands\DevlogSyncKnowledgeBase;
use App\Http\Middleware\SecurityHeaders;
use App\Support\BugMonitor;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        DevlogPreflight::class,
        DevlogSeedDemo::class,
        DevlogSeedSubmissionAssets::class,
        DevlogCheckUsageLimits::class,
        DevlogSnapshotUsage::class,
        DevlogGenerateUsageInvoices::class,
        DevlogBugMonitor::class,
        DevlogSyncKnowledgeBase::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
        $middleware->validateCsrfTokens(except: ['webhooks/*']);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $exception): void {
            BugMonitor::capture($exception, request());
        });
    })->create();
