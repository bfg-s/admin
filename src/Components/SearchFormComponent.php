<?php

declare(strict_types=1);

namespace Admin\Components;

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
use Admin\Components\SearchFields\SliderSearchInput;
use Admin\Components\SearchFields\SwitcherSearchField;
use Admin\Components\SearchFields\TimeFieldSearchField;
use Admin\Core\PrepareExport;
use Admin\Explanation;
use Admin\Facades\Admin;
use Admin\Traits\SearchFormConditionRulesTrait;
use Exception;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Search form component in the admin panel.
 *
 * @methods static::$field_components (string $name, string $label, $condition = '{{ $condition || =% }}')
 * @mixin SearchFormComponentMethods
 * @mixin SearchFormComponentFields
 */
class SearchFormComponent extends Component
{
    use SearchFormConditionRulesTrait;

    /**
     * List of component inputs that can be used in search forms.
     *
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
        'slider' => SliderSearchInput::class,
    ];

    /**
     * List of input names in a string separated by the "|" symbol.
     *
     * @var mixed
     */
    protected static $regInputs = null;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'search-form';

    /**
     * Ready-made default search conditions.
     *
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
     * Model fields for which global search occurs.
     *
     * @var array
     */
    protected array $global_search_fields = [];

    /**
     * SearchFormComponent constructor.
     *
     * @param  array  $delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->explainForce(Explanation::new($delegates));
    }

    /**
     * Get the number of search form fields.
     *
     * @return int
     */
    public function fieldsCount(): int
    {
        return count($this->contents);
    }

    /**
     * Get all search form fields.
     *
     * @return array
     */
    public function getFields(): array
    {
        return $this->contents;
    }

    /**
     * Set fields for global search by model.
     *
     * @param  array  $params
     * @return $this
     */
    public function globalSearchFields(array $params): static
    {
        $this->global_search_fields = $params;

        return $this;
    }

    /**
     * A magical method that generates inputs to fields and corresponding conditions.
     *
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

                if ($field_name && str_ends_with($field_name, '[]')) {
                    $name = "q[" . trim($field_name, '[]') . "][]";
                } else if ($field_name) {
                    $name = "q[{$field_name}]";
                } else {
                    $name = "q[]";
                }

                /** @var InputGroupComponent $class */
                $class = new $class($name, $label);

                if ($class instanceof InputGroupComponent) {
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
                    if (property_exists($class, 'condition')) {
                        /** @var string $conditionToSelect */
                        $conditionToSelect = $class::$condition;
                        if (isset($this->conditions[$conditionToSelect])) {
                            $condition = $conditionToSelect;
                        } else {
                            $condition = '%=%';
                        }
                    } else {
                        $condition = '%=%';
                    }

                    if (isset($this->conditions[$condition])) {
                        $method = $this->conditions[$condition];
                    }
                }

                $this->contents[] = PrepareExport::$fields[] = [
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
     * A magic method that generates inputs to fields based on property names.
     *
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
     * Assistant for adding a search field by ID.
     *
     * @return $this
     */
    public function id(): static
    {
        $this->numeric('id', 'admin.id', '=');

        return $this;
    }

    /**
     * Helper for adding a search field using the "created at" and "updated at" fields.
     *
     * @return $this
     */
    public function at(): static
    {
        return $this->updated_at()->created_at();
    }

    /**
     * Helper for adding a search field based on the "created at" field.
     *
     * @return $this
     */
    public function created_at(): static
    {
        $this->date_time_range('created_at', 'admin.created_at');

        return $this;
    }

    /**
     * Helper for adding a search field based on the "updated at" field.
     *
     * @return $this
     */
    public function updated_at(): static
    {
        $this->date_time_range('updated_at', 'admin.updated_at');

        return $this;
    }

    /**
     * The search informant template is automatically pulled up by the card.
     *
     * @return View
     */
    public function getSearchInfoComponent(): View
    {
        return admin_view('components.search-form.info', [
            'fields' => $this->contents
        ]);
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        $action = admin_url_with_get([], ['q', 'page']);

        return [
            'chunks' => collect($this->contents)->chunk(3),
            'action' => $action,
            'group' => ButtonsComponent::create()->success(['fas fa-search', __('admin.to_find')])->setType('submit')
        ];
    }

    /**
     * Data for the API.
     *
     * @return array
     */
    protected function apiData(): array
    {
        $action = admin_url_with_get([], ['q', 'page']);

        foreach ($this->contents as $key => $content) {

            if ($content['class'] ?? null && $content['class'] instanceof Component) {

                $this->contents[$key]['class'] = collect($content['class'])
                    ->map(fn ($q) => $q instanceof Component ? $q->exportToApi() : [$q])
                    ->collapse();
            }
        }

        return [
            'action' => $action,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
    }
}
