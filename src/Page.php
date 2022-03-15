<?php

namespace LteAdmin;

use BadMethodCallException;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Lar\Layout\Tags\DIV;
use LteAdmin\Components\CardComponent;
use LteAdmin\Components\Component;
use LteAdmin\Components\SearchFormComponent;
use LteAdmin\Controllers\Controller;
use LteAdmin\Core\Container;
use LteAdmin\Traits\Delegable;
use LteAdmin\Traits\Macroable;
use Throwable;

/**
 * @template CurrentModel
 * @macro_return LteAdmin\Page
 * @methods LteAdmin\Controllers\Controller::$explanation_list (...$delegates) LteAdmin\Page
 * @mixin PageMacroList
 * @mixin PageMethods
 */
class Page extends Container
{
    use Macroable;
    use Delegable;

    /**
     * Has models on process.
     * @var array
     */
    protected static $models = [];
    public ?array $menu;
    public ?Collection $menus = null;
    public ?Controller $controller = null;
    public ?string $controllerClassName = null;
    public ?string $resource_type = null;
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
     * @var string|null
     */
    protected $model_class = null;

    /**
     * Sheet constructor.
     * @param  Router  $router
     * @throws Throwable
     */
    public function __construct(
        Router $router
    ) {
        parent::__construct(null);
        $this->router = $router;
        $this->content = DIV::class;
        $this->menus = admin_repo()->nestedCollect;
        $this->menu = admin_repo()->now;
        $this->resource_type = admin_repo()->type;
        $this->model(admin_repo()->modelNow);
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
            $model = $model->makeModel($this->model ?: admin_repo()->modelNow);
        }
        if ($model !== null) {
            $this->model = is_callable($model)
                ? call_user_func($model)
                : (is_string($model) ? new $model() : $model);
        }

        return $this->model ?? $this->model = admin_repo()->modelNow;
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

    protected function applyExplanations(string $class, object $instance)
    {
        foreach ($this->explanations as $key => $explanation) {
            $explanation->applyFor($class, $instance);
            if ($explanation->isEmpty()) {
                unset($this->explanations[$key]);
            }
        }
    }

    /**
     * @param  Explanation|callable  $extend
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

    public function menu()
    {
        $model = $this->menu['model_class'] ?? null;
        if ($model && $this->model_class != $model) {
            $this->menu = $this->menus->where('model_class', $this->model_class)->first();
        }

        return $this->menu;
    }

    public function findModelMenu(string $model)
    {
        return $this->menus->where('model_class', $model)->first();
    }

    /**
     * @return false|mixed|string|null
     */
    public function getModelName($model = null)
    {
        $class = null;
        $model = $model ?: $this->model;
        if ($model instanceof Model) {
            $class = get_class($model);
        } elseif ($model instanceof Builder) {
            $class = get_class($model->getModel());
        } elseif ($model instanceof Relation) {
            $class = get_class($model->getModel());
        } elseif (is_object($model)) {
            $class = get_class($model);
        } elseif (is_string($model)) {
            $class = $model;
        } elseif (is_array($model)) {
            $class = substr(md5(json_encode($model)), 0, 10);
        }
        $this->model_class = $class;
        $return = $class ? strtolower(class_basename($class)) : 'object_'.spl_object_id($this);
        $prep = '';
        if (isset(static::$models[$return])) {
            $prep .= admin_repo()->modelNow?->id ?? static::$models[$return];
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
     * @param  string  $class
     * @return bool
     */
    public function hasClass(string $class): bool
    {
        return isset($this->classes[$class]);
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
     * @param $name
     * @param $arguments
     * @return static
     * @throws Exception|Throwable
     */
    public function __call($name, $arguments)
    {
        if (Controller::hasExtend($name) && !static::hasMacro($name)) {
            $this->registerClass(
                $this->getContent()->{$name}(...$arguments) //->addClass('col-12 p-0')
            );
        } elseif (str_ends_with($name, '_by_default')) {
            $name = str_replace('_by_default', '', $name);
            if (!request()->has('method') || request('method') == $name) {
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
            if (!static::hasMacro($name)) {
                throw new BadMethodCallException(sprintf(
                    'Method %s::%s does not exist.',
                    static::class,
                    $name
                ));
            }
            $macro = self::$macros[$name];
            if ($macro instanceof Closure) {
                return call_user_func_array($macro->bindTo($this, self::class), $arguments);
            }

            $macro(...$arguments);
        }

        return $this;
    }

    /**
     * @return Component|string|null
     */
    public function getContent()
    {
        return $this->getClass('content');
    }

    /**
     * @template RegisteredObject
     * @template RegisteredDefaultObject
     * @param  RegisteredObject|string  $class
     * @param  RegisteredDefaultObject|null  $default
     * @return mixed|null|RegisteredObject|RegisteredDefaultObject
     */
    public function getClass(string $class, mixed $default = null)
    {
        $class = $class === 'content' ? $this->content : $class;

        return $this->classes[$class] ?? $default;
    }

    protected function explainForClasses(array $delegates = [])
    {
        foreach ($this->classes as $className => $class) {
            Explanation::new($delegates)->applyFor($className, $class);
        }

        return $this;
    }
}
