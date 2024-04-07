<?php

declare(strict_types=1);

namespace Admin;

use Illuminate\Routing\Router;
use Illuminate\Support\Traits\Conditionable;
use Admin\Components\FieldInputTypesMethods;
use Admin\Core\Delegate;

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

    public static function new(...$delegates): self
    {
        $field = app(self::class);

        return $field->with(...$delegates);
    }

    /**
     * @param  array|Delegate  $delegate
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
                    $item = app()->call($item);
                }
                if (is_array($item)) {
                    $this->with($item);
                } elseif ($item instanceof Delegate) {
                    $this->delegates[] = $item;
                } elseif ($item instanceof self) {
                    $this->delegates = array_merge($this->delegates, $item->delegates);
                }
            }
        }

        return $this;
    }

    public function index(...$delegates)
    {
        if ($this->router->currentRouteNamed('*.index')) {
            $this->with(...$delegates);
        }

        return $this;
    }

    public function form(...$delegates)
    {
        return $this->edit(...$delegates)->create(...$delegates);
    }

    public function create(...$delegates)
    {
        if (
            $this->router->currentRouteNamed('*.create')
            || $this->router->currentRouteNamed('*.store')
        ) {
            $this->with(...$delegates);
        }

        return $this;
    }

    public function edit(...$delegates)
    {
        if (
            $this->router->currentRouteNamed('*.edit')
            || $this->router->currentRouteNamed('*.update')
        ) {
            $this->with(...$delegates);
        }

        return $this;
    }

    public function show(...$delegates)
    {
        if ($this->router->currentRouteNamed('*.show')) {
            $this->with(...$delegates);
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

    protected function apply(Delegate $delegate, object $instance)
    {
        foreach ($delegate->methods as $method) {
            $instance = $instance->{$method[0]}(...$method[1]);
        }
    }

    public function isEmpty()
    {
        return !count($this->delegates);
    }
}
