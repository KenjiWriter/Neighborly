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
        $locale = $request->session()->get('locale');

        if ($locale && in_array($locale, ['en', 'pl'])) {
            App::setLocale($locale);
        } else {
             // Fallback to config if session is empty (or set default if needed)
             // Laravel naturally falls back to config.app.locale
        }

        return $next($request);
    }
}
