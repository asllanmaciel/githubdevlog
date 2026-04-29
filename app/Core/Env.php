<?php

declare(strict_types=1);

namespace App\Core;

final class Env
{
    private static array $values = [];
    private static bool $loaded = false;

    public static function load(string $path): void
    {
        if (self::$loaded) {
            return;
        }

        self::$loaded = true;

        if (!is_file($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }
            if (!str_contains($trimmed, '=')) {
                continue;
            }
            [$key, $value] = array_map('trim', explode('=', $trimmed, 2));
            if ($key === '') {
                continue;
            }
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
            }
            self::$values[$key] = $value;
            putenv("$key=$value");
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, $_ENV)) {
            return $_ENV[$key];
        }
        return self::$values[$key] ?? $default;
    }
}
