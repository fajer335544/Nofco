<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cookie;

class AdminLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $locale = Cookie::get('locale');

        if (!empty($locale)) {
            if (!is_null($locale) && array_key_exists($locale, config('app.locales'))) {
                App::setLocale($locale);
            } else {
                App::setLocale(config('app.fallback_locale'));
            }
            $locale = config('app.locale');

            Cookie::queue('locale', $locale);
            config(['app.locale' => $locale]);
            config(['app.name' => trans('all.site-title')]);
            View::share([ 'locale' => $locale]);
            return $next($request);
        }else {
            $locale = App::getLocale();
            Cookie::queue('locale', $locale);
            config(['app.locale' => $locale]);
            config(['app.name' => trans('all.site-title')]);
            View::share([ 'locale' => $locale]);
            return $next($request);
        }
    }
}