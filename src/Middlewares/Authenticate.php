<?php

namespace Lar\LteAdmin\Middlewares;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Lar\Layout\Core\LConfigs;
use Lar\Layout\Respond;
use Lar\LteAdmin\LteBoot;
use Lar\LteAdmin\Models\LtePermission;

/**
 * Class Authenticate
 *
 * @package Lar\LteAdmin\Middlewares
 */
class Authenticate
{
    /**
     * @var Collection
     */
    protected static $menu;

    /**
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
        if (!Auth::guard('lte')->guest() && $this->shouldPassThrough($request)) {

            session()->flash("respond", Respond::glob()->toJson());

            return redirect()->route('lte.dashboard');
        }
        
        if (Auth::guard('lte')->guest() && !$this->shouldPassThrough($request)) {

            session()->flash("respond", Respond::glob()->toJson());

            $this->unauthenticated($request);
        }

        LteBoot::run();

        LConfigs::add('uploader', route('lte.uploader'));

        if (!$this->access()) {

            if ($request->ajax() && !$request->pjax()) {

                $respond = ["0:toast::error" => [__('lte.access_denied'), __('lte.error')]];

                if (request()->has("_exec")) {

                    $respond["1:doc::reload"] = null;
                }

                return response()->json($respond);
            }

            else if (!$request->isMethod('get')) {

                session()->flash("respond", respond()->toast_error([__('lte.access_denied'), __('lte.error')])->toJson());

                return back();
            }

            static::$access = false;
        }

        return $next($request);
    }

    /**
     * @return bool
     */
    protected function access()
    {
        $now = lte_now();

        list($class, $method) = \Str::parseCallback(\Route::currentRouteAction(), 'index');
        $classes = [trim($class, "\\")];

        if ($now && isset($now['extension']) && $now['extension']) {
            $classes[] = get_class($now['extension']);
        }

        if (!lte_class_can($classes, $method)) {

            return false;
        }

        if (method_exists($class, 'roles') && is_array($class::$roles) && !lte_user()->hasRoles($class::$roles)) {

            return false;
        }

        if (isset($now['roles']) && !lte_user()->hasRoles($now['roles'])) {

            return false;
        }

        return LtePermission::check();
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
            lte_uri('login'),
            lte_uri('logout'),
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
