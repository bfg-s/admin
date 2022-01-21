<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Database\Eloquent\Model;
use Lar\Developer\Core\Traits\Piplineble;
use Lar\Layout\Respond;
use Lar\LteAdmin\Components\ButtonsComponent;
use Lar\LteAdmin\Components\CardBodyComponent;
use Lar\LteAdmin\Components\Contents\CardContent;
use Lar\LteAdmin\Components\ChartJsComponent;
use Lar\LteAdmin\Components\FormComponent;
use Lar\LteAdmin\Components\GridColumnComponent;
use Lar\LteAdmin\Components\Contents\GridRowContent;
use Lar\LteAdmin\Components\ModelInfoTableComponent;
use Lar\LteAdmin\Components\ModelTableComponent;
use Lar\LteAdmin\Components\NestedComponent;
use Lar\LteAdmin\Components\SearchFormComponent;
use Lar\LteAdmin\Components\StatisticPeriodComponent;
use Lar\LteAdmin\Controllers\Traits\DefaultControllerResourceMethodsTrait;
use Lar\LteAdmin\Core\Delegate;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Exceptions\NotFoundExplainForControllerException;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Page;

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
    public static $crypt_fields = [];

    /**
     * @var string[]
     */
    protected static $explanation_list = [
        'row' => GridRowContent::class,
        'column' => GridColumnComponent::class,
        'card' => CardContent::class,
        'card_body' => CardBodyComponent::class,
        'search_form' => SearchFormComponent::class,
        'model_table' => ModelTableComponent::class,
        'nested' => NestedComponent::class,
        'ordered' => NestedComponent::class,
        'form' => FormComponent::class,
        'model_info_table' => ModelInfoTableComponent::class,
        'buttons' => ButtonsComponent::class,
        'chart_js' => ChartJsComponent::class,
        'statistic_periods' => StatisticPeriodComponent::class,
    ];

    public static function getHelpMethodList()
    {
        $result = Controller::$explanation_list;
        foreach ($result as $key => $extension) {
            $result[$key."_by_request"] = $extension;
            $result[$key."_by_default"] = $extension;
        }
        return $result;
    }

    public static function getExplanationList()
    {
        return Controller::$explanation_list;
    }

    public static function extend(string $name, string $class)
    {
        if (!static::hasExtend($name)) {
            Controller::$explanation_list[$name] = $class;
        }
    }

    public static function hasExtend(string $name)
    {
        return isset(Controller::$explanation_list[$name]);
    }

    public static function applyExtend(Page $page, string $name, array $delegates = [])
    {
        if (static::hasExtend($name)) {
            $class = Controller::$explanation_list[$name];
            if (method_exists($class, 'registrationInToContainer')) {
                $class::registrationInToContainer($page, $delegates, $name);
            } else {
                if ($page->hasClass(CardContent::class)) {
                    $page->registerClass(
                        $page->getClass(CardContent::class)->body()->{$name}($delegates)
                    );
                } else {
                    $page->registerClass(
                        $page->getContent()->{$name}($delegates)
                    );
                }
            }
        };
    }

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->makeModelEvents();
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|Respond
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function returnTo()
    {
        if (request()->ajax() && ! request()->pjax()) {
            return respond()->reload();
        }

        $_after = request()->get('_after', 'index');

        if ($_after === 'index' && $menu = gets()->lte->menu->now) {
            $last = session()->pull('temp_lte_table_data', []);

            return \redirect($menu['link'].(count($last) ? '?'.http_build_query($last) : ''))->with('_after', $_after);
        }

        return back()->with('_after', $_after);
    }

    /**
     * Trap for default methods.
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
//        if (isset(static::$explanation_list[$method])) {
//            return new Delegate(static::$explanation_list[$method]);
//        }

        return app()->call([$this, "{$method}_default"]);
    }

    /**
     * @param  string  $name
     * @return Delegate
     * @throws NotFoundExplainForControllerException
     */
    public function __get(string $name)
    {
        if ($name == 'page')
            return Page::new();

        if (isset(static::$explanation_list[$name])) {
            return new Delegate(static::$explanation_list[$name]);
        }

        throw new NotFoundExplainForControllerException($name);
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

            if ($model && $model->exists && ! request()->has($path)) {
                return e(multi_dot_call($model, $path) ?: request($path, $default));
            }

            return request($path, $default);
        }

        return request()->all();
    }

    /**
     * @param  string  $path
     * @param $need_value
     * @return bool
     */
    public function isRequest(string $path, $need_value)
    {
        $model = FormComponent::$current_model;

        $request_value = multi_dot_call($this->form(), $path);

        $value = old($path, $request_value ?: ($model ? multi_dot_call($model, $path, false) : null));

        return $value == $need_value;
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
}
