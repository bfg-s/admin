<?php

namespace Lar\LteAdmin\Core;

use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\H3;
use Lar\LteAdmin\Segments\LtePage;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\Nested;

/**
 * @mixin LtePage
 */
class LtePageMixin
{
    public function card()
    {
        return function ($title = null, callable $callback = null) {
            if (is_embedded_call($title)) {
                $callback = $title;
                $title = "";
            }
            $originTitle = $title;
            $title = is_array($title) && isset($title[0]) ? $title[0] : $title;
            if (lte_model_type('index')) {
                $title = $title ?: 'lte.list';
            } else if (lte_model_type('create')) {
                $title = $title ?: 'lte.add';
            } else if (lte_model_type('edit')) {
                $title = is_array($originTitle) && isset($originTitle[1]) ? $originTitle[1] : $title;
                $title = $title ?: 'lte.id_edit';
            } else if (lte_model_type('show')) {
                $title = $title ?: 'lte.information';
            }

            $this->registerClass(
                $this->registerClass($this->getClass(DIV::class)->card($title))
                    ->group()
            );
            $this->callCallBack($callback);
            return $this;
        };
    }

    public function search()
    {
        return function (callable $callback = null) {
            if ($this->hasClass(Card::class)) {

                $this->getClass(Card::class)->withSearchForm();
                $this->registerClass($this->getClass(Card::class)->search_form);
                $this->callCallBack($callback);
            }

            return $this;
        };
    }

    public function table()
    {
        return function (callable $callback = null) {
            $callback = function (ModelTable $table) use ($callback) {
                $this->callCallBack($callback, $table);
            };

            if ($this->hasClass(Card::class)) {
                $this->getClass(Card::class)->bodyModelTable($callback);

                $this->getClass(Card::class)->titleObj(function (H3 $h3) {
                    $ad = $this->getClass(ModelTable::class)->getActionData();
                    if ($ad['show']) {
                        $h3->_()->prepEnd(
                            view('lte::segment.model_table_actions', $ad)
                        );
                    }
                });
            } else {
                $this->getClass(DIV::class)->model_table($callback);
            }

            return $this;
        };
    }

    public function nested()
    {
        return function (callable $callback = null) {

            $this->getClass(Card::class)->nestedTools();

            $callback = function (Nested $nested) use ($callback) {
                $this->callCallBack($callback, $nested);
            };
            if ($this->hasClass(Card::class)) {
                $this->getClass(Card::class)->body()->nested($callback);
            } else {
                $this->getClass(DIV::class)->nested($callback);
            }
            return $this;
        };
    }

    public function ordered()
    {
        return function (callable $callback = null) {

            $callback = function (Nested $nested) use ($callback) {
                $this->callCallBack($callback, $nested);
            };
            if ($this->hasClass(Card::class)) {
                $this->getClass(Card::class)->body()->nested($callback);
            } else {
                $this->getClass(DIV::class)->nested($callback);
            }
            return $this;
        };
    }

    public function form()
    {
        return function (callable $callback = null) {

            $callback = function (Form $form) use ($callback) {
                $this->callCallBack($callback, $form);
            };
            if ($this->hasClass(Card::class)) {
                $this->getClass(Card::class)->bodyForm($callback)->footerForm();
            } else {
                $this->getClass(DIV::class)->form($callback);
            }
            return $this;
        };
    }

    public function info()
    {
        return function (callable $callback = null) {

            $callback = function (ModelInfoTable $table) use ($callback) {
                $this->callCallBack($callback, $table);
            };
            if ($this->hasClass(Card::class)) {
                $this->getClass(Card::class)->fullBody()->model_info_table($callback);
            } else {
                $this->getClass(DIV::class)->model_info_table($callback);
            }
            return $this;
        };
    }

    public function buttons()
    {
        return function (callable $callback = null) {

            if ($this->hasClass(Card::class)) {
                $this->registerClass($this->getClass(Card::class)->group());
            } else {
                $this->registerClass($this->getClass(DIV::class)->button_group());
            }

            $this->callCallBack($callback);
            return $this;
        };
    }

    public function chartjs()
    {
        return function (callable $callback = null) {

            if ($this->hasClass(Card::class)) {
                $this->registerClass(
                    $this->getClass(Card::class)->fullBody()->chart_js($callback)
                );
            } else {
                $this->registerClass(
                    $this->getClass(DIV::class)->chart_js($callback)
                );
            }
            return $this;
        };
    }

    public function periods()
    {
        return function (callable $callback = null) {

            if ($this->hasClass(Card::class)) {
                $this->registerClass(
                    $this->getClass(Card::class)->body()->statistic_periods($callback)
                );
            } else {
                $this->registerClass(
                    $this->getClass(DIV::class)->statistic_periods($callback)
                );
            }
            return $this;
        };
    }

    public function withTools()
    {
        return function ($test = null) {

            if ($this->hasClass(Card::class)) {
                $this->getClass(Card::class)->defaultTools($test);
            }
            return $this;
        };
    }

    public function whenNeed()
    {
        return function (string $method, callable $callback = null, $default = null) {

            $this->when(request()->has('method') ? request('method') == $method : $default, function (LtePage $page) use ($method, $callback) {
                $page->{$method}($callback);
            });

            return $this;
        };
    }
}
