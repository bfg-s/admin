<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Database\Eloquent\Model;
use Lar\Developer\Core\Traits\Piplineble;
use Lar\Layout\Respond;
use Lar\LteAdmin\Controllers\Traits\DefaultControllerResourceMethodsTrait;
use Lar\LteAdmin\Core\Delegate;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Segments\Tagable\ButtonGroup;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\ChartJs;
use Lar\LteAdmin\Segments\Tagable\ControllerMacroList;
use Lar\LteAdmin\Segments\Tagable\ControllerMethods;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\ModelTable;
use Lar\LteAdmin\Segments\Tagable\Nested;
use Lar\LteAdmin\Segments\Tagable\SearchForm;
use Lar\LteAdmin\Segments\Tagable\StatisticPeriods;

/**
 * Class Controller.
 *
 * @package Lar\LteAdmin\Controllers
 * @methods Lar\LteAdmin\Controllers\Controller::$explanation_list ()
 * @mixin ControllerMethods
 * @mixin ControllerMacroList
 */
class Controller extends BaseController
{
    use Piplineble, DefaultControllerResourceMethodsTrait, Macroable;

    /**
     * Permission functions for methods.
     *
     * @var array
     */
    public static $permission_functions = [];

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
    public static $explanation_list = [
        'card' => Card::class,
        'search' => SearchForm::class,
        'table' => ModelTable::class,
        'nested' => Nested::class,
        'form' => Form::class,
        'info' => ModelInfoTable::class,
        'buttons' => ButtonGroup::class,
        'chartjs' => ChartJs::class,
        'periods' => StatisticPeriods::class,
    ];

    /**
     * @var bool
     */
    protected $isDefault = false;

    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->makeModelEvents();
    }

    public function explanation(): Explanation
    {
        return $this->isDefault ? Explanation::new(
            Card::new()->defaultTools()
        )->index(
            SearchForm::new()->id(),
            SearchForm::new()->at(),
        )->index(
            ModelTable::new()->id(),
            ModelTable::new()->at(),
        )->form(
            Form::new()->info_id(),
            Form::new()->info_at(),
        )->show(
            ModelInfoTable::new()->id(),
            ModelInfoTable::new()->at(),
        ) : Explanation::new();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|Respond
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
        if (isset(static::$explanation_list[$method])) {
            return new Delegate(static::$explanation_list[$method]);
        }

        $this->isDefault = true;

        return app()->call([$this, "{$method}_default"]);
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
                return multi_dot_call($model, $path) ?: request($path, $default);
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
        $model = Form::$current_model;

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
            $model::created(function ($model) {
                lte_log_info('Created model', get_class($model), 'fas fa-plus');
            });
            $model::updated(function ($model) {
                lte_log_info('Updated model', get_class($model), 'fas fa-highlighter');
            });
            $model::deleted(function ($model) {
                lte_log_danger('Deleted model', get_class($model), 'fas fa-trash');
            });
        }
    }
}
