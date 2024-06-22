<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Components\CardComponent;
use Admin\Components\Component;
use Admin\Core\Delegate;
use Admin\Exceptions\NotFoundComponentForControllerException;
use Admin\Explanation;
use Admin\Page;
use Admin\Respond;
use Admin\Traits\DefaultControllerResourceMethodsTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

use function redirect;

/**
 * Parent controller for all admin panel controllers.
 *
 * @property-read Page $page
 */
class Controller extends BaseController
{
    use DefaultControllerResourceMethodsTrait;

    /**
     * Current rules for validating incoming data.
     *
     * @var array
     */
    protected static array $rules = [];

    /**
     * Current messages of incoming data validation rules.
     *
     * @var array
     */
    protected static array $ruleMessages = [];

    /**
     * Image modifiers for incoming data.
     *
     * @var array
     */
    protected static array $imageModifiers = [];

    /**
     * Fields for encryption for incoming data.
     *
     * @var array
     */
    protected static array $cryptFields = [
        'password',
    ];

    /**
     * Reset all static fields.
     *
     * @return void
     */
    public static function reset(): void
    {
        static::$rules = [];
        static::$ruleMessages = [];
        static::$imageModifiers = [];
        static::$cryptFields = [
            'password',
        ];
    }

    /**
     * Add a global rule for validating incoming data.
     *
     * @param  string  $key
     * @param  mixed  $rule
     * @return void
     */
    public static function addGlobalRule(string $key, mixed $rule): void
    {
        if (is_string($rule) && isset(static::$rules[$key])) {
            if (!in_array($rule, static::$rules[$key])) {
                static::$rules[$key][] = $rule;
            }
        } else {
            static::$rules[$key][] = $rule;
        }
    }

    /**
     * Add a global message for the incoming data validation rule.
     *
     * @param  string  $key
     * @param  mixed  $rule
     * @param  string|null  $message
     * @return void
     */
    public static function addGlobalRuleMessage(string $key, mixed $rule, ?string $message): void
    {
        if ($message && is_string($rule)) {
            static::$ruleMessages["{$key}.{$rule}"] = $message;
        }
    }

    /**
     * Add incoming data image modifier.
     *
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
     * Add a field for encrypting incoming data.
     *
     * @param  string  $fieldName
     * @return void
     */
    public static function addCryptField(string $fieldName): void
    {
        static::$cryptFields[] = $fieldName;
    }

    /**
     * Default date interval.
     *
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
     * Complete the explanation for the first card on the page.
     *
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
     * Function for redirection after operation.
     *
     * @return Application|RedirectResponse|Redirector|Respond
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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
     * Magic method to trap standard resource controller methods.
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
     * A magical method for generating a page or delegation component.
     *
     * @param  string  $name
     * @return Delegate
     * @throws NotFoundComponentForControllerException
     */
    public function __get(string $name)
    {
        if ($name == 'page') {
            return Page::new();
        }

        if (isset(Component::$components[$name])) {
            return new Delegate(Component::$components[$name]);
        }

        throw new NotFoundComponentForControllerException($name);
    }

    /**
     * Receive a correct model or request. If there is a request by name, it will give it, if not, it will give the value to the model.
     *
     * @param  string|null  $path
     * @param  null  $default
     * @return array|mixed|null
     */
    public function request(string $path = null, $default = null): mixed
    {
        if ($path) {

            $model = $this->model();

            if ($model && $model->exists && !request()->has($path)) {

                $value = multi_dot_call($model, $path) ?: request($path, $default);

                return is_array($value) || is_object($value) ? $value : e($value);
            }

            return request($path, $default);
        }

        return request()->all();
    }

    /**
     * Check if the request is the specified value.
     *
     * @param  string  $path
     * @param  mixed  $specified_value
     * @return bool
     */
    public function isRequest(string $path, mixed $specified_value = true): bool
    {
        $val = $this->request($path);

        if (is_array($specified_value)) {

            return in_array($val, $specified_value);
        }

        return $specified_value == (is_bool($specified_value) ? (bool) $val : $val);
    }

    /**
     * Check if the request is not the specified value.
     *
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool
     */
    public function isNotRequest(string $path, mixed $need_value = true): bool
    {
        return !$this->isRequest($path, $need_value);
    }

    /**
     * Get the value of a model property if there is no request with the same name.
     *
     * @param  string  $path
     * @param $default
     * @return array|Application|\Illuminate\Foundation\Application|Request|mixed|string|null
     */
    public function modelInput(string $path, $default = null): mixed
    {
        $model = app(Page::class)->model();

        if ($model && $model->exists && !request()->has($path)) {
            return multi_dot_call($model, $path) ?: $default;
        }

        return request($path, $default);
    }

    /**
     * Find out whether the value of a model property is equal to the specified value.
     *
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
     * Find out the value of a model property is not equal to the specified value.
     *
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool
     */
    public function isNotModelInput(string $path, mixed $need_value = true): bool
    {
        return !$this->isModelInput($path, $need_value);
    }
}
