<?php

namespace Lar\LteAdmin\Layouts;

use Lar\Layout\Tags\DIV;

/**
 * Landing Class
 *
 * @package App\Layouts
 */
class LteLayout extends LteBase
{
    /**
     * To enable the module, specify the container identifier in the parameter.
     *
     * @var bool|string
     */
    protected $pjax = "lte-content-container";

    /**
     * LteLayout constructor.
     *
     * @param string $body_class
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->body->addClass('hold-transition sidebar-mini text-sm layout-fixed layout-navbar-fixed');

        try {

            $this->body->div(['wrapper'], function (DIV $div) {

                $div->view('lte::segment.nav');

                $div->view('lte::segment.side_bar');

                $div->div(['content-wrapper'], function (DIV $div) {

                    $div->section(['content', 'id' => 'lte-content-container'])
                        //->view('lte::segment.container_header')
                        ->haveLink($this->container);
                });

                $div->view('lte::segment.footer');

                $div->view('lte::segment.control_sidebar');
            });

        } catch (\Exception $exception) {
            dd($exception);
        }
    }
}
