<?php

declare(strict_types=1);

namespace Admin\Middlewares;

use Admin\BladeDirectives\SystemJsBladeDirective;
use Admin\BladeDirectives\UpdateWithPjaxBladeDirective;
use Admin\Components\ModelTableComponent;
use Admin\Respond;
use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\ViewErrorBag;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DomMiddleware
{
    /**
     * @var ModelTableComponent|null
     */
    protected static ?ModelTableComponent $modelTableComponent = null;

    /**
     * @param  ModelTableComponent|null  $modelTableComponent
     * @return void
     */
    public static function setModelTableComponent(?ModelTableComponent $modelTableComponent): void
    {
        if (!static::$modelTableComponent) {
            static::$modelTableComponent = $modelTableComponent;
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     * @throws Exception
     */
    public function handle($request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response->isRedirection()) {
            session()->flash('respond', Respond::glob()->toJson());

            return $response;
        }

        if ($request->ajax() && !$request->pjax()) {
            return $response;
        }

        if (!$request->isMethod('get')) {
            return $response;
        }

        if ($response instanceof JsonResponse) {
            return $response;
        }

        if (
            $request->has('format')
            && $request->has('q')
        ) {
            if (static::$modelTableComponent) {
                return response()->json(static::$modelTableComponent->getPaginate()->jsonSerialize());
            }
            return response()->json([]);
        }

        $this->setUriHeader($response, $request)
            ->setErrorsToasts()
            ->setContent($request, $response);

        return $response;
    }

    /**
     * @param  Request  $request
     * @param  Response|BinaryFileResponse  $response
     * @return $this
     */
    protected function setContent(Request $request, Response|BinaryFileResponse $response)
    {
        if ($request->pjax() && $request->header('X-PJAX-CONTAINER') && $response instanceof Response) {
            $html = new Crawler($response->getContent());

            $container_html = $html->filter($request->header('X-PJAX-CONTAINER'))->eq(0);
            $content = $container_html->count() > 0 ? $container_html->html() : '';

            $this->createTagWatcher($html);

            $js = SystemJsBladeDirective::buildScripts(false);

            if ($js) {
                $content .= '<script compile data-exec-on-popstate>'.$js.'</script>';
            }


            $response->setContent($content);
        }

        return $this;
    }


    /**
     * @param  Crawler  $html
     * @return $this
     */
    protected function createTagWatcher(Crawler $html): static
    {
        foreach (UpdateWithPjaxBladeDirective::$_lives as $life) {
            SystemJsBladeDirective::addComponentJs(
                "exec("
                .json_encode([
                    'html' => [
                        "[data-update-with-pjax='{$life}']",
                        $html->filter("[data-update-with-pjax='{$life}']")->eq(0)->html()
                    ]
                ]).");"
            );
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function setErrorsToasts()
    {
        if (config('layout.toast_errors', true) && session()->has('errors')) {
            /** @var ViewErrorBag $bags */
            $bags = session('errors');

            $messages = $bags->getBag('default')->all();

            foreach ($messages as $message) {
                Respond::glob()->toast_error($message);
            }
        }

        return $this;
    }

    /**
     * @param  Response|RedirectResponse  $response
     * @param  Request  $request
     * @return DomMiddleware
     */
    protected function setUriHeader($response, Request $request)
    {
        if (method_exists($response, 'header')) {
            $response->header('X-PJAX-URL', $request->getRequestUri());
        }

        return $this;
    }
}
