<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * @var array<string, string>
     */
    private const SUPPORTED_LOCALES = [
        'pt-BR' => 'pt_BR',
        'pt_BR' => 'pt_BR',
        'pt' => 'pt_BR',
        'en' => 'en',
        'en-US' => 'en',
        'en_US' => 'en',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $requestedLocale = $request->query('lang');

        if (is_string($requestedLocale) && ($normalizedLocale = $this->normalizeLocale($requestedLocale))) {
            Session::put('locale', $normalizedLocale);
            Cookie::queue('locale', $normalizedLocale, 60 * 24 * 365);
        }

        $locale = $this->normalizeLocale((string) Session::get('locale'))
            ?? $this->normalizeLocale((string) $request->cookie('locale'))
            ?? $this->normalizeLocale((string) config('app.locale'))
            ?? 'pt_BR';

        App::setLocale($locale);

        return $next($request);
    }

    private function normalizeLocale(string $locale): ?string
    {
        return self::SUPPORTED_LOCALES[$locale] ?? null;
    }
}
