<?php

namespace App\Support;

use App\Models\BugReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class BugMonitor
{
    public static function capture(Throwable $exception, ?Request $request = null): ?BugReport
    {
        if (! self::shouldCapture($exception)) {
            return null;
        }

        try {
            if (! Schema::hasTable('bug_reports')) {
                return null;
            }

            $now = now();
            $fingerprint = self::fingerprint($exception, $request);
            $payload = self::payload($exception, $request, $now);

            return DB::transaction(function () use ($fingerprint, $payload, $now) {
                $report = BugReport::where('fingerprint', $fingerprint)->lockForUpdate()->first();

                if ($report) {
                    $report->fill([
                        ...$payload,
                        'occurrences' => $report->occurrences + 1,
                        'last_seen_at' => $now,
                        'resolved_at' => null,
                    ])->save();

                    return $report;
                }

                return BugReport::create([
                    'fingerprint' => $fingerprint,
                    'occurrences' => 1,
                    'first_seen_at' => $now,
                    'last_seen_at' => $now,
                    ...$payload,
                ]);
            });
        } catch (Throwable) {
            return null;
        }
    }

    public static function report(): array
    {
        if (! Schema::hasTable('bug_reports')) {
            return [
                'available' => false,
                'open_count' => 0,
                'resolved_count' => 0,
                'today_count' => 0,
                'latest' => collect(),
                'top' => collect(),
            ];
        }

        return [
            'available' => true,
            'open_count' => BugReport::open()->count(),
            'resolved_count' => BugReport::whereNotNull('resolved_at')->count(),
            'today_count' => BugReport::where('last_seen_at', '>=', now()->startOfDay())->count(),
            'latest' => BugReport::query()->latest('last_seen_at')->limit(12)->get(),
            'top' => BugReport::open()->orderByDesc('occurrences')->orderByDesc('last_seen_at')->limit(6)->get(),
        ];
    }

    private static function shouldCapture(Throwable $exception): bool
    {
        if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() < 500) {
            return false;
        }

        return true;
    }

    private static function fingerprint(Throwable $exception, ?Request $request): string
    {
        return hash('sha256', implode('|', [
            $exception::class,
            self::trimPath($exception->getFile()),
            $exception->getLine(),
            $request?->route()?->getName() ?? $request?->path() ?? 'console',
        ]));
    }

    private static function payload(Throwable $exception, ?Request $request, $now): array
    {
        $route = $request?->route();
        $routeName = $route?->getName() ?: $route?->uri();

        return [
            'level' => self::level($exception),
            'exception_class' => $exception::class,
            'message' => Str::limit($exception->getMessage() ?: 'Sem mensagem', 1000, ''),
            'file' => self::trimPath($exception->getFile()),
            'line' => $exception->getLine(),
            'method' => $request?->method(),
            'url' => $request ? Str::limit($request->fullUrl(), 1000, '') : null,
            'route' => $routeName ? Str::limit($routeName, 255, '') : null,
            'user_id' => Auth::id(),
            'ip_hash' => $request?->ip() ? hash('sha256', $request->ip().config('app.key')) : null,
            'last_seen_at' => $now,
            'context' => [
                'env' => app()->environment(),
                'user_agent' => $request ? Str::limit((string) $request->userAgent(), 255, '') : null,
                'command' => app()->runningInConsole() ? implode(' ', $_SERVER['argv'] ?? []) : null,
                'trace' => collect($exception->getTrace())->take(8)->map(fn ($frame) => [
                    'file' => isset($frame['file']) ? self::trimPath($frame['file']) : null,
                    'line' => $frame['line'] ?? null,
                    'function' => $frame['function'] ?? null,
                    'class' => $frame['class'] ?? null,
                ])->all(),
            ],
        ];
    }

    private static function level(Throwable $exception): string
    {
        if ($exception instanceof HttpExceptionInterface && $exception->getStatusCode() >= 500) {
            return 'critical';
        }

        return app()->isProduction() ? 'error' : 'warning';
    }

    private static function trimPath(string $path): string
    {
        return Str::replaceFirst(base_path().DIRECTORY_SEPARATOR, '', $path);
    }
}
