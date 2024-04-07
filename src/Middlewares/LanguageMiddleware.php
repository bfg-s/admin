<?php

declare(strict_types=1);

namespace Admin\Middlewares;

use Admin\Facades\AdminFacade;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class LanguageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle($request, Closure $next)
    {
        $lang = AdminFacade::nowLang();

        App::setLocale($lang);

        Cookie::queue('lang', $lang, time() * time());

        return $next($request);
    }
}
