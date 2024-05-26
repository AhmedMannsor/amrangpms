<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class SetDirection
{
    public function handle($request, Closure $next)
    {
        $lang = session('app_locale', 'en');
        App::setLocale($lang);

        $direction = $lang === 'ar' ? 'rtl' : 'ltr';
        Config::set('settings.KT_THEME_DIRECTION', $direction);

        return $next($request);
    }
}
