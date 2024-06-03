<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Components\Inputs\Parts\InputFormGroupComponent;
use Admin\Page;
use Admin\Traits\FontAwesomeTrait;
use Admin\Traits\RulesBackTrait;
use Admin\Traits\RulesFrontTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\ViewErrorBag;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * The main abstract component of the input. All inputs are inherited from this component.
 */
abstract class InputGroupComponent extends Component
{
    use RulesFrontTrait;
    use RulesBackTrait;
    use FontAwesomeTrait;

    /**
     * Component count for unique identifier.
     *
     * @var int
     */
    protected static int $counter = 0;

    /**
     * Input label text.
     *
     * @var string|null
     */
    protected string|null $title = null;

    /**
     * Input name.
     *
     * @var string|null
     */
    protected string|null $name = null;

    /**
     * Input icon.
     *
     * @var string|null
     */
    protected string|null $icon = 'fas fa-pencil-alt';

    /**
     * Additional input information is located below.
     *
     * @var string|null
     */
    protected string|null $info = null;

    /**
     * Number of input label columns.
     *
     * @var int|null
     */
    protected int|null $label_width = 2;

    /**
     * Vertical display of input and label.
     *
     * @var bool
     */
    protected bool $vertical = false;

    /**
     * Reverse display of input and label.
     *
     * @var bool
     */
    protected bool $reversed = false;

    /**
     * The parent component of the input.
     *
     * @var Component|FormComponent|null
     */
    protected FormComponent|Component|null $parent_component = null;

    /**
     * Input value.
     *
     * @var mixed
     */
    protected mixed $value = null;

    /**
     * The value of the input ID attribute.
     *
     * @var string|null
     */
    protected string|null $field_id = null;

    /**
     * Dot path to the input value.
     *
     * @var string|null
     */
    protected string|null $path = null;

    /**
     * Whether the input has validation errors.
     *
     * @var bool
     */
    protected bool $has_bug = false;

    /**
     * List of validation errors.
     *
     * @var ViewErrorBag
     */
    protected mixed $errors = null;

    /**
     * Whether the current controller is an admin controller.
     *
     * @var bool
     */
    protected bool $admin_controller = false;

    /**
     * The class of the current controller.
     *
     * @var string|null
     */
    protected string|null $controller;

    /**
     * The current controller method.
     *
     * @var string|null
     */
    protected string|null $method = null;

    /**
     * Display only the input without a label.
     *
     * @var bool
     */
    protected bool $only_input = false;

    /**
     * Callback for determining the input value.
     *
     * @var callable
     */
    protected mixed $value_to = null;

    /**
     * Current page of the admin panel.
     *
     * @var Page
     */
    protected Page $page;

    /**
     * Default input value if no value is set.
     *
     * @var mixed|null
     */
    protected mixed $default = null;

    /**
     * Callback for the group form component when it is created.
     *
     * @var array
     */
    protected array $formGroupCallbacks = [];

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'content-only';

    /**
     * InputGroupComponent constructor.
     *
     * @param  string  $name
     * @param  string|null  $title
     * @param  mixed  ...$params
     */
    public function __construct(string $name, string $title = null, ...$params)
    {
        $this->page = app(Page::class);

        parent::__construct();

        $this->title = $title ? __($title) : $title;
        $this->name = $name;

        $this->field_id = $this->currentCount . '_input_'.$this->convertBracketsToUnderscore($name);
        $this->path = $this->convertBracketsToUnderscore($name, '.');
        $this->errors = request()->session()->get('errors') ?: new ViewErrorBag();
        $this->has_bug = $this->errors->getBag('default')->has($name);
        if (Route::current()) {
            list($this->controller, $this->method) = Str::parseCallback(Route::currentRouteAction());
            $this->admin_controller = property_exists($this->controller, 'rules');
        }
        if (!$title) {
            $this->vertical();
        }
        $this->model = $this->page->model();
        $this->after_construct();
    }

    /**
     * Convert a name string to a path or identifier string.
     *
     * @param $str
     * @param  string  $delimiter
     * @return string
     */
    protected function convertBracketsToUnderscore($str, string $delimiter = "_"): string
    {
        return str_replace(
            ["{$delimiter}{$delimiter}", '{__id__}', '{_id_}'],
            [$delimiter, '', ''],
            trim(str_replace(['[', ']'], $delimiter, str_replace('[]', '', $str)), $delimiter)
        );
    }

