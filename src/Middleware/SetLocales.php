<?php

namespace App\Http\Middleware;

use Closure;
use App;
use Date;

/**
 * Set proper locale to display user language or fallback.
 */

class SetLocales
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Set App and Date locales to the user's browser language
        $browser_lang = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
        if($browser_lang) {
            App::setLocale($browser_lang);
            Date::setLocale($browser_lang);
        }

        return $next($request);
    }
}
