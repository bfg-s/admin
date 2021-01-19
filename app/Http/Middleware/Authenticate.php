<?php

namespace Admin\Http\Middleware;

use Admin\Models\AdminPermission;
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
     * Access token for the template.
     * @var bool
     */
    static $access = true;

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
        $route_login = \Route::currentRouteName() === 'admin.login';

        /**
         * Redirect in case the administrator is logged in.
         */
        if (!Auth::guard('admin')->guest() && $route_login) {

            return redirect()->route('admin');
        }

        /**
         * An exception for the case when the user is not logged in and tries to access the page.
         */
        if (Auth::guard('admin')->guest() && !$route_login) {

            $this->unauthenticated($request);
        }

        /**
         * Launch of all services and extensions of the admin panel.
         */
        \AdminExtension::boot();

        /**
         * Checking the current access link.
         */
        if (!AdminPermission::checkCurrentPath() || !static::$access) {

            if ($request->ajax() && !$request->pjax()) {

                return response()->json([
                    __('admin.error'), __('admin.access_denied')
                ], 401);
            }

            else if (!$request->isMethod('get')) {

                return back()->with('error', [__('admin.error'), __('admin.access_denied')]);
            }

            static::$access = false;
        }

        return $next($request);
    }

    /**
     * Call exception if unauthenticated
     * @param  Request  $request
     * @throws AuthenticationException
     */
    protected function unauthenticated(Request $request)
    {
        $all = $request->all();
        $url = url()->current() . (count($all) ? "?" . http_build_query($all) : "");
        session(['return_authenticated_url' => $url]);

        throw new AuthenticationException(
            'Unauthenticated.', ['admin'], route('admin.login')
        );
    }
}
