<?php

namespace Lar\LteAdmin\Components;

use Lar\Developer\Core\Traits\Piplineble;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\Contents\CardContent;
use Lar\LteAdmin\Components\Cores\NestableComponentCore;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Interfaces\ControllerContainerInterface;
use Lar\LteAdmin\Page;
use Lar\Tagable\Events\onRender;

/**
 * @mixin NestedComponentMacroList
 */
class NestedComponent extends DIV implements onRender, ControllerContainerInterface
{
    use Macroable, Piplineble, Delegable;

    /**
     * @var bool
     */
    protected $only_content = true;

    /**
     * @var NestableComponentCore
     */
    protected $nested;
    protected Page $page;
    public $model = null;

    /**
     * @param  array  $delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->page = app(Page::class);

        $this->model = $this->page->model();

        $this->nested = new NestableComponentCore($this->model);

        $this->explainForce(Explanation::new($delegates));

        $this->appEnd($this->nested);

        $this->callConstructEvents();
    }

    /**
     * @param  SearchFormComponent|callable|array|null  $callback
     * @return $this
     */
    public function model(SearchFormComponent|callable|array $callback = null)
    {
        if ($callback) {

            if ($callback instanceof SearchFormComponent) {
                $callback = $callback->makeModel($this->model);
            }

            $this->nested->model($callback);
        }

        return $this;
    }

    /**
     * @param  string|null  $field
     * @return $this
     */
    public function orderDesc(string $field = null)
    {
        $this->nested->orderDesc($field);

        return $this;
    }

    /**
     * @param  string|null  $field
     * @param  string|null  $order
     * @return $this
     */
    public function orderBy(string $field = null, string $order = null)
    {
        $this->nested->orderBy($field, $order);

        return $this;
    }

    /**
     * @param  string|callable  $field
     * @return $this
     */
    public function titleField($field)
    {
        $this->nested->title_field($field);

        return $this;
    }

    /**
     * @param  string|callable  $field
     * @return $this
     */
    public function parentField($field)
    {
        $this->nested->parent_field($field);

        return $this;
    }

    /**
     * @param  int  $depth
     * @return $this
     */
    public function maxDepth(int $depth)
    {
        $this->nested->maxDepth($depth);

        return $this;
    }

    /**
     * @param  callable  $call
     * @return $this
     */
    public function controls(callable $call)
    {
        $this->nested->controls($call);

        return $this;
    }

    /**
     * @param  \Closure|array|null  $test
     * @return $this
     */
    public function disableControls($test = null)
    {
        $this->nested->disableControls($test);

        return $this;
    }

    /**
     * @param  \Closure|array|null  $test
     * @return $this
     */
    public function disableInfo($test = null)
    {
        $this->nested->disableInfo($test);

        return $this;
    }

    /**
     * @param  \Closure|array|null  $test
     * @return $this
     */
    public function disableEdit($test = null)
    {
        $this->nested->disableEdit($test);

        return $this;
    }

    /**
     * @param  \Closure|array|null  $test
     * @return $this
     */
    public function disableDelete($test = null)
    {
        $this->nested->disableDelete($test);

        return $this;
    }

    /**
     * @return mixed|void
     * @throws \ReflectionException
     */
    public function onRender()
    {
        $this->callRenderEvents();

        $this->nested->build();
    }

    public static function registrationInToContainer(Page $page, array $delegates = [], string $name = 'nested')
    {
        if ($name === 'nested') {

            $page->getClass(CardContent::class)?->nestedTools();
        }

        $page->registerClass(
            $page->getClass(CardContent::class)?->body()->nested($delegates)->model($page->getClass(SearchFormComponent::class))
            ?? $page->getContent()->nested($delegates)->model($page->getClass(SearchFormComponent::class))
        );
    }
}
