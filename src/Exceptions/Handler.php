<?php

namespace Admin\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Admin;
use Admin\Layouts\AdminAuthLayout;
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
        if ($request->is('*/'.config('admin.route.prefix').'*')) {
            if ($exception instanceof NotFoundHttpException) {
                $layout = new AdminAuthLayout();
                $layout->setInContent(view('admin::404'));

                return response($layout->render());
            }

            if (!Admin::guest()) {
                ob_clean();
            }
        }

        return parent::render($request, $exception);
    }
}
