<?php

namespace Lar\LteAdmin\Middlewares;

use Cache;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Lar\Layout\Core\LConfigs;
use Lar\Layout\Respond;
use Lar\LteAdmin\LteBoot;
use Lar\LteAdmin\Models\LtePermission;
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
     * @throws ReflectionException
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::guard('lte')->guest() && $this->shouldPassThrough($request)) {
            session()->flash('respond', Respond::glob()->toJson());

            return redirect()->route('lte.dashboard');
        }

        if (Auth::guard('lte')->guest() && !$this->shouldPassThrough($request)) {
            session()->flash('respond', Respond::glob()->toJson());

            $this->unauthenticated($request);
        }

        LteBoot::run();

        LConfigs::add('home', route('lte.home'));
        LConfigs::add('uploader', route('lte.uploader'));

        if (!$this->access()) {
            if ($request->ajax() && !$request->pjax()) {
                lte_log_danger('Pattern go to the forbidden zone', 'Blocked Ajax request', 'fas fa-shield-alt');
                $respond = ['0:toast::error' => [__('lte.access_denied'), __('lte.error')]];

                if (request()->has('_exec')) {
                    $respond['1:doc::reload'] = null;
                }

                return response()->json($respond);
            } else {
                if (!$request->isMethod('get')) {
                    lte_log_danger('Pattern go to the forbidden zone', 'Blocked GET request', 'fas fa-shield-alt');
                    session()->flash('respond',
                        respond()->toast_error([__('lte.access_denied'), __('lte.error')])->toJson());

                    return back();
                }
            }

            static::$access = false;
        }

        lte_log_primary('Loaded page', trim(Route::currentRouteAction(), '\\'));

        /** @var Response $res */
        $res = $next($request);

        if ($request->has('_areas') && $request->ajax()) {
            $_areas = $request->get('_areas');

            if ($_areas) {
                $hashes = Cache::get('admin_areas_hashes', []);

                $html = new Crawler($res->getContent());

                $result_areas = [];

                foreach ($_areas as $area) {
                    $html_text = $html->filter("#{$area}")->eq(0)->html();
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

                Cache::forever('admin_areas_hashes', $hashes);

                return $res->setContent($result_areas);
            }
        } else {
            if (Cache::has('admin_areas_hashes')) {
                Cache::forget('admin_areas_hashes');
            }
        }

        return $res;
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
        if ($request->has('_pjax')) {
            unset($all['_pjax']);
        }
        $url = url()->current().(count($all) ? '?'.http_build_query($all) : '');
        session(['return_authenticated_url' => $url]);

        throw new AuthenticationException(
            'Unauthenticated.', ['lte'], route('lte.login')
        );
    }

    /**
     * @return bool
     */
    protected function access()
    {
        $now = lte_now();

        if (isset($now['roles']) && !lte_user()->hasRoles($now['roles'])) {
            return false;
        }

        return LtePermission::check();
    }
}
