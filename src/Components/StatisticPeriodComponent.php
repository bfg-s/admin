<?php

namespace Lar\LteAdmin\Components;

use Exception;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Traits\FontAwesome;
use Lar\LteAdmin\Components\Traits\BuildHelperTrait;
use Lar\LteAdmin\Components\Traits\FieldMassControlTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Page;
use Lar\Tagable\Events\onRender;
use Lar\Tagable\Tag;
use ReflectionException;
use Str;

/**
 * @methods Lar\LteAdmin\Components\FieldComponent::$inputs (string $name, string $label = null, ...$params)
 * @mixin StatisticPeriodComponentMacroList
 * @mixin StatisticPeriodComponentMethods
 */
class StatisticPeriodComponent extends DIV implements onRender
{
    use FieldMassControlTrait, Macroable, BuildHelperTrait, FontAwesome, Delegable;

    /**
     * @var Page
     */
    public $page;
    /**
     * @var string
     */
    protected $class = 'row';
    protected $model;
    protected $entity;
    protected $icon = 'fas fa-lightbulb';

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->page = app(Page::class);

        $this->model($this->page->model());

        $this->explainForce(Explanation::new($delegates));

        $this->addClass($this->class);

        $this->callConstructEvents();
    }

    public function model($model)
    {
        $this->model = is_string($model) ? new $model : $model;

        if ($this->page->hasClass(SearchFormComponent::class)) {
            $this->model = eloquent_instruction(
                $this->model,
                $this->page->getClass(SearchFormComponent::class)
            );
        }

        $this->entity = Str::plural(class_basename($this->model::class));

        return $this;
    }

    public function title(string $nameOfSubject = null)
    {
        $this->entity = __($nameOfSubject);

        return $this;
    }

    public function forToday()
    {
        $this->column()
            ->info_box(
                __('lte.statistic_for_today', ['entity' => $this->entity]),
                $this->model::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count().' ',
                $this->icon
            )->successType();

        return $this;
    }

    public function perWeek()
    {
        $this->column()
            ->info_box(
                __('lte.statistic_per_week', ['entity' => $this->entity]),
                $this->model::whereBetween('created_at',
                    [now()->subWeek()->startOfDay(), now()->endOfDay()])->count().' ',
                $this->icon
            )->infoType();

        return $this;
    }

    public function perYear()
    {
        $this->column()
            ->info_box(
                __('lte.statistic_per_year', ['entity' => $this->entity]),
                $this->model::whereBetween('created_at',
                    [now()->subYear()->startOfDay(), now()->endOfDay()])->count().' ',
                $this->icon
            )->warningType();

        return $this;
    }

    public function total()
    {
        $this->column()
            ->info_box(
                __('lte.statistic_total', ['entity' => mb_strtolower($this->entity)]),
                $this->model::count(),
                $this->icon
            )->primaryType();

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
     * @return bool|FormComponent|Tag|mixed|string
     * @throws Exception
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
     * @throws ReflectionException
     */
    public function onRender()
    {
        $this->callRenderEvents();
    }
}
