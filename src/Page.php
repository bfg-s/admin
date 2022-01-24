<?php

namespace Lar\LteAdmin;

use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\CardComponent;
use Lar\LteAdmin\Components\Component;
use Lar\LteAdmin\Components\SearchFormComponent;
use Lar\LteAdmin\Controllers\Controller;
use Lar\LteAdmin\Core\Container;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;

/**
 * @template CurrentModel
 * @macro_return Lar\LteAdmin\Page
 * @methods Lar\LteAdmin\Controllers\Controller::$explanation_list (...$delegates) Lar\LteAdmin\Page|\App\LteAdmin\Page
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
     * @var Model|null
     */
    protected $model = null;

    /**
     * Has models on process.
     * @var array
     */
    protected static $models = [];

    public ?Controller $controller = null;
    public ?string $controllerClassName = null;

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
            $this->controller = $this->router->current()->controller;
            $this->controllerClassName = get_class($this->controller);
            if ($this->controller && method_exists($this->controller, 'explanation')) {
                $this->explanation([$this->controller, 'explanation']);
            }
        }
    }

    /**
     * @return CurrentModel|Builder|Model|Relation|mixed
     */
    public function model($model = null)
    {
        if ($model instanceof SearchFormComponent) {
            $model = $model->makeModel($this->model ?: gets()->lte->menu->model);
        }
        if ($model !== null) {
            $this->model = is_callable($model)
                ? call_user_func($model)
                : (is_string($model) ? new $model : $model);
        }

        return $this->model ?: gets()->lte->menu->model;
    }

    /**
     * @return false|mixed|string|null
     */
    public function getModelName()
    {
        $class = null;
        if ($this->model() instanceof Model) {
            $class = get_class($this->model());
        } elseif ($this->model() instanceof Builder) {
            $class = get_class($this->model()->getModel());
        } elseif ($this->model() instanceof Relation) {
            $class = get_class($this->model()->getModel());
        } elseif (is_object($this->model())) {
            $class = get_class($this->model());
        } elseif (is_string($this->model())) {
            $class = $this->model();
        } elseif (is_array($this->model())) {
            $class = substr(md5(json_encode($this->model())), 0, 10);
        }
        $this->model_class = $class;
        $return = $class ? strtolower(class_basename($class)) : 'object_'.spl_object_id($this);
        $prep = '';
        if (isset(static::$models[$return])) {
            $prep .= $this->model()?->id ?? static::$models[$return];
            static::$models[$return]++;
        } else {
            static::$models[$return] = 1;
        }

        return $return.$prep;
    }

    public function next(...$delegates): static
    {
        $this->content = DIV::class;

        $this->forgetClass(CardComponent::class);

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
        if (Controller::hasExtend($name) && ! static::hasMacro($name)) {
            $this->registerClass(
                $this->getContent()->{$name}(...$arguments) //->addClass('col-12 p-0')
            );
        } elseif (str_ends_with($name, '_by_default')) {
            $name = str_replace('_by_default', '', $name);
            if (! request()->has('method') || request('method') == $name) {
                $this->registerClass($this->{$name}());
                $this->explainForClasses($arguments);
            }
        } elseif (str_ends_with($name, '_by_request')) {
            $name = str_replace('_by_request', '', $name);
            if (request()->has('method') && request('method') == $name) {
                $this->registerClass($this->{$name}());
                $this->explainForClasses($arguments);
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
