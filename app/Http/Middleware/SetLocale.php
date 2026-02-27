<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Date;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowedLocales = ['ps', 'en'];
        $defaultLocale = config('app.locale', 'ps');
        $sessionLocale = $request->session()->get('locale');
        $isLocaleManuallySelected = (bool) $request->session()->get('locale_manually_selected', false);

        $locale = $defaultLocale;

        if ($isLocaleManuallySelected && is_string($sessionLocale) && in_array($sessionLocale, $allowedLocales, true)) {
            $locale = $sessionLocale;
        }

        App::setLocale($locale);
        Date::setLocale($locale);

        return $next($request);
    }
}
