<?php

namespace Nonoesp\Folio\Middleware;

use Closure;
use App;
use Date;

/**
 * Set proper locales to display user language or fallback.
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
        // App
        $browser_lang = substr($request->server('HTTP_ACCEPT_LANGUAGE'), 0, 2);
        if($browser_lang) {
            App::setLocale($browser_lang);
            // If app locale is Folio's second locale
            // Set app.fallback_locale to Folio's first locale
            $folioLocales = config('folio.translations');
            if (
                $folioLocales &&
                count($folioLocales) > 1 &&
                App::getLocale() == $folioLocales[1]
            ) {
                config(['app.fallback_locale' => $folioLocales[0]]);
            }            
        }

        // Date
        $supported_langs = [
            'ar', 'az', 'bd', 'bg', 'ca', 'cs', 'cy', 'da', 'de',
            'el', 'en', 'eo', 'es', 'et', 'eu', 'fa', 'fi', 'fr',
            'gl', 'he', 'hi', 'hr', 'hu', 'id', 'is', 'it', 'ja',
            'ka', 'ko', 'lt', 'lv', 'mk', 'ms', 'nl', 'no', 'pl',
            'pt', 'ro', '-ru', 'sh', 'sk', 'sl', 'sq', 'sr', 'sv',
            'th', 'tk', 'tr', 'uk', 'vi'];

      	if(in_array($browser_lang, $supported_langs)) {
      		Date::setLocale($browser_lang);
      	} else {
      		Date::setLocale(config('app.fallback_locale', 'en'));
      	}

        return $next($request);
    }
}
