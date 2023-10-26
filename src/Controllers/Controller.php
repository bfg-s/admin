<?php

namespace Admin\Controllers;

use Admin\Components\CardComponent;
use Admin\Components\Component;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Admin\Respond;
use Admin\Controllers\Traits\DefaultControllerResourceMethodsTrait;
use Admin\Core\Delegate;
use Admin\Exceptions\NotFoundExplainForControllerException;
use Admin\Explanation;
use Admin\Page;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function redirect;

/**
 * @property-read Page $page
 */
class Controller extends BaseController
{
    use DefaultControllerResourceMethodsTrait;

    /**
     * @var array
     */
    public static array $rules = [];

    /**
     * @var array
     */
    public static array $rule_messages = [];

    /**
     * @var array
     */
    public static array $crypt_fields = [
        'password',
    ];

    protected static bool $started = false;

    /**
     * @var array
     */
    public array $menu = [];

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->makeModelEvents();
    }

    /**
     * @return void
     */
    private function makeModelEvents(): void
    {
        if (
            property_exists($this, 'model')
            && class_exists(static::$model)
        ) {
            /** @var Model $model */
            $model = static::$model;
            $model::created(static function ($model) {
                admin_log_info('Created model', get_class($model), 'fas fa-plus');
            });
            $model::updated(static function ($model) {
                admin_log_info('Updated model', get_class($model), 'fas fa-highlighter');
            });
            $model::deleted(static function ($model) {
                admin_log_danger('Deleted model', get_class($model), 'fas fa-trash');
            });
        }
    }

    /**
     * @param  string  $name
     * @param  string  $class
     * @return void
     */
    public static function extend(string $name, string $class): void
    {
        if (!Component::hasComponentStatic($name)) {
            Component::$components[$name] = $class;
        }
    }

    /**
     * @return array
     */
    public function defaultDateRange(): array
    {
        return [
            now()->subYear()->toDateString(),
            now()->addDay()->toDateString(),
        ];
    }

    /**
     * @return Explanation
     */
    public function explanationForFirstCard(): Explanation
    {
        return Explanation::new(
            CardComponent::new()->defaultTools(
                method_exists($this, 'defaultTools') ? [$this, 'defaultTools'] : null
            )
        );
    }

    /**
     * @return Application|RedirectResponse|Redirector|Respond
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function returnTo(): mixed
    {
        if (request()->ajax() && !request()->pjax()) {
            return Respond::glob()->reload();
        }

        $_after = request()->get('_after', 'index');

        $menu = admin_repo()->now;

        if ($_after === 'index' && $menu && $menu->isResource()) {
            return redirect($menu->getLinkIndex())->with('_after', $_after);
        }

        return back()->with('_after', $_after);
    }

    /**
     * Trap for default methods.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return app()->call([$this, "{$method}_default"]);
    }

    /**
     * @param  string  $name
     * @return Delegate
     * @throws NotFoundExplainForControllerException
     */
    public function __get(string $name)
    {
        if ($name == 'page') {
            return Page::new();
        }

        if (isset(Component::$components[$name])) {
            return new Delegate(Component::$components[$name]);
        }

        throw new NotFoundExplainForControllerException($name);
    }

    /**
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool
     */
    public function isNotRequest(string $path, mixed $need_value = true): bool
    {
        return !$this->isRequest($path, $need_value);
    }

    /**
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool
     */
    public function isRequest(string $path, mixed $need_value = true): bool
    {
        $val = $this->request($path);
        if (is_array($need_value)) {
            return in_array($val, $need_value);
        }

        return $need_value == (is_bool($need_value) ? (bool) $val : $val);
    }

    /**
     * @param  string|null  $path
     * @param  null  $default
     * @return array|mixed|null
     */
    public function request(string $path = null, $default = null): mixed
    {
        if ($path) {
            $model = $this->model();

            if ($model && $model->exists && !request()->has($path)) {
                $ddd = multi_dot_call($model, $path) ?: request($path, $default);

                return is_array($ddd) || is_object($ddd) ? $ddd : e($ddd);
            }

            return request($path, $default);
        }

        return request()->all();
    }

    /**
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool
     */
    public function isNotModelInput(string $path, mixed $need_value = true): bool
    {
        return !$this->isModelInput($path, $need_value);
    }

    /**
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool
     */
    public function isModelInput(string $path, mixed $need_value = true): bool
    {
        $val = old($path, $this->modelInput($path));
        if (is_array($need_value)) {
            return in_array($val, $need_value);
        }

        return $need_value == (is_bool($need_value) ? (bool) $val : $val);
    }

    /**
     * @param  string  $path
     * @param $default
     * @return array|Application|\Illuminate\Foundation\Application|\Illuminate\Http\Request|mixed|string|null
     */
    public function modelInput(string $path, $default = null): mixed
    {
        $model = app(Page::class)->model();

        if ($model && $model->exists && !request()->has($path)) {
            return multi_dot_call($model, $path) ?: $default;
        }

        return request($path, $default);
    }
}
