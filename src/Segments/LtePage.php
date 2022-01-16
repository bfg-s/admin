<?php

namespace Lar\LteAdmin\Segments;

use BadMethodCallException;
use Illuminate\Routing\Router;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;

/**
 * Class LtePage
 * @package Lar\LteAdmin\Segments
 * @macro_return Lar\LteAdmin\Segments\LtePage
 * @mixin LtePageMacroList
 */
class LtePage extends Container {

    use Macroable;

    /**
     * @var array
     */
    protected $classes = [];

    /**
     * @var Explanation[]
     */
    protected $explanations = [];

    /**
     * @var Router
     */
    protected $router;

    /**
     * Sheet constructor.
     * @param  Route  $route
     */
    public function __construct(
        Router $router
    )
    {
        parent::__construct(null);
        $this->router = $router;
        $this->registerClass($this->component);
        if ($this->router->current()) {
            $controller = $this->router->current()->controller;
            if ($controller && method_exists($controller, 'explanation')) {
                $this->explanation([$controller, 'explanation']);
            }
        }
    }

    /**
     * @param Explanation|callable $extend
     * @return static
     */
    public function explanation($extend): LtePage
    {
        if (is_callable($extend)) {
            $extend = call_user_func($extend);
        }
        if ($extend instanceof Explanation) {

            $this->explanations[] = $extend;
        }

        return $this;
    }

    /**
     * @param  callable|null  $callback
     * @param  object|null  $registerBefore
     * @return void
     * @throws \Throwable
     */
    public function callCallBack(callable $callback = null, object $registerBefore = null)
    {
        if ($registerBefore) {
            $this->registerClass($registerBefore);
        }
        if ($callback && is_callable($callback)) {
            embedded_call($callback, array_merge([
                static::class => $this
            ], $this->classes));
        }
    }

    /**
     * @template RegisteredObject
     * @param  object|RegisteredObject  $class
     * @return object|RegisteredObject
     */
    public function registerClass(object $class)
    {
        $className = get_class($class);
        $this->classes[$className] = $class;
        $this->applyExplanations($className, $class);
        return $class;
    }

    /**
     * @template RegisteredObject
     * @template RegisteredDefaultObject
     * @param string|RegisteredObject $class
     * @param null|RegisteredDefaultObject $default
     * @return mixed|null|RegisteredObject|RegisteredDefaultObject
     */
    public function getClass(string $class, $default = null)
    {
        return $this->classes[$class] ?? $default;
    }

    /**
     * @param  string  $class
     * @return bool
     */
    public function hasClass(string $class): bool
    {
        return isset($this->classes[$class]);
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this|bool|ModelInfoTable|\Lar\Tagable\Tag|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if (! static::hasMacro($name)) {
            throw new BadMethodCallException(sprintf(
                'Method %s::%s does not exist.', static::class, $name
            ));
        }
        $macro = LtePage::$macros[$name];
        if ($macro instanceof \Closure) {
            return call_user_func_array($macro->bindTo($this, LtePage::class), $arguments);
        }
        return $macro(...$arguments);
    }

    protected function applyExplanations(string $class, object $instance)
    {
        foreach ($this->explanations as $key => $explanation) {
            $explanation->applyFor($class, $instance);
            if ($explanation->isEmpty()) {
                unset($this->explanations[$key]);
            }
        }
    }
}
