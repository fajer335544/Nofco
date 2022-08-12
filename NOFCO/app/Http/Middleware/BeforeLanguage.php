<?php

namespace App\Http\Middleware;

use Closure;
use \Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;
use \Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cookie;

class BeforeLanguage
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

        $locale = Request::route('locale');

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
            if(!is_null(Cookie::get('locale')) && array_key_exists(Cookie::get('locale'), config('app.locales'))) {
                $locale = Cookie::get('locale');
                App::setLocale(Cookie::get('locale'));
                config(['app.locale' => $locale]);
                View::share(['locale' => $locale]);
                config(['app.name' => trans('all.site-title')]);

                return $next($request);
            }else{
                return redirect(url('/' . config('app.fallback_locale')));
            }
        }
    }
}