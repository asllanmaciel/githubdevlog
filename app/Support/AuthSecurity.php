<?php

namespace App\Support;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Schema;

class AuthSecurity
{
    public static function report(): array
    {
        if (! Schema::hasTable('audit_logs')) {
            return [
                'failed_24h' => 0,
                'success_24h' => 0,
                'registered_24h' => 0,
                'rate_limited_24h' => 0,
                'suspicious_ips' => collect(),
                'latest_events' => collect(),
                'healthy' => true,
            ];
        }

        $since = now()->subDay();
        $failed = AuditLog::where('action', 'auth.login_failed')->where('created_at', '>=', $since)->count();
        $rateLimited = AuditLog::whereIn('action', ['auth.login_rate_limited', 'auth.register_rate_limited'])->where('created_at', '>=', $since)->count();

        return [
            'failed_24h' => $failed,
            'success_24h' => AuditLog::where('action', 'auth.login_success')->where('created_at', '>=', $since)->count(),
            'registered_24h' => AuditLog::where('action', 'auth.registered')->where('created_at', '>=', $since)->count(),
            'rate_limited_24h' => $rateLimited,
            'suspicious_ips' => AuditLog::query()
                ->selectRaw('ip_address, count(*) as total')
                ->whereIn('action', ['auth.login_failed', 'auth.login_rate_limited', 'auth.register_rate_limited'])
                ->where('created_at', '>=', $since)
                ->whereNotNull('ip_address')
                ->groupBy('ip_address')
                ->having('total', '>=', 3)
                ->orderByDesc('total')
                ->limit(8)
                ->get(),
            'latest_events' => AuditLog::whereIn('action', ['auth.login_failed', 'auth.login_success', 'auth.registered', 'auth.login_rate_limited', 'auth.register_rate_limited'])
                ->latest('created_at')
                ->limit(10)
                ->get(),
            'healthy' => $failed < 25 && $rateLimited < 10,
        ];
    }
}
