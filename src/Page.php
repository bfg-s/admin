<?php

declare(strict_types=1);

namespace Admin;

use Admin\Components\Component;
use Admin\Components\PageComponents;
use Admin\Components\SearchFormComponent;
use Admin\Controllers\Controller;
use Admin\Core\PageContainer;
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
 * The main file of the page where all components are placed.
 * The main page container class.
 *
 * @template CurrentModel
 * @mixin PageComponents
 */
class Page extends PageContainer
{
    use Delegable;

    /**
     * Has models on process.
     *
     * @var array
     */
    protected static array $models = [];

    /**
     * The menu item that is currently active or selected.
     *
     * @var MenuItem|null
     */
    public MenuItem|null $menu;

    /**
     * List of all menu items present on the page.
     *
     * @var Collection|MenuItem[]|mixed|null
     */
    public Collection|null $menus = null;

    /**
     * The current application controller.
     *
     * @var Controller|mixed|null
     */
    public Controller|null $controller = null;

    /**
     * The current name of the application controller class.
     *
     * @var string|null
     */
    public string|null $controllerClassName = null;

    /**
     * The application's current operation type (add, edit, view, index...).
     *
     * @var string|mixed|null
     */
    public string|null $resource_type = null;

    /**
     * Child classes that should be displayed on the page
     *
     * @var array
     */
    protected array $classes = [];

    /**
     * List of explanations for nested child classes.
     *
     * @var Explanation[]
     */
    protected array $explanations = [];

    /**
     * Current application router.
     *
     * @var Router
     */
    protected Router $router;

    /**
     * The last content component.
     *
     * @var string
     */
    protected string $content;

    /**
     * The main current model to which the page belongs.
     *
     * @var Model|null
     */
    protected Model|null $model = null;

    /**
     * The class name of the main current model to which the page belongs.
     *
     * @var string|null
     */
    protected string|null $model_class = null;

    /**
     * Explanations of what should be done first after creating a component on a page.
     *
     * @var mixed|array|null
     */
    protected mixed $firstExplanation = null;

    /**
     * Page constructor.
     *
     * @param  Router  $router
     * @throws Throwable
     */
    public function __construct(Router $router)
    {
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
     * Install your model or get the current model.
     *
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
     * Get and assign the current menu item.
     *
     * @return MenuItem|null
     */
    public function menu(): MenuItem|null
    {
        $model = $this->menu->getModelClass();

        if ($model && $this->model_class != $model) {

            $this->menu = $this->findModelMenu($this->model_class);
        }

        return $this->menu;
    }

    /**
     * Find a menu item that belongs to the specified model class.
     *
     * @param  string  $model
     * @return mixed
     */
    public function findModelMenu(string $model): mixed
    {
        return $this->menus->where('model_class', $model)->first();
    }

    /**
     * Get the unique name of the page model.
     *
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
     * Get the current page model.
     *
     * @return Model|null
     */
    public function getModel(): Model|null
    {
        return $this->model;
    }

    /**
     * Follow these steps with explanations.
     *
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
     * Add an explanation to the list with explanations.
     *
     * @param  Explanation|callable  $extend
     * @return static
     */
    public function explanation(Explanation|callable $extend): static
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
     * Forget the specified children's class.
     *
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
     * Check if the specified child class exists.
     *
     * @param  string  $class
     * @return bool
     */
    public function hasClass(string $class): bool
    {
        return isset($this->classes[$class]);
    }

    /**
     * Apply a callback to the current page.
     *
     * @param  callable|null  $callback
     * @param  object|null  $registerBefore
     * @return static
     * @throws Throwable
     */
    public function callCallBack(callable $callback = null, object $registerBefore = null): static
    {
        if ($registerBefore) {
            $this->registerClass($registerBefore);
        }
        if ($callback && is_callable($callback)) {
            return embedded_call($callback, array_merge([
                static::class => $this,
            ], $this->classes));
        }

        return $this;
    }

    /**
     * Register your children's class.
     *
     * @template RegisteredObject
     * @param  object|RegisteredObject  $class
     * @return object|RegisteredObject
     */
    public function registerClass(object $class): object
    {
        $className = get_class($class);
        $this->classes[$className] = $class;
        $this->applyExplanations($className, $class);

        return $class;
    }

    /**
     * Applies existing explanations to the specified class.
     *
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
     * A magic method that is responsible for filling this page with content when we magically write the name of our component.
     *
     * @param $name
     * @param $arguments
     * @return static
     * @throws Exception|Throwable
     */
    public function __call($name, $arguments)
    {
        if (isset(Component::$components[$name])) {
            $component = Component::$components[$name];

            /*** @var Component $component * */
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
     * Apply delegations to all classes that are currently present on the page.
     *
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
     * Get the latest content component.
     *
     * @return Component|string|null
     */
    public function getContent(): Component|string|null
    {
        return $this->getClass('content');
    }

    /**
     * Get the specified child class.
     *
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
}
