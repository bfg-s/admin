<?php

namespace Lar\LteAdmin\Layouts;

use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Middlewares\Authenticate;

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

                    $this->toComponent($div, 'prep_end_wrapper');

                    $div->section(['content', 'id' => 'lte-content-container'])
                        ->haveLink($this->container);

                    $this->toComponent($this->container, 'prep_end_content');

                    $this->toComponent($div, 'app_end_wrapper');
                });

                $div->view('lte::segment.footer');

                $div->view('lte::segment.control_sidebar');
            });

        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    /**
     * Add data in to layout content
     *
     * @param $data
     * @return void
     * @throws \Exception
     */
    public function setInContent($data)
    {
        try {

            if (Authenticate::$access) {

                $this->container->appEnd($data);

                $this->toComponent($this->container, 'app_end_content');
            }

            else {

                $this->container->view('lte::access_denied');
            }

        } catch (\Exception $exception) {

            dd($exception);
        }
    }

    /**
     * @param  Component  $component
     * @param  string  $segment
     */
    private function toComponent(Component $component, string $segment)
    {
        foreach (\LteAdmin::getSegments($segment) as $segment) {
            if (\View::exists($segment['component'])) {
                $component->view($segment['component'], $segment['params']);
            } else {
                $component->appEnd(new $segment['component'](...$segment['params']));
            }
        }
    }
}
