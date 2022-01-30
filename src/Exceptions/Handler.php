<?php

namespace LteAdmin\Exceptions;

use Exception;
use Illuminate\Http\Request;
use LteAdmin;
use LteAdmin\Layouts\LteAuthLayout;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends \App\Exceptions\Handler
{
    /**
     * Report or log an exception.
     *
     * @param  Throwable  $exception
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param  Throwable  $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->is('*/'.config('lte.route.prefix').'*')) {
            if ($exception instanceof NotFoundHttpException) {
                $layout = new LteAuthLayout();
                $layout->setInContent(view('lte::404'));

                return response($layout->render());
            }

            if (!LteAdmin::guest()) {
                ob_clean();
            }
        }

        return parent::render($request, $exception);
    }
}
