<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Core\PrepareExport;
use Exception;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Admin\Components\SearchFields\AmountSearchInput;
use Admin\Components\SearchFields\ChecksSearchInput;
use Admin\Components\SearchFields\ColorSearchInput;
use Admin\Components\SearchFields\DateRangeSearchInput;
use Admin\Components\SearchFields\DateSearchInput;
use Admin\Components\SearchFields\DateTimeRangeSearchField;
use Admin\Components\SearchFields\DateTimeSearchInput;
use Admin\Components\SearchFields\EmailSearchInput;
use Admin\Components\SearchFields\IconSearchInput;
use Admin\Components\SearchFields\InputSearch;
use Admin\Components\SearchFields\MultiSelectSearchInput;
use Admin\Components\SearchFields\NumberSearchField;
use Admin\Components\SearchFields\NumericSearchField;
use Admin\Components\SearchFields\RadiosSearchField;
use Admin\Components\SearchFields\SelectSearchInput;
use Admin\Components\SearchFields\SelectTagsSearchField;
use Admin\Components\SearchFields\SwitcherSearchField;
use Admin\Components\SearchFields\TimeFieldSearchField;
use Admin\Explanation;
use Admin\Traits\SearchFormConditionRulesTrait;
use Admin\Traits\SearchFormHelpersTrait;

/**
 * @methods static::$field_components (string $name, string $label, $condition = '{{ $condition || =% }}')
 * @mixin SearchFormComponentMethods
 * @mixin SearchFormComponentFields
 */
class SearchFormComponent extends Component
{
    use SearchFormConditionRulesTrait;
    use SearchFormHelpersTrait;

    /**
     * @var array
     */
    public static array $field_components = [
        'input' => InputSearch::class,
        'email' => EmailSearchInput::class,
        'number' => NumberSearchField::class,
        'numeric' => NumericSearchField::class,
        'amount' => AmountSearchInput::class,
        'switcher' => SwitcherSearchField::class,
        'date_range' => DateRangeSearchInput::class,
        'date_time_range' => DateTimeRangeSearchField::class,
        'date' => DateSearchInput::class,
        'date_time' => DateTimeSearchInput::class,
        'time' => TimeFieldSearchField::class,
        'icon' => IconSearchInput::class,
        'color' => ColorSearchInput::class,
        'select' => SelectSearchInput::class,
        'multi_select' => MultiSelectSearchInput::class,
        'select_tags' => SelectTagsSearchField::class,
        'checks' => ChecksSearchInput::class,
        'radios' => RadiosSearchField::class,
    ];

    /**
     * @var mixed
     */
    protected static $regInputs = null;

    /**
     * @var string
     */
    protected string $view = 'search-form';

    /**
     * @var array
     */
    protected array $fields = [];

    /**
     * @var string[]
     */
    protected array $conditions = [
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
    protected array $global_search_fields = [];

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
    public function fieldsCount(): int
    {
        return count($this->fields);
    }

    /**
     * @param  array  $params
     * @return $this
     */
    public function globalSearchFields(array $params): static
    {
        $this->global_search_fields = $params;

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|FormComponent|mixed|string
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (!SearchFormComponent::$regInputs) {
            $inputs = SearchFormComponent::$regInputs = implode('|',
                array_keys(SearchFormComponent::$field_components));
        } else {
            $inputs = SearchFormComponent::$regInputs;
        }

        if (
            preg_match("/^in_($inputs)_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Component::hasComponentStatic($name)
            && !isset(static::$field_components[$name])
        ) {
            $field = $matches[1];
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[2], '_'));
            $label = $arguments[0] ?? ucfirst(str_replace(['.', '_'], ' ', $name));
            $condition = $arguments[1] ?? null;

            if ($condition) {
                return $this->{$field}($name, Lang::has("admin.$label") ? __("admin.$label") : $label, $condition);
            }
            return $this->{$field}($name, Lang::has("admin.$label") ? __("admin.$label") : $label);
        } else {
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

                $this->fields[] = PrepareExport::$fields[] = [
                    'field' => $name,
                    'condition' => $condition,
                    'field_name' => $field_name,
                    'method' => $method,
                    'class' => $class,
                ];

                return $class;
            }
        }

        return parent::__call($name, $arguments);
    }

    /**
     * @param  string  $name
     * @return SearchFormComponent
     */
    public function __get(string $name)
    {
        if (!SearchFormComponent::$regInputs) {
            $inputs = SearchFormComponent::$regInputs = implode('|',
                array_keys(SearchFormComponent::$field_components));
        } else {
            $inputs = SearchFormComponent::$regInputs;
        }

        if (
            preg_match("/^in_($inputs)_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Component::hasComponentStatic($name)
            && !isset(static::$field_components[$name])
        ) {
            $field = $matches[1];
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[2], '_'));
            $label = ucfirst(str_replace(['.', '_'], ' ', $name));

            return $this->{$field}($name, Lang::has("admin.$name") ? __("admin.$name") : $label);
        }

        return parent::__get($name);
    }

    /**
     * @return \Illuminate\View\View
     */
    public function getSearchInfoComponent(): \Illuminate\View\View
    {
        return admin_view('components.search-form.info', [
            'fields' => $this->fields
        ]);
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        $action = admin_url_with_get([], ['q', 'page']);

        return [
            'chunks' => collect($this->fields)->chunk(3),
            'action' => $action,
            'group' => ButtonsComponent::create()->success(['fas fa-search', __('admin.to_find')])->setType('submit')
        ];
    }

    /**
     * @return void
     */
    protected function mount(): void
    {

    }
}
