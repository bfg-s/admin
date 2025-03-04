<?php

declare(strict_types=1);

namespace Admin\Middlewares;

use Admin\Models\AdminBrowser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware for checking the administrator's browser.
 */
class BrowserDetectMiddleware
{
    /**
     * @var AdminBrowser|null
     */
    public static AdminBrowser|null $browser = null;

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (admin()->id && $request->userAgent()) {
            $result = getBrowserDetails($request->userAgent());

            if ($result['name'] !== 'Unknown') {

                $exists = admin()->browsers()
                    ->where('name', $result['name'])
                    ->where('ip', $request->ip())
                    ->latest()
                    ->first();

                $data = [
                    'name' => $result['name'],
                    'ip' => $request->ip(),
                    'user_agent' => $result['userAgent'],
                    'session_id' => session()->getId(),
                    'admin_user_id' => admin()->id,
                ];

                if ($exists) {
                    $exists->update($data);
                    $exists->touch();
                } else {
                    $exists = admin()->browsers()->create($data)->fresh();
                }

                if (!$exists->active) {
                    Auth::guard('admin')->logout();
                    session()->flash('message', 'Your browser is not allowed to access the admin panel.');
                    return redirect()->route('admin.home');
                }

                static::$browser = $exists;
            }
        }

        return $next($request);
    }
}
