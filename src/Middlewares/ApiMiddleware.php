<?php

declare(strict_types=1);

namespace Admin\Middlewares;

use Admin\Controllers\SystemController;
use Admin\Facades\Admin;
use Admin\Models\AdminUser;
use Admin\Repositories\AdminRepository;
use Admin\Respond;
use Bfg\Resource\BfgResource;
use Closure;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;

/**
 * Middleware which is responsible for the api requests to the admin panel.
 */
class ApiMiddleware
{
    /**
     * A property that is responsible for the state of the request type, api request or not.
     *
     * @var bool
     */
    protected static bool $isApi = false;

    /**
     * The important contents that should be present in the response.
     *
     * @var array
     */
    protected static array $importantContents = [];

    /**
     * The expected query parameters.
     *
     * @var array
     */
    protected static array $expectedQuery = [];

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {

        $currentController = Route::current()->controller;

        static::$isApi = $request->header('Content-Api')
            && ! $currentController instanceof SystemController;

        if (static::isApi() && $token = $request->bearerToken()) {

            try {
                $token = Crypt::decrypt($token);
            } catch (\Throwable $e) {
                $token = null;
            }

            if ($user = AdminUser::findOrFail($token)) {

                Auth::guard('admin')->login($user);
            }
        }

        if (static::isApi()) {

            $request->request->add([
                '_token' => $request->session()->token(),
            ]);
        }

        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);

        if (static::isApi()) {


            Auth::guard('admin')->logout();

            $response->headers->add([
                'Content-Type' => 'application/json',
                'X-Content-Type-Options' => 'nosniff',
            ]);

            if (! Authenticate::$access) {

                return response()->json([
                    'status' => 'error',
                    'message' => 'Permission denied.'
                ], 403);
            }

            if ($response->exception) {

                return response()->json([
                    'status' => 'error',
                    'message' => $response->exception->getMessage()
                ], 500);
            }

            if ($validationError = $request->session()->get('errors')?->getBag('default')) {

                $response->setContent(
                    json_encode([
                        'status' => 'error',
                        'message' => 'The given data was invalid.',
                        'errors' => $validationError
                    ], JSON_UNESCAPED_UNICODE)
                );

                $response->setStatusCode(422);

                $request->session()->forget('errors');

                return $response;
            }

            $content = json_decode(
                $this->removeHttpHeaders($response->getContent()), true
            );

            if (! $request->header('Content-Layout')) {
                unset($content['contents'], $content['buttonGroups'], $content['menu']);
            }

            //unset($content['buttonGroups'], $content['menu']);

            $content['query'] = static::$expectedQuery;
            $content['data'] = [];
            foreach (static::$importantContents as $name => [$contentImportant, $resource]) {
                if ($resource) {
                    $resource = new $resource;
                    if ($resource instanceof JsonResource) {
                        if (
                            $contentImportant instanceof Collection
                            || $contentImportant instanceof LengthAwarePaginator
                            || (is_array($contentImportant) && !is_assoc($contentImportant))
                        ) {
                            $jsonResourceResponse = (new ResourceResponse(
                                $resource::collection($contentImportant)
                            ))->toResponse($request)->original;
                        } else {
                            $jsonResourceResponse = $resource::make($contentImportant);
                        }

                        $content['data'][$name] = $jsonResourceResponse;
                    }
                } else {
                    $content['data'][$name] = $contentImportant;
                }
            }

            $content['respond'] = Respond::glob();
            $content['version'] = Admin::version();

            $response->setContent(
                json_encode($content, JSON_UNESCAPED_UNICODE)
            );
        }

        return $response;
    }

    /**
     * Remove http headers from the input.
     *
     * @param $input
     * @return array|string|null
     */
    function removeHttpHeaders($input): array|string|null
    {
        $pattern = '/^HTTP\/\d\.\d \d{3} .*\r?\n(?:[A-Za-z\-]+: .*\r?\n)+\r?\n/';
        return trim(preg_replace($pattern, '', $input));
    }

    /**
     * Check if the request is an api request.
     *
     * @return bool
     */
    public static function isApi(): bool
    {
        return static::$isApi;
    }

    /**
     * Set the important contents that should be present in the response.
     *
     * @param  string  $name
     * @param  mixed  $content
     * @param  string|null  $resource
     * @return void
     */
    public static function addImportantContent(string $name, mixed $content, string $resource = null): void
    {
        static::$importantContents[$name] = [$content, $resource];
    }

    /**
     * Add the expected query parameter.
     *
     * @param  string  $name
     * @return void
     */
    public static function addExpectedQuery(string $name): void
    {
        static::$expectedQuery[] = $name;
    }

    /**
     * Reset static variables.
     *
     * @return void
     */
    public static function reset(): void
    {
        static::$expectedQuery = [];
        static::$importantContents = [];
    }
}