    /**
     * Enable vertical input mode.
     *
     * @return $this
     */
    public function vertical(): static
    {
        $this->vertical = true;

        return $this;
    }

    /**
     * After construct event.
     *
     * @return void
     */
    protected function after_construct(): void
    {
    }

    /**
     * Set the input ID.
     *
     * @param  string  $id
     * @return $this
     */
    public function setId(string $id): static
    {
        $this->field_id = $id;

        return $this;
    }

    /**
     * Make wrapper for input.
     *
     * @return void
     */
    protected function makeWrapper(): void
    {
        $this->on_build();

        if ($this->only_input) {
            $this->value = $this->create_value();
            if ($this->value_to) {
                $this->value = call_user_func($this->value_to, $this->value, $this->model);
            }
            $this->appEnd(
                $this->field()
            )->appEnd(
                $this->app_end_field()
            );

            return;
        }

        $formGroup = $this->createComponent(InputFormGroupComponent::class);

        $group_width = 12 - $this->label_width;

        $formGroup->setViewData([
            'icon' => $this->icon,
            'vertical' => $this->vertical,
            'name' => $this->name,
            'title' => $this->title,
            'group_width' => $group_width,
            'label_width' => $this->label_width,
            'reversed' => $this->reversed,
            'id' => $this->field_id,
            'info' => $this->info,
            'errors' => $this->errors,
            'messages' => $this->errors->get($this->name),
            'hasError' => $this->errors->has($this->name),
        ]);

        foreach ($this->formGroupCallbacks as $formGroupCallback) {

            call_user_func($formGroupCallback, $formGroup);
        }

        $this->value = $this->create_value();

        if ($this->value_to) {

            $this->value = call_user_func($this->value_to, $this->value, $this->model);
        }

        $formGroup->appEnd(
            $this->field()
        )->appEnd(
            $this->app_end_field()
        );

        $this->appEnd($formGroup);
    }

    /**
     * Method to override, event before creating the input wrapper.
     *
     * @return void
     */
    protected function on_build(): void
    {
    }

    /**
     * Get the current value of the input.
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * Get the default input value.
     *
     * @return mixed
     */
    public function getDefault(): mixed
    {
        return $this->default;
    }

    /**
     * Create a value for the input.
     *
     * @return mixed
     */
    protected function create_value(): mixed
    {
        if ($this->value !== null) {
            return $this->value;
        }

        $val = old($this->path);
        if (!$val) {
            $val = request($this->path) ?: null;
        }
        if (!$val && $this->model) {
            $val = multi_dot_call($this->model, $this->path, false);
        }

        return $val !== null && $val !== '' ? $val : $this->default;
    }

    /**
     * Method for creating an input field.
     *
     * @return mixed
     */
    abstract public function field(): mixed;

    /**
     * Data that needs to be placed after the input.
     *
     * @return string
     */
    protected function app_end_field(): string
    {
        return '';
    }

