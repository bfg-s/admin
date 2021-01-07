<?php

namespace Admin\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class Authenticate
 * @package Admin\Http\Middleware
 */
class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('admin')->guest() && $this->shouldPassThrough($request)) {

            return redirect()->route('admin');
        }
        
        if (Auth::guard('admin')->guest() && !$this->shouldPassThrough($request)) {

            $this->unauthenticated($request);
        }

        return $next($request);
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        $excepts = [
            admin_uri('login'),
            admin_uri('logout'),
        ];

        foreach ($excepts as $except) {

            if ($except !== '/') {

                $except = trim($except, '/');
            }

            if ($request->is($except)) {

                return true;
            }
        }

        return false;
    }

    /**
     * @param  Request  $request
     * @throws AuthenticationException
     */
    protected function unauthenticated(Request $request)
    {
        $all = $request->all();
        if ($request->has('_pjax')) { unset($all['_pjax']); }
        $url = url()->current() . (count($all) ? "?" . http_build_query($all) : "");
        session(['return_authenticated_url' => $url]);

        throw new AuthenticationException(
            'Unauthenticated.', ['lte'], route('lte.login')
        );
    }
}
