<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\LteAdmin\Core\FormSearchComponents;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Amount;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Checks;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Color;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Date;
use Lar\LteAdmin\Segments\Tagable\SearchFields\DateRange;
use Lar\LteAdmin\Segments\Tagable\SearchFields\DateTime;
use Lar\LteAdmin\Segments\Tagable\SearchFields\DateTimeRange;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Email;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Icon;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Input;
use Lar\LteAdmin\Segments\Tagable\SearchFields\MultiSelect;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Number;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Numeric;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Radios;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Select;
use Lar\LteAdmin\Segments\Tagable\SearchFields\SelectTags;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Switcher;
use Lar\LteAdmin\Segments\Tagable\SearchFields\Time;
use Lar\LteAdmin\Segments\Tagable\Traits\SearchFormConditionRulesTrait;
use Lar\LteAdmin\Segments\Tagable\Traits\SearchFormHelpersTrait;

/**
 * Class SearchForm
 * @package Lar\LteAdmin\Segments\Tagable
 * @mixin FormSearchComponents
 */
class SearchForm extends \Lar\Layout\Tags\FORM {

    use SearchFormConditionRulesTrait, SearchFormHelpersTrait, Macroable;

    /**
     * @var array
     */
    static $field_components = [
        'input' => Input::class,
        'email' => Email::class,
        'number' => Number::class,
        'numeric' => Numeric::class,
        'amount' => Amount::class,
        'switcher' => Switcher::class,
        'date_range' => DateRange::class,
        'date_time_range' => DateTimeRange::class,
        'date' => Date::class,
        'date_time' => DateTime::class,
        'time' => Time::class,
        'icon' => Icon::class,
        'color' => Color::class,
        'select' => Select::class,
        'multi_select' => MultiSelect::class,
        'select_tags' => SelectTags::class,
        'checks' => Checks::class,
        'radios' => Radios::class
    ];

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var string[]
     */
    protected $conditions = [
        '=' => 'equally',
        '!=' => 'not_equal',
        '>=' => 'more_or_equal',
        '<=' => 'less_or_equal',
        '>' => 'more',
        '<' => 'less',
        '%=' => 'like_right',
        '=%' => 'like_left',
        '%=%' => 'like_any',
        'null' => 'nullable',
        'not_null' => 'not_nullable',
        'in' => 'where_in',
        'not_in' => 'where_not_in',
        'between' => 'where_between',
        'not_between' => 'where_not_between'
    ];

    /**
     * Form constructor.
     * @param  mixed  $model
     * @param  mixed  ...$params
     */
    public function __construct(...$params)
    {
        parent::__construct();

        $this->when($params);

        $this->toExecute('buildForm');

        $this->callConstructEvents();
    }

    /**
     * Form builder
     */
    protected function buildForm()
    {
        $this->callRenderEvents();

        $this->setMethod('get');

        $action = urlWithGet([], ['q']);

        $this->setAction($action);

        $chunks = collect($this->fields)->chunk(3);

        foreach ($chunks as $chunk) {

            $this->row(function (Row $row) use ($chunk) {

                foreach ($chunk as $field) {

                    $row->col()->pl3()->pr3()->appEnd($field['class']);
                }
            });
        }

        $this->div()->textRight()->button_group(function (ButtonGroup $group) use ($action) {

            $group->success(['fas fa-search', __('lte.to_find')])->setType('submit');

        });
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|Form|\Lar\Tagable\Tag|mixed|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if (isset(static::$field_components[$name])) {

            $class = static::$field_components[$name];

            $field_name = $arguments[0] ?? null;
            $label = $arguments[1] ?? null;
            $condition = $arguments[2] ?? null;

            $class = new $class($this, "q[{$field_name}]", $label);

            if ($class instanceof FormGroup) {

                $class->vertical();

                $class->value(request("q.{$field_name}"));
            }

            if (!isset($this->conditions[$condition])) {
                if (property_exists($class, 'condition') && isset($this->conditions[$class::$condition])) {
                    $condition = $class::$condition;
                } else {
                    $condition = '=%';
                }
            }

            $this->fields[] = [
                'field' => $name,
                'condition' => $condition,
                'field_name' => $field_name,
                'method' => $this->conditions[$condition],
                'class' => $class
            ];

            return $class;
        }

        return parent::__call($name, $arguments);
    }
}