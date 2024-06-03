<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\Components\LangComponent;
use Admin\Page;
use Closure;

/**
 * Part of the kernel that is responsible for providing delegations with the necessary functionality.
 *
 * @property-read LangComponent|static $lang
 * @template DelegatedClass
 * @mixin DelegatedClass
 */
abstract class Delegator
{
    /**
     * Delegated actions for class.
     *
     * @var DelegatedClass
     */
    protected $class;

    /**
     * Condition under which the delegate will be executed.
     *
     * @var mixed|bool
     */
    protected mixed $condition = true;

    /**
     * The magic method for adding methods to the delegate.
     *
     * @param  string  $name
     * @return \Admin\Core\Delegate
     */
    public function __get(string $name)
    {
        $result = (new Delegate($this->class, $this->condition))->__call($name, []);
        $this->condition = true;

        return $result;
    }

    /**
     * The magic method for adding methods to the delegate or calling macros.
     *
     * @param $method
     * @param $parameters
     * @return \Admin\Core\Delegate|mixed
     */
    public function __call($method, $parameters)
    {
        if (static::hasMacro($method)) {
            $macro = static::$macros[$method];

            if ($macro instanceof Closure) {
                $macro = $macro->bindTo($this, static::class);
            }

            return $macro(...$parameters);
        }

        $result = (new Delegate($this->class, $this->condition))->__call($method, $parameters);
        $this->condition = true;

        return $result;
    }

    /**
     * Execute the following delegations if the resource type is now index.
     *
     * @param  mixed  $addCondition
     * @return $this
     */
    public function ifIndex(mixed $addCondition = true): static
    {
        $router = app('router');
        $this->if(
            $router->currentRouteNamed('*.index')
            && $addCondition
        );

        return $this;
    }

    /**
     * Execute the following delegations if the condition is met.
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function if(mixed $condition): static
    {
        $this->condition = is_callable($condition) ? call_user_func($condition) : $condition;

        return $this;
    }

    /**
     * Execute the following delegations if the resource type is now creation.
     *
     * @param  mixed  $addCondition
     * @return $this
     */
    public function ifCreate(mixed $addCondition = true): static
    {
        $router = app('router');
        $this->if(
            ($router->currentRouteNamed('*.create')
                || $router->currentRouteNamed('*.store'))
            && $addCondition
        );

        return $this;
    }

    /**
     * Perform the following delegations if the resource type is currently creation or editing.
     *
     * @param  mixed  $addCondition
     * @return $this
     */
    public function ifForm(mixed $addCondition = true): static
    {
        $router = app('router');
        $this->if(
            ($router->currentRouteNamed('*.edit')
                || $router->currentRouteNamed('*.update')
                || $router->currentRouteNamed('*.create')
                || $router->currentRouteNamed('*.store'))
            && $addCondition
        );

        return $this;
    }

    /**
     * Execute the following delegations if the resource type is now displaying the model.
     *
     * @param ...$delegates
     * @return $this
     */
    public function ifShow(...$delegates): static
    {
        $router = app('router');
        $this->if($router->currentRouteNamed('*.show'));

        return $this;
    }

    /**
     * Execute the following delegations if the current query matches the specified one.
     *
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool|$this
     */
    public function ifQuery(string $path, mixed $need_value = true): bool|static
    {
        $val = request($path);
        if (is_array($need_value)) {
            return in_array($val, $need_value);
        }

        $this->if($need_value == (is_bool($need_value) ? (bool) $val : $val));

        return $this;
    }

    /**
     * Execute the following delegations if the current query does not match the specified one.
     *
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool|$this
     */
    public function ifNotQuery(string $path, mixed $need_value = true): bool|static
    {
        $val = request($path);
        if (is_array($need_value)) {
            return in_array($val, $need_value);
        }

        $this->ifNot($need_value == (is_bool($need_value) ? (bool) $val : $val));

        return $this;
    }

    /**
     * Execute the following delegations if the specified conditions worked out false.
     *
     * @param  mixed  $condition
     * @return $this
     */
    public function ifNot(mixed $condition): static
    {
        $this->condition = !(is_callable($condition) ? call_user_func($condition) : $condition);

        return $this;
    }

    /**
     * If the model input is not equal to the specified value.
     *
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool
     */
    public function isNotModelInput(string $path, mixed $need_value = true): bool
    {
        return !$this->isModelInput($path, $need_value);
    }

    /**
     * If the model input is equal to the specified value.
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
     * Get the value of the share if there is no request, if there is a request with a similar name, then the request will be taken.
     *
     * @param  string  $path
     * @param $default
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Foundation\Application|\Illuminate\Http\Request|mixed|string|null
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
     * Process the following delegations if the current resource type is editing.
     *
     * @param mixed $addCondition
     * @return $this
     */
    public function ifEdit(mixed $addCondition = true): static
    {
        $router = app('router');
        $this->if(
            ($router->currentRouteNamed('*.edit')
                || $router->currentRouteNamed('*.update'))
            && $addCondition
        );

        return $this;
    }

    /**
     * Helper for adding input information about the identifier.
     *
     * @return array
     */
    public function inputInfoId(): array
    {
        return [
            $this->ifEdit()->info('id', 'admin.id')
        ];
    }

    /**
     * Helper for adding timestamp information input.
     *
     * @return array
     */
    public function inputInfoAt(): array
    {
        return [
            $this->ifEdit()->info_updated_at(),
            $this->ifEdit()->info_created_at(),
        ];
    }
}
