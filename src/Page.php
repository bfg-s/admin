<?php

namespace Lar\LteAdmin;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\Component;
use Lar\LteAdmin\Components\Contents\CardContent;
use Lar\LteAdmin\Controllers\Controller;
use Lar\LteAdmin\Core\Container;
use Lar\LteAdmin\Core\Delegate;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Interfaces\ControllerContentInterface;

/**
 * @template CurrentModel
 * @macro_return Lar\LteAdmin\Page
 * @methods Lar\LteAdmin\Controllers\Controller::$explanation_list (...$delegates)
 * @mixin PageMacroList
 * @mixin PageMethods
 */
class Page extends Container
{
    use Macroable, Delegable;

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
     * The last content component.
     * @var string
     */
    protected $content;

    /**
     * Sheet constructor.
     * @param  Router  $router
     * @throws \Throwable
     */
    public function __construct(
        Router $router
    ) {
        parent::__construct(null);
        $this->router = $router;
        $this->content = DIV::class;
        $this->registerClass($this->component);
        if ($this->router->current()) {
            $controller = $this->router->current()->controller;
            if ($controller && method_exists($controller, 'explanation')) {
                $this->explanation([$controller, 'explanation']);
            }
        }
    }

    /**
     * @return CurrentModel|Builder|Model|Relation|mixed
     */
    public function model()
    {
        return gets()->lte->menu->model;
    }

    public function next(...$delegates): static
    {
        $this->content = DIV::class;

        $this->forgetClass(CardContent::class);

        $this->explanation(
            Explanation::new($delegates)
        );

        $this->registerClass($this->component);

        return $this;
    }

    /**
     * @param Explanation|callable $extend
     * @return static
     */
    public function explanation($extend): self
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
     * @return mixed
     */
    public function callCallBack(callable $callback = null, object $registerBefore = null): mixed
    {
        if ($registerBefore) {
            $this->registerClass($registerBefore);
        }
        if ($callback && is_callable($callback)) {
            return embedded_call($callback, array_merge([
                static::class => $this,
            ], $this->classes));
        }
        return null;
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
        if ($class instanceof ControllerContentInterface) {
            $this->content = $className;
        }
        $this->applyExplanations($className, $class);

        return $class;
    }

    /**
     * @template RegisteredObject
     * @template RegisteredDefaultObject
     * @param  RegisteredObject|string $class
     * @param  RegisteredDefaultObject|null  $default
     * @return mixed|null|RegisteredObject|RegisteredDefaultObject
     */
    public function getClass(string $class, mixed $default = null)
    {
        $class = $class === 'content' ? $this->content : $class;
        return $this->classes[$class] ?? $default;
    }

    /**
     * @return Component|string|null
     */
    public function getContent()
    {
        return $this->getClass('content');
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
     * @param  string  $class
     * @return $this
     */
    public function forgetClass(string $class)
    {
        if ($this->hasClass($class)) {

            unset($this->classes[$class]);
        }

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return static
     * @throws \Exception|\Throwable
     */
    public function __call($name, $arguments)
    {
        $args = function () use ($arguments) {
            $callbacks = [];
            $delegates = [];
            foreach ($arguments as $argument) {
                if (is_callable($argument)) {
                    $callbacks[] = $argument;
                } else if ($argument instanceof Delegate) {
                    $delegates[] = $argument;
                }
            }
            return [$callbacks, $delegates];
        };

        if (Controller::hasExtend($name) && !static::hasMacro($name)) {
            list($callbacks, $delegates) = $args();
            Controller::applyExtend($this, $name, $delegates);
            array_map([$this, 'callCallBack'], $callbacks);
        } else if (str_ends_with($name, "_by_default")) {
            $name = str_replace("_by_default", "", $name);
            if (!request()->has('method') || request('method') == $name) {
                list($callbacks, $delegates) = $args();
                $this->registerClass($this->{$name}());
                $this->explainForClasses($delegates);
                array_map([$this, 'callCallBack'], $callbacks);
            }
        } else if (str_ends_with($name, "_by_request")) {
            $name = str_replace("_by_request", "", $name);
            if (request()->has('method') && request('method') == $name) {
                list($callbacks, $delegates) = $args();
                $this->registerClass($this->{$name}());
                $this->explainForClasses($delegates);
                array_map([$this, 'callCallBack'], $callbacks);
            }
        } else {
            if (! static::hasMacro($name)) {
                throw new BadMethodCallException(sprintf(
                    'Method %s::%s does not exist.', static::class, $name
                ));
            }
            $macro = self::$macros[$name];
            if ($macro instanceof \Closure) {
                return call_user_func_array($macro->bindTo($this, self::class), $arguments);
            }

            $macro(...$arguments);
        }

        return $this;
    }

    protected function explainForClasses(array $delegates = [])
    {
        foreach ($this->classes as $className => $class) {

            Explanation::new($delegates)->applyFor($className, $class);
        }

        return $this;
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
