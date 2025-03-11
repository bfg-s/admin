<?php

declare(strict_types=1);

namespace Admin\Middlewares;

use Admin\Facades\Admin;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;

/**
 * Middleware which is responsible for the language model of behavior of the admin panel.
 */
class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $lang = Admin::nowLang();

        App::setLocale($lang);

        Cookie::queue('lang', $lang, time() * 5);

        return $next($request);
    }
}
