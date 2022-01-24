<?php

namespace Lar\LteAdmin\Layouts;

use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\AccessDeniedComponent;
use Lar\LteAdmin\Middlewares\Authenticate;

class LteLayout extends LteBase
{
    /**
     * To enable the module, specify the container identifier in the parameter.
     *
     * @var bool|string
     */
    protected $pjax = 'lte';

    public function __construct()
    {
        //LteAdmin::$echo = true;

        parent::__construct();

        $this->body->addClass('hold-transition sidebar-mini text-sm layout-fixed layout-navbar-fixed');

        try {
            $this->body->div(['wrapper'], function (DIV $div) {
                $div->view('lte::layout.nav');

                $div->view('lte::layout.side_bar');

                $div->div(['content-wrapper'], function (DIV $div) {
                    $this->toComponent($div, 'prep_end_wrapper');

                    $div->section(['content', 'id' => 'lte'])
                        ->haveLink($this->container);

                    $this->toComponent($this->container, 'prep_end_content');

                    $this->toComponent($div, 'app_end_wrapper');
                });

                $div->view('lte::layout.footer');

                $div->view('lte::layout.control_sidebar');
            });
        } catch (\Exception $exception) {
            dd($exception);
        }
    }

    /**
     * Add data in to layout content.
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
            } else {
                $this->container->appEnd(AccessDeniedComponent::create());
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
