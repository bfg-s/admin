<?php

namespace Admin\Middlewares;

use Cache;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Admin\Respond;
use Admin\Boot;
use Admin\Models\AdminPermission;
use ReflectionException;
use Route;
use Symfony\Component\DomCrawler\Crawler;

class Authenticate
{
    /**
     * @var bool
     */
    public static $access = true;
    /**
     * @var Collection
     */
    protected static $menu;

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return mixed
     * @throws ReflectionException|AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('admin')->guest() && $this->shouldPassThrough($request)) {
            session()->flash('respond', Respond::glob()->toJson());

            return redirect()->route(config('admin.home-route', 'admin.dashboard'));
        }

        if (Auth::guard('admin')->guest() && !$this->shouldPassThrough($request)) {
            session()->flash('respond', Respond::glob()->toJson());

            $this->unauthenticated($request);
        }

        Boot::run();

        if (!$this->access()) {
            if ($request->ajax() && !$request->pjax()) {
                admin_log_danger('Pattern go to the forbidden zone', 'Blocked Ajax request', 'fas fa-shield-alt');
                $respond = ['0:toast::error' => [__('admin.access_denied'), __('admin.error')]];

                if (request()->has('_exec')) {
                    $respond['1:doc::reload'] = null;
                }

                return response()->json($respond);
            } else {
                if (!$request->isMethod('get')) {
                    admin_log_danger('Pattern go to the forbidden zone', 'Blocked POST request', 'fas fa-shield-alt');
                    session()->flash(
                        'respond',
                        Respond::glob()->toast_error([__('admin.access_denied'), __('admin.error')])->toJson()
                    );

                    return back();
                }
            }

            static::$access = false;
        }

        if (
            ! Auth::guard('admin')->guest()
            && !\Illuminate\Support\Facades\Route::currentRouteNamed('admin.profile')
            && !\Illuminate\Support\Facades\Route::currentRouteNamed('jax.executor')
            && !\Illuminate\Support\Facades\Route::currentRouteNamed('admin.profile.logout')
            && config('admin.functional.force-2fa')
            && ! admin()->two_factor_confirmed_at
        ) {
            session()->flash(
                'respond',
                Respond::glob()->toast_error([__('admin.2fa_enable_before'), __('admin.error')])->toJson()
            );
            return redirect()->route('admin.profile');
        }

        admin_log_primary('Loaded page', trim(Route::currentRouteAction(), '\\'));

        /** @var Response $response */
        $response = $next($request);

//        if ($response instanceof Response) {
//
//            if ($areaResponse = $this->watchAreas($request, $response)) {
//                $response = $areaResponse;
//            } else if ($modalResponse = $this->watchModal($request, $response)) {
//                $response = $modalResponse;
//            }
//        }

        return $response;
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param  Request  $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        $excepts = [
            admin_uri('login'),
            admin_uri('2fa'),
            admin_uri('2fa_post'),
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
        if ($request->has('_pjax')) {
            unset($all['_pjax']);
        }
        $url = url()->current().(count($all) ? '?'.http_build_query($all) : '');
        session(['return_authenticated_url' => $url]);

        throw new AuthenticationException(
            'Unauthenticated.',
            ['admin'],
            route('admin.login')
        );
    }

    /**
     * @return bool
     */
    protected function access()
    {
        $now = admin_now();

        if ($now?->getRoles() && !admin_user()->hasRoles($now->getRoles())) {
            return false;
        }

        return AdminPermission::check();
    }

    protected function watchModal(Request $request, Response $response)
    {
        if ($request->has('_modal') && $request->ajax()) {
            $controller = Route::current()?->controller;

            $method = $request->get('_handle');
            if (
                $method
                && $controller
                && method_exists($controller, $method)
            ) {
                return $response->setContent(
                    app()->call([$controller, $method], [
                        'detail' => $request->has('_detail')
                    ])
                );
            }
        }

        return null;
    }

    protected function watchAreas(Request $request, Response $response)
    {
        if ($request->has('_areas') && $request->ajax()) {
            $_areas = $request->get('_areas');

            if ($_areas) {
                $hashes = Cache::get('admin_areas_hashes', []);

                $html = new Crawler($response->getContent());

                $result_areas = [];

                foreach ($_areas as $area) {
                    $ht = $html->filter("#{$area}");
                    if ($ht->count()) {
                        $html_text = $ht->eq(0)->html();
                        $html_hash = md5($html_text);
                        if (isset($hashes[$area])) {
                            if ($hashes[$area] != $html_hash) {
                                $result_areas[$area] = $html_text;
                                $hashes[$area] = $html_hash;
                            }
                        } else {
                            $result_areas[$area] = $html_text;
                            $hashes[$area] = $html_hash;
                        }
                    }
                }

                Cache::forever('admin_areas_hashes', $hashes);

                return $response->setContent($result_areas);
            }
        } else {
            if (Cache::has('admin_areas_hashes')) {
                Cache::forget('admin_areas_hashes');
            }
        }

        return null;
    }
}
