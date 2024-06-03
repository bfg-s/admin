<?php

declare(strict_types=1);

namespace Admin;

use Admin\Core\Delegate;
use Illuminate\Routing\Router;
use Illuminate\Support\Traits\Conditionable;

/**
 * Main explanation class. Designed for field management.
 *
 * @method Explanation name(string $name) Set the name of field (database column)
 * @method Explanation label(string $name) Set the name of field (database column)
 */
final class Explanation
{
    use Conditionable;

    /**
     * List of declarations added to the explanation.
     *
     * @var Delegate[]
     */
    public array $delegates = [];

    /**
     * Current application router.
     *
     * @var Router
     */
    protected Router $router;

    /**
     * Explanation constructor.
     *
     * @param  Router  $router
     */
    public function __construct(
        Router $router
    ) {
        $this->router = $router;
    }

    /**
     * Method for creating a new explanation instance.
     *
     * @param ...$delegates
     * @return $this
     */
    public static function new(...$delegates): Explanation
    {
        $field = app(self::class);

        return $field->with(...$delegates);
    }

    /**
     * Method for adding delegations to the current list of delegations.
     *
     * @param  array|Delegate|null  $delegate
     * @return $this
     */
    public function with(Delegate|array $delegate = null): Explanation
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

    /**
     * Add delegations only if there is an index on the router.
     *
     * @param ...$delegates
     * @return $this
     */
    public function index(...$delegates): Explanation
    {
        if ($this->router->currentRouteNamed('*.index')) {
            $this->with(...$delegates);
        }

        return $this;
    }

    /**
     * Add delegations only if there is editing or adding on the router.
     *
     * @param ...$delegates
     * @return Explanation
     */
    public function form(...$delegates): Explanation
    {
        return $this->edit(...$delegates)->create(...$delegates);
    }

    /**
     * Add delegations only if you are creating a router.
     *
     * @param ...$delegates
     * @return $this
     */
    public function create(...$delegates): Explanation
    {
        if (
            $this->router->currentRouteNamed('*.create')
            || $this->router->currentRouteNamed('*.store')
        ) {
            $this->with(...$delegates);
        }

        return $this;
    }

    /**
     * Add delegations only if you are editing on the router.
     *
     * @param ...$delegates
     * @return $this
     */
    public function edit(...$delegates): Explanation
    {
        if (
            $this->router->currentRouteNamed('*.edit')
            || $this->router->currentRouteNamed('*.update')
        ) {
            $this->with(...$delegates);
        }

        return $this;
    }

    /**
     * Add delegations only if shown on the router.
     *
     * @param ...$delegates
     * @return $this
     */
    public function show(...$delegates): Explanation
    {
        if ($this->router->currentRouteNamed('*.show')) {
            $this->with(...$delegates);
        }

        return $this;
    }

    /**
     * Apply delegations to the specified implementation.
     *
     * @param  string  $class
     * @param  object  $instance
     * @return $this
     */
    public function applyFor(string $class, object $instance): Explanation
    {
        foreach ($this->delegates as $key => $delegate) {
            if ($delegate->class == $class || $class == '*') {
                $this->apply($delegate, $instance);
                unset($this->delegates[$key]);
            }
        }

        return $this;
    }

    /**
     * Apply delegation to the specified implementation.
     *
     * @param  Delegate  $delegate
     * @param  object  $instance
     * @return $this
     */
    protected function apply(Delegate $delegate, object $instance): Explanation
    {
        foreach ($delegate->methods as $method) {
            $instance = $instance->{$method[0]}(...$method[1]);
        }

        return $this;
    }

    /**
     * Check if delegations are empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return !count($this->delegates);
    }
}
