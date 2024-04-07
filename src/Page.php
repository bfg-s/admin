<?php

declare(strict_types=1);

namespace Admin;

use Admin\Components\CardComponent;
use Admin\Components\PageComponents;
use Admin\Components\Component;
use Admin\Components\SearchFormComponent;
use Admin\Controllers\Controller;
use Admin\Core\Container;
use Admin\Core\MenuItem;
use Admin\Traits\Delegable;
use BadMethodCallException;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Throwable;

/**
 * @template CurrentModel
 * @mixin PageComponents
 */
class Page extends Container
{
    use Delegable;

    /**
     * Has models on process.
     * @var array
     */
    protected static array $models = [];

    /**
     * @var MenuItem|null
     */
    public ?MenuItem $menu;

    /**
     * @var Collection|MenuItem[]|mixed|null
     */
    public ?Collection $menus = null;

    /**
     * @var Controller|mixed|null
     */
    public ?Controller $controller = null;

    /**
     * @var string|null
     */
    public ?string $controllerClassName = null;

    /**
     * @var string|mixed|null
     */
    public ?string $resource_type = null;

    /**
     * @var array
     */
    protected array $classes = [];

    /**
     * @var Explanation[]
     */
    protected array $explanations = [];

    /**
     * @var Router
     */
    protected Router $router;

    /**
     * The last content component.
     * @var string
     */
    protected string $content;

    /**
     * @var Model|null
     */
    protected ?Model $model = null;

    /**
     * @var string|null
     */
    protected ?string $model_class = null;

    /**
     * @var mixed|array|null
     */
    protected mixed $firstExplanation = null;

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
        $this->menus = admin_repo()->menuList;
        $this->menu = admin_repo()->now;
        $this->resource_type = admin_repo()->type;
        $this->model(admin_repo()->modelNow);
        if ($this->router->current()) {
            $this->controller = $this->router->current()->controller;
            $this->controllerClassName = get_class($this->controller);
            if ($this->controller && method_exists($this->controller, 'explanationForFirstCard')) {
                $this->firstExplanation = [$this->controller, 'explanationForFirstCard'];
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

    /**
     * @param  string  $class
     * @param  object  $instance
     * @return void
     */
    protected function applyExplanations(string $class, object $instance): void
    {
        foreach ($this->explanations as $key => $explanation) {
            $explanation->applyFor($class, $instance);
            if ($explanation->isEmpty()) {
                unset($this->explanations[$key]);
            }
        }
    }

    /**
     * @param  callable|Explanation  $extend
     * @return static
     */
    public function explanation(callable|Explanation $extend): self
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
     * @return MenuItem|null
     */
    public function menu(): ?MenuItem
    {
        $model = $this->menu->getModelClass();
        if ($model && $this->model_class != $model) {
            $this->menu = $this->menus->where('model_class', $this->model_class)->first();
        }

        return $this->menu;
    }

    /**
     * @param  string  $model
     * @return mixed
     */
    public function findModelMenu(string $model): mixed
    {
        return $this->menus->where('model_class', $model)->first();
    }

    /**
     * @param  null  $model
     * @return string
     */
    public function getModelName($model = null): string
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

    /**
     * @param ...$delegates
     * @return $this
     */
    public function next(...$delegates): static
    {
        $this->explanation(
            Explanation::new($delegates)
        );

        return $this;
    }

    /**
     * @param  string  $class
     * @return $this
     */
    public function forgetClass(string $class): static
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
     * @throws Throwable
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
        if (isset(Component::$components[$name])) {

            $component = Component::$components[$name];

            /*** @var Component $component **/
            $component = new $component(...$arguments);

            $component->model($this->model);

            if (!$component instanceof Component) {
                throw new Exception('Component is not admin part');
            }

            if ($this->firstExplanation && $name == 'card') {
                $component->explain(call_user_func($this->firstExplanation));
                $this->firstExplanation = null;
            }

            $this->contents[] = $component;

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
    public function getContent(): Component|string|null
    {
        return $this->getClass('content');
    }

    /**
     * @template RegisteredObject
     * @template RegisteredDefaultObject
     * @param  RegisteredObject|string  $class
     * @param  RegisteredDefaultObject|null  $default
     * @return mixed|RegisteredObject|RegisteredDefaultObject
     */
    public function getClass(string $class, mixed $default = null): mixed
    {
        $class = $class === 'content' ? $this->content : $class;

        return $this->classes[$class] ?? $default;
    }

    /**
     * @param  array  $delegates
     * @return $this
     */
    protected function explainForClasses(array $delegates = []): static
    {
        foreach ($this->classes as $className => $class) {
            Explanation::new($delegates)->applyFor($className, $class);
        }

        return $this;
    }

    /**
     * @return Closure
     */
    public function withTools(): Closure
    {
        return function ($test = null) {
            if ($this->hasClass(CardComponent::class)) {
                $this->getClass(CardComponent::class)->defaultTools($test);
            }

            return $this;
        };
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }
}
