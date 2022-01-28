<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Lar\Developer\Core\Traits\Piplineble;
use Lar\Layout\Respond;
use Lar\LteAdmin\Components\ButtonsComponent;
use Lar\LteAdmin\Components\CardBodyComponent;
use Lar\LteAdmin\Components\CardComponent;
use Lar\LteAdmin\Components\ChartJsComponent;
use Lar\LteAdmin\Components\FieldComponent;
use Lar\LteAdmin\Components\FormComponent;
use Lar\LteAdmin\Components\GridColumnComponent;
use Lar\LteAdmin\Components\GridRowComponent;
use Lar\LteAdmin\Components\LiveComponent;
use Lar\LteAdmin\Components\ModelInfoTableComponent;
use Lar\LteAdmin\Components\ModelRelationComponent;
use Lar\LteAdmin\Components\ModelTableComponent;
use Lar\LteAdmin\Components\NestedComponent;
use Lar\LteAdmin\Components\SearchFormComponent;
use Lar\LteAdmin\Components\StatisticPeriodComponent;
use Lar\LteAdmin\Components\TimelineComponent;
use Lar\LteAdmin\Components\WatchComponent;
use Lar\LteAdmin\Controllers\Traits\DefaultControllerResourceMethodsTrait;
use Lar\LteAdmin\Core\Delegate;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Exceptions\NotFoundExplainForControllerException;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Page;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function redirect;

/**
 * @property-read Page $page
 * @methods Lar\LteAdmin\Controllers\Controller::$explanation_list (likeProperty)
 * @mixin ControllerMethods
 * @mixin ControllerMacroList
 */
class Controller extends BaseController
{
    use Piplineble, DefaultControllerResourceMethodsTrait, Macroable;

    /**
     * @var array
     */
    public static $rules = [];
    /**
     * @var array
     */
    public static $rule_messages = [];
    /**
     * @var array
     */
    public static $crypt_fields = [
        'password',
    ];
    /**
     * @var string[]
     */
    protected static $explanation_list = [
        'row' => GridRowComponent::class,
        'column' => GridColumnComponent::class,
        'card' => CardComponent::class,
        'card_body' => CardBodyComponent::class,
        'search_form' => SearchFormComponent::class,
        'model_table' => ModelTableComponent::class,
        'nested' => NestedComponent::class,
        'form' => FormComponent::class,
        'model_info_table' => ModelInfoTableComponent::class,
        'buttons' => ButtonsComponent::class,
        'chart_js' => ChartJsComponent::class,
        'timeline' => TimelineComponent::class,
        'statistic_period' => StatisticPeriodComponent::class,
        'live' => LiveComponent::class,
        'watch' => WatchComponent::class,
        'field' => FieldComponent::class,
        'model_relation' => ModelRelationComponent::class,
    ];
    /**
     * @var array
     */
    public $menu = [];

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->makeModelEvents();

        $this->menu = gets()->lte->menu->now;
    }

    private function makeModelEvents()
    {
        if (
            property_exists($this, 'model')
            && class_exists(static::$model)
        ) {
            /** @var Model $model */
            $model = static::$model;
            $model::created(static function ($model) {
                lte_log_info('Created model', get_class($model), 'fas fa-plus');
            });
            $model::updated(static function ($model) {
                lte_log_info('Updated model', get_class($model), 'fas fa-highlighter');
            });
            $model::deleted(static function ($model) {
                lte_log_danger('Deleted model', get_class($model), 'fas fa-trash');
            });
        }
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

    public static function getExplanationList()
    {
        return self::$explanation_list;
    }

    public static function extend(string $name, string $class)
    {
        if (!static::hasExtend($name)) {
            self::$explanation_list[$name] = $class;
        }
    }

    public static function hasExtend(string $name)
    {
        return isset(self::$explanation_list[$name]);
    }

    public function defaultDateRange()
    {
        return [
            now()->subYear()->toDateString(),
            now()->addDay()->toDateString(),
        ];
    }

    public function explanation(): Explanation
    {
        return Explanation::new(
            $this->card->defaultTools(
                method_exists($this, 'defaultTools') ? [$this, 'defaultTools'] : null
            )
        );
    }

    /**
     * @return Application|RedirectResponse|Redirector|Respond
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function returnTo()
    {
        if (request()->ajax() && !request()->pjax()) {
            return respond()->reload();
        }

        $_after = request()->get('_after', 'index');

        if ($_after === 'index' && $menu = $this->menu) {
            return redirect($menu['link.index']())->with('_after', $_after);
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

        if (isset(static::$explanation_list[$name])) {
            return new Delegate(static::$explanation_list[$name]);
        }

        throw new NotFoundExplainForControllerException($name);
    }

    public function isNotRequest(string $path, mixed $need_value = true)
    {
        return !$this->isRequest($path, $need_value);
    }

    public function isRequest(string $path, mixed $need_value = true)
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
    public function request(string $path = null, $default = null)
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
    public function isNotModelInput(string $path, mixed $need_value = true)
    {
        return !$this->isModelInput($path, $need_value);
    }

    /**
     * @param  string  $path
     * @param  mixed  $need_value
     * @return bool
     */
    public function isModelInput(string $path, mixed $need_value = true)
    {
        $val = old($path, $this->modelInput($path));
        if (is_array($need_value)) {
            return in_array($val, $need_value);
        }

        return $need_value == (is_bool($need_value) ? (bool) $val : $val);
    }

    public function modelInput(string $path, $default = null)
    {
        $model = app(Page::class)->model();

        if ($model && $model->exists && !request()->has($path)) {
            return multi_dot_call($model, $path) ?: $default;
        }

        return request($path, $default);
    }
}
