<?php

namespace Lar\LteAdmin;

use Closure;
use Illuminate\Routing\Router;
use Lar\LteAdmin\Core\Delegate;
use Illuminate\Support\Traits\Conditionable;
use Lar\LteAdmin\Segments\Tagable\FieldInputTypesMethods;

/**
 * @method Explanation name(string $name) Set the name of field (database column)
 * @method Explanation label(string $name) Set the name of field (database column)
 */
final class Explanation
{
    use Conditionable;

    /**
     * @var Delegate[]
     */
    public $delegates = [];

    /**
     * @var Router
     */
    protected $router;

    /**
     * @param  Router  $router
     */
    public function __construct(
        Router $router
    ) {
        $this->router = $router;
    }

    public static function new(...$delegates): Explanation
    {
        $field = app(Explanation::class);
        return $field->with(...$delegates);
    }

    public function index(...$delegates)
    {
        if ($this->router->currentRouteNamed("*.index")) {

            $this->with(...$delegates);
        }

        return $this;
    }

    public function create(...$delegates)
    {
        if ($this->router->currentRouteNamed("*.create")) {

            $this->with(...$delegates);
        }

        return $this;
    }

    public function edit(...$delegates)
    {
        if ($this->router->currentRouteNamed("*.edit")) {

            $this->with(...$delegates);
        }

        return $this;
    }

    public function form(...$delegates)
    {
        return $this->edit(...$delegates)->create(...$delegates);
    }

    public function show(...$delegates)
    {
        if ($this->router->currentRouteNamed("*.show")) {

            $this->with(...$delegates);
        }

        return $this;
    }

    /**
     * @param array|Delegate $delegate
     * @return $this
     */
    public function with($delegate = null)
    {
        if ($delegate) {
            if (!is_array($delegate)) {
                $delegate = func_get_args();
            }
            foreach ($delegate as $item) {
                if (is_callable($item)) {
                    $item = call_user_func($item);
                }
                if (is_array($item)) {
                    $this->with($item);
                } else if ($item instanceof Delegate) {
                    $this->delegates[] = $item;
                } else if ($item instanceof Explanation) {
                    $this->delegates = array_merge($this->delegates, $item->delegates);
                }
            }
        }
        return $this;
    }

    public function applyFor(string $class, object $instance)
    {
        foreach ($this->delegates as $key => $delegate) {
            if ($delegate->class == $class || $class == '*') {
                $this->apply($delegate, $instance);
                unset($this->delegates[$key]);
            }
        }
        return $this;
    }

    public function isEmpty()
    {
        return !count($this->delegates);
    }

    protected function apply(Delegate $delegate, object $instance)
    {
        foreach ($delegate->methods as $method) {
            $instance = $instance->{$method[0]}(...$method[1]);
        }
    }
}