    /**
     * Allows you to insert a value for an input from a request.
     *
     * @return $this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function queryable(): static
    {
        $request = request();
        if ($request->has($this->path)) {
            $this->value($request->get($this->path));
        }

        return $this;
    }

    /**
     * Set the raw value of the input.
     *
     * @param  mixed  $value
     * @return $this
     */
    public function value(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the deep name of the input.
     *
     * @param  array  $names
     * @return string|null
     */
    public function deepName(array $names): string|null
    {
        return $this->name;
    }

    /**
     * Get the deep input path.
     *
     * @param  array  $paths
     * @return string|null
     */
    public function deepPath(array $paths): string|null
    {
        return $this->path;
    }

    /**
     * @param  Component  $parent
     * @return $this
     */
    public function set_parent(Component $parent): static
    {
        $this->parent_component = $parent;

        $deepNames = $this->deepNames();
        $name = $this->namesToString($deepNames);

        $isArrayable = str_ends_with($this->get_name(), '[]');

        $this->set_name($name.($isArrayable ? '[]' : ''));
        $this->set_id('input_'.$this->convertBracketsToUnderscore($name).($isArrayable ? '_'.static::$counter++ : ''));

        return $this;
    }

    /**
     * Convert the input name that was generated by the deep method.
     *
     * @param $array
     * @return string
     */
    protected function namesToString($array): string
    {
        if (empty($array)) {
            return '';
        }

        $firstElement = array_shift($array);
        $formattedElements = array_map(function ($item) {
            return sprintf('[%s]', $item);
        }, $array);

        return $firstElement.implode('', $formattedElements);
    }

    /**
     * Get the input name.
     *
     * @return string|null
     */
    public function get_name(): string|null
    {
        return $this->name;
    }

    /**
     * Set the input name.
     *
     * @param  string  $name
     * @return $this
     */
    public function set_name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the input ID.
     *
     * @param $id
     * @return $this
     */
    public function set_id($id): static
    {
        $this->field_id = Str::slug($id, '_');

        return $this;
    }

    /**
     * Get the parent of the input.
     *
     * @return Component|null
     */
    public function getParent(): ?Component
    {
        return $this->parent_component;
    }

    /**
     * Set the input to horizontal mode.
     *
     * @return $this
     */
    public function horizontal(): static
    {
        $this->vertical = false;

        return $this;
    }

    /**
     * Switch the input to reverse mode.
     *
     * @return $this
     */
    public function reversed(): static
    {
        $this->reversed = true;

        return $this;
    }

    /**
     * Display only the input, without the label.
     *
     * @return $this
     */
    public function only_input(): static
    {
        $this->only_input = true;

        return $this;
    }

    /**
     * Set the input icon.
     *
     * @param  string|null  $name
     * @return $this
     */
    public function icon(string $name = null): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * Add input to the list of crypt fields when saving.
     *
     * @return $this
     */
    public function crypt(): static
    {
        if ($this->admin_controller) {
            $this->controller::addCryptField($this->name);
        }

        return $this;
    }

    /**
     * Set the width of the label in the number of columns.
     *
     * @param  int  $width
     * @return $this
     */
    public function label_width(int $width): static
    {
        $this->label_width = $width;

        return $this;
    }

    /**
     * Set default value for input.
     *
     * @param $value
     * @return $this
     */
    public function default($value): static
    {
        $this->default = $value;

        return $this;
    }

    /**
     * Set the default value for input from the model along the path.
     *
     * @param  string  $path
     * @return $this
     */
    public function defaultFromModel(string $path): static
    {
        if ($this->model) {
            $this->value_to = function () use ($path) {
                $ddd = multi_dot_call($this->model, $path);

                return is_array($ddd) || is_object($ddd) || is_null($ddd) || is_bool($ddd) ? $ddd : e($ddd);
            };
        }

        return $this;
    }

    /**
     * Set the current input model.
     *
     * @param  Model|null  $model
     * @return $this
     */
    public function setModel(Model $model = null): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Set input info text.
     *
     * @param  string  $name
     * @return $this
     */
    public function info(string $name): static
    {
        $this->info = $name;

        return $this;
    }

    /**
     * Add a callback to the event of creating a new group form.
     *
     * @param  callable  $call
     * @return $this
     */
    public function onFormGroupCreated(callable $call): static
    {
        $this->formGroupCallbacks[] = $call;

        return $this;
    }

    /**
     * Set a callback to execute receiving the input value.
     *
     * @param  callable  $call
     * @return $this
     */
    public function value_to(callable $call): static
    {
        $this->value_to = $call;

        return $this;
    }

    /**
     * Get the current input ID.
     *
     * @return string|null
     */
    public function get_id(): ?string
    {
        return $this->field_id;
    }

    /**
     * Get the current vertical input mode.
     *
     * @return bool|null
     */
    public function get_vertical(): ?bool
    {
        return $this->vertical;
    }

    /**
     * Get the width of the input label in columns.
     *
     * @return int|null
     */
    public function get_label_width(): ?int
    {
        return $this->label_width;
    }

    /**
     * Get input reversed mode.
     *
     * @return bool|null
     */
    public function get_reversed(): ?bool
    {
        return $this->reversed;
    }

    /**
     * Get the current input path.
     *
     * @return string|null
     */
    public function get_path(): ?string
    {
        return $this->path;
    }

    /**
     * Set the current input path.
     *
     * @param  string  $path
     * @return $this
     */
    public function set_path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get input title.
     *
     * @return string|null
     */
    public function get_title(): ?string
    {
        return $this->title;
    }

    /**
     * Set the input title.
     *
     * @param  string  $title
     * @return $this
     */
    public function set_title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    public function mount(): void
    {
        $this->makeWrapper();
    }
}
