<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\Env;

require_once __DIR__ . '/app/Core/Env.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Http/Controllers/GitHubWebhookController.php';
require_once __DIR__ . '/app/Services/GitHubService.php';
require_once __DIR__ . '/app/Services/OpenAIService.php';
require_once __DIR__ . '/app/Services/DevLogService.php';

spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

Env::load(__DIR__ . '/.env');
Database::connect();
