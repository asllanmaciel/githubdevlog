<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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

        if (is_string($requestedLocale) && isset(self::SUPPORTED_LOCALES[$requestedLocale])) {
            Session::put('locale', self::SUPPORTED_LOCALES[$requestedLocale]);
        }

        $locale = Session::get('locale', config('app.locale'));
        App::setLocale(in_array($locale, self::SUPPORTED_LOCALES, true) ? $locale : 'pt_BR');

        return $next($request);
    }
}
