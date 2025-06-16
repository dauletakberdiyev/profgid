<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Получаем локаль из сессии
        $locale = $request->session()->get('locale');
        
        // Проверяем, есть ли локаль в сессии и поддерживается ли она
        $supportedLocales = config('app.supported_locales', ['ru', 'kk']);
        
        if ($locale && in_array($locale, $supportedLocales)) {
            App::setLocale($locale);
        } else {
            // Устанавливаем локаль по умолчанию
            $defaultLocale = config('app.locale', 'ru');
            App::setLocale($defaultLocale);
            $request->session()->put('locale', $defaultLocale);
        }

        return $next($request);
    }
}
