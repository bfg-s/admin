<?php

namespace Lar\LteAdmin\Exceptions;

use Lar\LteAdmin\Layouts\LteAuthLayout;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

/**
 * Class Handler.
 * @package Lar\LteAdmin\Exceptions
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
        if ($request->is('*/'.config('lte.route.prefix').'*')) {
            if ($exception instanceof NotFoundHttpException) {
                $layout = new LteAuthLayout();
                $layout->setInContent(view('lte::404'));

                return response($layout->render());
            }

            if (! \LteAdmin::guest()) {
                ob_clean();
            }
        }

        return parent::render($request, $exception);
    }
}
