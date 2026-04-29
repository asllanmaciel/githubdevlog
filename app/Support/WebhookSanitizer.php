<?php

namespace App\Support;

class WebhookSanitizer
{
    private const SENSITIVE_KEYS = [
        'authorization',
        'cookie',
        'password',
        'secret',
        'token',
        'access_token',
        'refresh_token',
        'client_secret',
        'private_key',
        'x-hub-signature',
        'x-hub-signature-256',
    ];

    public static function clean(array $data): array
    {
        return self::walk($data);
    }

    private static function walk(array $data): array
    {
        foreach ($data as $key => $value) {
            if (self::isSensitiveKey((string) $key)) {
                $data[$key] = '[redacted]';
                continue;
            }

            if (is_array($value)) {
                $data[$key] = self::walk($value);
            }
        }

        return $data;
    }

    private static function isSensitiveKey(string $key): bool
    {
        $normalized = strtolower(str_replace(['-', '_'], '', $key));

        foreach (self::SENSITIVE_KEYS as $sensitiveKey) {
            $candidate = strtolower(str_replace(['-', '_'], '', $sensitiveKey));

            if ($normalized === $candidate || str_contains($normalized, $candidate)) {
                return true;
            }
        }

        return false;
    }
}
