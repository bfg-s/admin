<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\ButtonGroup;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\Nested;
use Lar\LteAdmin\Segments\Tagable\SearchForm;

/**
 * Class LtePage
 * @package Lar\LteAdmin\Segments
 */
class LtePage extends Container {

    /**
     * @var Card|null
     */
    protected $card = null;

    /**
     * @var ModelTable|null
     */
    protected $modelTable = null;

    /**
     * @var Nested|null
     */
    protected $nested = null;

    /**
     * @var Form|null
     */
    protected $form = null;

    /**
     * @var ModelInfoTable|null
     */
    protected $infoTable = null;

    /**
     * @var ButtonGroup|null
     */
    protected $buttonGroup = null;
    protected $searchForm = null;

    protected $switched = false;
    protected $withNestedTools = false;

    /**
     * Sheet constructor.
     */
    public function __construct()
    {
        parent::__construct(null);
    }

    /**
     * @param  \Closure|array|string|null  $title
     * @param  callable|null  $callback
     * @return static
     */
    public function card($title = null, callable $callback = null)
    {
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
        $this->card = $this->component->card($title);
        $this->buttonGroup = $this->card->group();
        $this->callCallBack($callback);
        return $this;
    }

    public function search(callable $callback = null)
    {
        if ($this->card) {

            $this->card->withSearchForm();
            $this->searchForm = $this->card->search_form;
            $this->callCallBack($callback);
        }

        return $this;
    }

    /**
     * @param  callable  $callback
     * @return static
     */
    public function table(callable $callback = null)
    {
        $callback = function (ModelTable $table) use ($callback) {
            $this->modelTable = $table;
            $this->callCallBack($callback);
        };
        if ($this->card) {
            $this->card->bodyModelTable($callback);
        } else {
            $this->component->model_table($callback);
        }

        return $this;
    }

    /**
     * @param  callable  $callback
     * @return static
     */
    public function nested(callable $callback = null)
    {
        if ($this->withNestedTools != 'set_done') {
            if ($this->card) {
                $this->card->nestedTools();
            }
        } else {
            $this->withNestedTools = true;
        }

        $callback = function (Nested $nested) use ($callback) {
            $this->nested = $nested;
            $this->callCallBack($callback);
        };
        if ($this->card) {
            $this->card->body()->nested($callback);
        } else {
            $this->component->nested($callback);
        }
        return $this;
    }

    /**
     * @param  callable  $callback
     * @return static
     */
    public function ordered(callable $callback = null)
    {
        $this->withNestedTools = false;

        $callback = function (Nested $nested) use ($callback) {
            $this->nested = $nested;
            $this->callCallBack($callback);
        };
        if ($this->card) {
            $this->card->body()->nested($callback);
        } else {
            $this->component->nested($callback);
        }
        return $this;
    }

    /**
     * @param  callable  $callback
     * @return static
     */
    public function form(callable $callback = null)
    {
        $callback = function (Form $form) use ($callback) {
            $this->form = $form;
            $this->callCallBack($callback);
        };
        if ($this->card) {
            $this->card->bodyForm($callback)->footerForm();
        } else {
            $this->component->form($callback);
        }
        return $this;
    }

    /**
     * @param  callable  $callback
     * @return static
     */
    public function info(callable $callback = null)
    {
        $callback = function (ModelInfoTable $table) use ($callback) {
            $this->infoTable = $table;
            $this->callCallBack($callback);
        };
        if ($this->card) {
            $this->card->fullBody()->model_info_table($callback);
        } else {
            $this->component->model_info_table($callback);
        }
        return $this;
    }

    /**
     * @param  callable  $callback
     * @return static
     */
    public function buttons(callable $callback = null)
    {
        $this->buttonGroup = $this->card
            ? $this->card->group()
            : $this->component->button_group();
        $this->callCallBack($callback);
        return $this;
    }

    /**
     * @param  callable|null  $callback
     * @return static
     */
    public function chartjs(callable $callback = null)
    {
        if ($this->card) {
            $this->card->fullBody()->chart_js($callback);
        } else {
            $this->component->chart_js($callback);
        }
        return $this;
    }

    /**
     * @param  callable|null  $callback
     * @return static
     */
    public function periods(callable $callback = null)
    {
        if ($this->card) {
            $this->card->body()->statistic_periods($callback);
        } else {
            $this->component->statistic_periods($callback);
        }
        return $this;
    }


    public function whenNeed(string $method, callable $callback = null, $default = null)
    {
        $this->when(request()->has('method') ? request('method') == $method : $default, function (LtePage $page) use ($method, $callback) {
            $page->{$method}($callback);
        });

        return $this;
    }


    /**
     * @param $test
     * @return $this
     */
    public function withTools($test = null)
    {
        if ($this->card) {
            $this->card->defaultTools($test);
            if ($this->withNestedTools) {
                $this->card->nestedTools();
                $this->withNestedTools = 'set_done';
            }
        }
        return $this;
    }





    protected function callCallBack(callable $callback = null)
    {
        if ($callback && is_embedded_call($callback)) {
            embedded_call($callback, [
                ModelInfoTable::class => $this->infoTable,
                ButtonGroup::class => $this->buttonGroup,
                SearchForm::class => $this->searchForm,
                ModelTable::class => $this->modelTable,
                Nested::class => $this->nested,
                Form::class => $this->form,
                Card::class => $this->card,
                DIV::class => $this->component,
                static::class => $this
            ]);
        }
        return $this;
    }
}
