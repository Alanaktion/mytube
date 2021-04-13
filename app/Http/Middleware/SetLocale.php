<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLocale
{
    public const SESSION_KEY = 'locale';

    public function handle(Request $request, Closure $next)
    {
        $session = $request->getSession();

        $locales = array_keys(config('app.locale_list'));

        if (!$session->has(self::SESSION_KEY)) {
            $session->put(self::SESSION_KEY, $request->getPreferredLanguage($locales) ?: 'en');
        }

        if ($request->has('lang')) {
            $lang = $request->input('lang');
            if (in_array($lang, $locales)) {
                $session->put(self::SESSION_KEY, $lang);
            }
        }

        app()->setLocale($session->get(self::SESSION_KEY));

        return $next($request);
    }
}
