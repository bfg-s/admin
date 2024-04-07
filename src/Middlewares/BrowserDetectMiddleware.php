<?php

declare(strict_types=1);

namespace Admin\Middlewares;

use Admin\Facades\AdminFacade;
use Admin\Models\AdminBrowser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class BrowserDetectMiddleware
{
    /**
     * @var AdminBrowser|null
     */
    public static ?AdminBrowser $browser = null;

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //dump($request->userAgent());
        if (admin()->exists && $request->userAgent() && ! $request->ajax()) {

            $result = getBrowserDetails($request->userAgent());

            if ($result['name'] !== 'Unknown') {

                $id = $request->cookie('browser');

                /** @var AdminBrowser $exists */
                $exists = $id ? admin()->browsers()->find($id) : null;

                $data = [
                    'name' => $result['name'],
                    'ip' => $request->ip(),
                    'user_agent' => $result['userAgent'],
                    'session_id' => session()->getId(),
                ];

                if ($exists) {
                    $exists->update($data);
                } else {
                    $exists = admin()->browsers()->create($data)->fresh();
                }

                if (! $exists->active) {
                    Auth::guard('admin')->logout();
                    return redirect()->route('admin.home');
                }

                static::$browser = $exists;

                Cookie::queue(Cookie::forever('browser', $exists->id));
            }
        }

        return $next($request);
    }
}
