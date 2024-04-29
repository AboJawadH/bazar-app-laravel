<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;
use Laracasts\Utilities\JavaScript\JavaScriptFacade as JavaScript;
use Illuminate\Support\Facades\Log;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //@@@@@@@@@//
        //@@@@@@@@@//
        //@@@@@@@@@//
        // Log::debug("0");
        $languageShortcut = $request->header('X-Accept-Language');
        // Log::debug("1");
        // Log::debug($languageShortcut);

        if ($languageShortcut) {

            if ($languageShortcut === 'ar') {
                App::setLocale('ar');
                // Log::debug($languageShortcut);
            } elseif ($languageShortcut === 'en') {
                App::setLocale('en');
                // Log::debug($languageShortcut);
            } elseif ($languageShortcut === 'tr') {
                App::setLocale('tr');
                // Log::debug($languageShortcut);
            }
        }
        //@@@@@@@@@//
        //@@@@@@@@@//
        //@@@@@@@@@//
        return $next($request);

        // $allowed_languages = collect(['ar', 'en']);

        // if ($request->is('api/*')) {
        //     $locale = $request->header('Accept-Language', Config::get('app.locale'));
        //     Log::debug($locale);
        //     Log::debug("11");

        // } else {
        //     if (!Session::has('locale')) {
        //         Session::put('locale', Config::get('app.locale'));
        //         Log::debug("22");

        //     }

        //     $locale = Session::get('locale');
        //     Log::debug($locale);
        //     Log::debug("33");


        // }

        // if (!$allowed_languages->contains($locale)) {
        //     $locale = Config::get('app.locale');
        //     Log::debug("44");

        // }

        // App::setLocale($locale);
        // Log::debug("55");
        // Log::debug($locale);

        // Config::set('mail.markdown.theme', $locale);
        // Carbon::setLocale($locale);
        // Config::set('app.locale', $locale);

        // $direction = ($locale === 'ar' ? 'rtl' : 'ltr');

        // View::share([
        //     'locale' => $locale,
        //     'direction' => $direction,
        // ]);

        // JavaScript::put([
        //     'locale' => $locale,
        //     'direction' => $direction,
        // ]);


    }
}
