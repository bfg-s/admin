<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Components\CardComponent;
use Admin\Components\Component;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Admin\Respond;
use Admin\Controllers\Traits\DefaultControllerResourceMethodsTrait;
use Admin\Core\Delegate;
use Admin\Exceptions\NotFoundExplainForControllerException;
use Admin\Explanation;
use Admin\Page;

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
    protected static array $rules = [];

    /**
     * @var array
     */
    protected static array $imageModifiers = [];

    /**
     * @var array
     */
    protected static array $rule_messages = [];

    /**
     * @var array
     */
    protected static array $crypt_fields = [
        'password',
    ];

    /**
     * @var bool
     */
    protected static bool $started = false;

    /**
     * @var array
     */
    public array $menu = [];

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

    public static function getHelpMethodList()
    {
        $result = self::$explanation_list;
        foreach ($result as $key => $extension) {
            $result[$key.'_by_request'] = $extension;
            $result[$key.'_by_default'] = $extension;
        }

        return $result;
    }

    /**
     * @param  string  $key
     * @param  mixed  $rule
     * @return void
     */
    public static function setGlobalRule(string $key, mixed $rule): void
    {
        if (is_string($rule) && isset(static::$rules[$key])) {
            if (! in_array($rule, static::$rules[$key])) {
                static::$rules[$key][] = $rule;
            }
        } else {
            static::$rules[$key][] = $rule;
        }
    }

    /**
     * @param  string  $key
     * @param  mixed  $rule
     * @param  string|null  $message
     * @return void
     */
    public static function setGlobalRuleMessage(string $key, mixed $rule, ?string $message): void
    {
        if ($message && is_string($rule)) {

            static::$rule_messages["{$key}.{$rule}"] = $message;
        }
    }

    /**
     * @param  string  $path
     * @param  string  $name
     * @param  array  $attributes
     * @return void
     */
    public static function addImageModifier(string $path, string $name, array $attributes = []): void
    {
        static::$imageModifiers[$path][] = [$name, $attributes];
    }

    /**
     * @param  string  $fieldName
     * @return void
     */
    public static function addCryptField(string $fieldName): void
    {
        static::$crypt_fields[] = $fieldName;
    }
}
