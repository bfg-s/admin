<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;
use Lar\Layout\Traits\FontAwesome;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Segments\Tagable\Traits\BuildHelperTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\FieldMassControl;
use Lar\Tagable\Events\onRender;

/**
 * Class StatisticsPeriods.
 * @package Lar\LteAdmin\Segments\Tagable
 * @methods Lar\LteAdmin\Segments\Tagable\Field::$form_components (string $name, string $label = null, ...$params)
 * @mixin StatisticsPeriodsMacroList
 * @mixin StatisticsPeriodsMethods
 */
class StatisticPeriods extends DIV implements onRender
{
    use FieldMassControl, Macroable, BuildHelperTrait, FontAwesome, Delegable;

    /**
     * @var string
     */
    protected $class = 'row';
    protected $model;
    protected $entity;
    protected $icon;

    /**
     * StatisticsPeriods constructor.
     * @param string|null $model
     * @param  string|null  $nameOfSubject
     * @param  string  $icon
     * @param  mixed  ...$params
     */
    public function __construct($model = null, string $nameOfSubject = null, string $icon = 'fas fa-calendar-alt', ...$params)
    {
        parent::__construct();

        $this->model = $model;

        if (! $this->model) {
            $this->model = gets()->lte->menu->model;
        } elseif (is_string($this->model)) {
            $this->model = new $this->model;
        }

        $this->entity = __($nameOfSubject);

        if (! $this->entity && $this->model) {
            $this->entity = \Str::plural(class_basename($this->model::class));
        }

        $this->icon = $icon;

        $this->when($params);

        $this->addClass($this->class);

        $this->callConstructEvents();
    }

    public function forToday()
    {
        $this->col()
            ->info_box(
                __('lte.statistic_for_today', ['entity' => $this->entity]),
                $this->model::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count().' ',
                $this->icon
            )->success();

        return $this;
    }

    public function perWeek()
    {
        $this->col()
            ->info_box(
                __('lte.statistic_per_week', ['entity' => $this->entity]),
                $this->model::whereBetween('created_at', [now()->subWeek()->startOfDay(), now()->endOfDay()])->count().' ',
                $this->icon
            )->info();

        return $this;
    }

    public function perYear()
    {
        $this->col()
            ->info_box(
                __('lte.statistic_per_year', ['entity' => $this->entity]),
                $this->model::whereBetween('created_at', [now()->subYear()->startOfDay(), now()->endOfDay()])->count().' ',
                $this->icon
            )->warning();

        return $this;
    }

    public function total()
    {
        $this->col()
            ->info_box(
                __('lte.statistic_total', ['entity' => strtolower($this->entity)]),
                $this->model::count(),
                $this->icon
            )->primary();

        return $this;
    }

    /**
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|Form|\Lar\Tagable\Tag|mixed|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if ($call = $this->call_group($name, $arguments)) {
            return $call;
        }

        return parent::__call($name, $arguments);
    }

    /**
     * @return mixed|void
     * @throws \ReflectionException
     */
    public function onRender()
    {
        $this->callRenderEvents();
    }
}
