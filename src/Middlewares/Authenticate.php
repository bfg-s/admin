<?php

declare(strict_types=1);

namespace Admin\Middlewares;

use Admin\Boot;
use Admin\Facades\Admin;
use Admin\Models\AdminPermission;
use Admin\Respond;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use ReflectionException;
use Route;

/**
 * Middleware which is responsible for checking authorization and authentication.
 */
class Authenticate
{
    /**
     * A property that is responsible for public access to the page.
     *
     * @var bool
     */
    public static bool $access = true;

    /**
     * Mark request as no log.
     *
     * @var bool
     */
    public static bool $noLog = false;

    /**
     * A property that is responsible for the state of the request type, so that it is not a get or a head.
     *
     * @var bool
     */
    protected bool $isNoGetOrHead = false;

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     * @throws ReflectionException|AuthenticationException
     */
    public function handle(Request $request, Closure $next): Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $currentRouteName = \Illuminate\Support\Facades\Route::currentRouteName();

        if (!$this->isNoGetOrHead) {

            $this->isNoGetOrHead = !($request->isMethod('get') || $request->isMethod('head'));
        }

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
                    $respond['1:reload'] = null;
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
            !Auth::guard('admin')->guest()
            && !\Illuminate\Support\Facades\Route::currentRouteNamed('admin.profile')
            && !\Illuminate\Support\Facades\Route::currentRouteNamed('admin.load_modal')
            && !\Illuminate\Support\Facades\Route::currentRouteNamed('admin.toggle_dark')
            && !\Illuminate\Support\Facades\Route::currentRouteNamed('admin.call_callback')
            && !\Illuminate\Support\Facades\Route::currentRouteNamed('admin.load_lives')
            && !\Illuminate\Support\Facades\Route::currentRouteNamed('admin.load_chart_js')
            && !\Illuminate\Support\Facades\Route::currentRouteNamed('admin.profile.logout')
            && config('admin.force-2fa')
            && !admin()->two_factor_confirmed_at
        ) {
            session()->flash(
                'respond',
                Respond::glob()->toast_error([__('admin.2fa_enable_before'), __('admin.error')])->toJson()
            );


            return redirect()->route('admin.profile');
        }

        if (static::$access) {
            foreach (Admin::extensions() as $extension) {
                $extension->config()->middleware($request);
            }
        }


        /** @var Response $response */
        $response = $next($request);

        if (
            ! $this->isNoGetOrHead
            && ! static::$noLog
            && $currentRouteName != 'admin.call_callback'
            && $currentRouteName != 'admin.load_lives'
            && $currentRouteName != 'admin.load_chart_js'
            && $currentRouteName != 'admin.translate'
            && $currentRouteName != 'admin.save_image_order'
            && $currentRouteName != 'admin.delete_ordered_image'
            && $currentRouteName != 'admin.load_select2'
            && $currentRouteName != 'admin.realtime'
        ) {
            admin_log_primary('Loaded page', trim(Route::currentRouteAction() ?: '', '\\'), 'fas fa-file-download');
        }

        if ($response instanceof Response) {
            foreach (Admin::extensions() as $extension) {
                $response = $extension->config()->response($response);
            }
        }

        return $response;
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param  Request  $request
     *
     * @return bool
     */
    protected function shouldPassThrough(Request $request): bool
    {
        $excepts = [
            'bfg/info',
            admin_uri('login'),
            admin_uri('2fa'),
            admin_uri('2fa_post'),
            admin_uri('logout'),
        ];

        foreach ($excepts as $except) {
            if ($except !== 'bfg/info') {
                if ($except !== '/') {
                    $except = trim($except, '/');
                }

                if (config('admin.lang_mode') && !str_starts_with($except, App::getLocale())) {
                    $except = App::getLocale().'/'.$except;
                }
            }

            if ($request->is($except)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Method for handling an exception being thrown for an unauthorized user.
     *
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
        if (
            $request->isMethod('GET')
            && !\Illuminate\Support\Facades\Route::currentRouteNamed('profile.logout')
        ) {

            session(['return_authenticated_url' => $url]);
        }

        throw new AuthenticationException(
            'Unauthenticated.',
            ['admin'],
            route('admin.login')
        );
    }

    /**
     * Method for checking user access to a page.
     *
     * @return bool
     */
    protected function access(): bool
    {
        $now = admin_now();

        if ($now?->getRoles() && !admin_user()->hasRoles($now->getRoles())) {
            return false;
        }

        return AdminPermission::check();
    }
}
