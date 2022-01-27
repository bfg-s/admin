<?php

namespace Lar\LteAdmin\Components;

use Exception;
use Lar\Layout\Tags\OL;
use Lar\LteAdmin\Components\SearchFields\AmountSearchField;
use Lar\LteAdmin\Components\SearchFields\ChecksSearchField;
use Lar\LteAdmin\Components\SearchFields\ColorSearchField;
use Lar\LteAdmin\Components\SearchFields\DateRangeSearchField;
use Lar\LteAdmin\Components\SearchFields\DateSearchField;
use Lar\LteAdmin\Components\SearchFields\DateTimeRangeSearchField;
use Lar\LteAdmin\Components\SearchFields\DateTimeSearchField;
use Lar\LteAdmin\Components\SearchFields\EmailSearchField;
use Lar\LteAdmin\Components\SearchFields\IconSearchField;
use Lar\LteAdmin\Components\SearchFields\InputSearchField;
use Lar\LteAdmin\Components\SearchFields\MultiSelectSearchField;
use Lar\LteAdmin\Components\SearchFields\NumberSearchField;
use Lar\LteAdmin\Components\SearchFields\NumericSearchField;
use Lar\LteAdmin\Components\SearchFields\RadiosSearchField;
use Lar\LteAdmin\Components\SearchFields\SelectSearchField;
use Lar\LteAdmin\Components\SearchFields\SelectTagsSearchField;
use Lar\LteAdmin\Components\SearchFields\SwitcherSearchField;
use Lar\LteAdmin\Components\SearchFields\TimeFieldSearchField;
use Lar\LteAdmin\Components\Traits\SearchFormConditionRulesTrait;
use Lar\LteAdmin\Components\Traits\SearchFormHelpersTrait;
use Lar\LteAdmin\Explanation;
use Lar\Tagable\Tag;

/**
 * @methods static::$field_components (string $name, string $label, $condition = '{{ $condition || =% }}')
 * @mixin SearchFormComponentMethods
 */
class SearchFormComponent extends Component
{
    use SearchFormConditionRulesTrait,
        SearchFormHelpersTrait;

    /**
     * @var array
     */
    public static $field_components = [
        'input' => InputSearchField::class,
        'email' => EmailSearchField::class,
        'number' => NumberSearchField::class,
        'numeric' => NumericSearchField::class,
        'amount' => AmountSearchField::class,
        'switcher' => SwitcherSearchField::class,
        'date_range' => DateRangeSearchField::class,
        'date_time_range' => DateTimeRangeSearchField::class,
        'date' => DateSearchField::class,
        'date_time' => DateTimeSearchField::class,
        'time' => TimeFieldSearchField::class,
        'icon' => IconSearchField::class,
        'color' => ColorSearchField::class,
        'select' => SelectSearchField::class,
        'multi_select' => MultiSelectSearchField::class,
        'select_tags' => SelectTagsSearchField::class,
        'checks' => ChecksSearchField::class,
        'radios' => RadiosSearchField::class,
    ];

    protected $element = 'form';

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
        'not_between' => 'where_not_between',
    ];

    /**
     * @var array
     */
    protected $global_search_fields;

    /**
     * Form constructor.
     * @param  array  $delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->explainForce(Explanation::new($delegates));
    }

    /**
     * @return int
     */
    public function fieldsCount()
    {
        return count($this->fields);
    }

    /**
     * @param  array  $params
     * @return $this
     */
    public function globalSearchFields(array $params)
    {
        $this->global_search_fields = $params;

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|FormComponent|Tag|mixed|string
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (isset(static::$field_components[$name])) {
            $class = static::$field_components[$name];

            $field_name = $arguments[0] ?? null;
            $label = $arguments[1] ?? null;
            $condition = $arguments[2] ?? null;

            $class = new $class("q[{$field_name}]", $label);

            if ($class instanceof FormGroupComponent) {
                $class->set_parent($this);

                $class->vertical();

                $class->value(request("q.{$field_name}"));
            }

            $method = null;

            if (is_embedded_call($condition)) {
                $method = $condition;
            } elseif (is_string($condition) && isset($this->conditions[$condition])) {
                $method = $this->conditions[$condition];
            } else {
                if (property_exists($class, 'condition') && isset($this->conditions[$class::$condition])) {
                    $condition = $class::$condition;
                } else {
                    $condition = '%=%';
                }

                if (is_string($condition) && isset($this->conditions[$condition])) {
                    $method = $this->conditions[$condition];
                }
            }

            $this->fields[] = [
                'field' => $name,
                'condition' => $condition,
                'field_name' => $field_name,
                'method' => $method,
                'class' => $class,
            ];

            return $class;
        }

        return parent::__call($name, $arguments);
    }

    public function getSearchInfoComponent()
    {
        $div = null;

        foreach ($this->fields as $field) {
            /** @var FormGroupComponent $formGroup */
            $formGroup = $field['class'];
            $val = request($formGroup->get_path());
            $val = is_array($val) ? implode(' - ', $val) : $val;
            if ($val) {
                if (!$div) {
                    $div = OL::create(['breadcrumb p-1 pl-2 ml-1 m-0 bg-white']);
                    $div->li(['breadcrumb-item active'])
                        ->i(['fas fa-search'])
                        ->_text(' '.__('lte.sort_result_report'));
                }

                $div->li(['breadcrumb-item'])
                    ->a([
                        'href' => urlWithGet([], [$formGroup->get_path()])
                    ])->setTitle(__('lte.cancel').': '.$formGroup->get_title())
                    ->i(['fas fa-window-close text-danger'])
                    ->__text(' '.$formGroup->get_title().': '.$val);
            }
        }

        return $div;
    }

    protected function mount()
    {
        $this->setMethod('get');

        $action = urlWithGet([], ['q', 'page']);

        $this->setAction($action);

        $chunks = collect($this->fields)->chunk(3);

        foreach ($chunks as $chunk) {
            $this->row()->when(function (GridRowComponent $row) use ($chunk) {
                foreach ($chunk as $field) {
                    $row->column()->pl3()->pr3()->appEnd($field['class']);
                }
            });
        }

        $this->div()->textRight()->buttons()->when(static function (ButtonsComponent $group) use ($action) {
            $group->success(['fas fa-search', __('lte.to_find')])->setType('submit');
        });
    }
}
