<?php

namespace Admin\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Class Handler
 * @package Admin\Exceptions
 */
class Handler extends \App\Exceptions\Handler
{
    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->is('*/'.config('admin.route.prefix').'*')) {

            if ($exception instanceof NotFoundHttpException) {

                return response(view('admin::errors.404'));
            }

            if (!\Admin::guest()) {

                ob_clean();
            }
        }

        return parent::render($request, $exception);
    }
}
