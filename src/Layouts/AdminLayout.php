<?php

namespace Admin\Layouts;

use Exception;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Admin;
use Admin\Components\AccessDeniedComponent;
use Admin\Middlewares\Authenticate;
use View;

class AdminLayout extends AdminBase
{
    /**
     * To enable the module, specify the container identifier in the parameter.
     *
     * @var bool|string
     */
    protected $pjax = 'admin';

    public function __construct()
    {
        //Admin::$echo = true;

        parent::__construct();

        $this->body->addClass('hold-transition sidebar-mini text-sm layout-fixed layout-navbar-fixed');

        try {
            $this->body->div(['wrapper'], function (DIV $div) {
                $div->view('admin::layout.nav');

                $div->view('admin::layout.side_bar');

                $div->div(['content-wrapper'], function (DIV $div) {
                    $this->toComponent($div, 'prep_end_wrapper');

                    $div->section(['content', 'id' => 'admin'])
                        ->haveLink($this->container);

                    $this->toComponent($this->container, 'prep_end_content');

                    $this->toComponent($div, 'app_end_wrapper');
                });

                $div->view('admin::layout.footer');

                $div->view('admin::layout.control_sidebar');
            });
        } catch (Exception $exception) {
            dd($exception);
        }
    }

    /**
     * @param  Component  $component
     * @param  string  $segment
     */
    private function toComponent(Component $component, string $segment)
    {
        foreach (Admin::getSegments($segment) as $segment) {
            if (View::exists($segment['component'])) {
                $component->view($segment['component'], $segment['params']);
            } else {
                $component->appEnd(new $segment['component'](...$segment['params']));
            }
        }
    }

    /**
     * Add data in to layout content.
     *
     * @param $data
     * @return void
     * @throws Exception
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
        } catch (Exception $exception) {
            dd($exception);
        }
    }
}
