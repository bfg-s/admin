<?php

namespace Admin\Components;

use Admin\Components\Inputs\Parts\InputFormGroupComponent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ViewErrorBag;
use Admin\Page;
use Admin\Traits\FontAwesome;
use Admin\Traits\RulesBackTrait;
use Admin\Traits\RulesFrontTrait;
use Illuminate\Support\Str;

abstract class FormGroupComponent extends Component
{
    use RulesFrontTrait;
    use RulesBackTrait;
    use FontAwesome;

    /**
     * @var array
     */
    public static array $construct_modify = [];

    /**
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-pencil-alt';

    /**
     * @var string|null
     */
    protected ?string $info = null;

    /**
     * @var int|null
     */
    protected ?int $label_width = 2;

    /**
     * @var bool
     */
    protected bool $vertical = false;

    /**
     * @var bool
     */
    protected bool $reversed = false;

    /**
     * @var Component|FormComponent|null
     */
    protected FormComponent|Component|null $parent_field = null;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string|null
     */
    protected ?string $field_id = null;
    /**
     * @var string|null
     */
    protected ?string $path = null;

    /**
     * @var bool
     */
    protected bool $has_bug = false;

    /**
     * @var ViewErrorBag
     */
    protected mixed $errors = null;

    /**
     * @var bool
     */
    protected bool $admin_controller = false;

    /**
     * @var string|null
     */
    protected ?string $controller;

    /**
     * @var string|null
     */
    protected ?string $method = null;

    /**
     * @var bool
     */
    protected $only_input = false;

    /**
     * @var callable
     */
    protected mixed $value_to = null;

    /**
     * @var Page
     */
    protected Page $page;

    /**
     * @var mixed|null
     */
    protected mixed $default = null;

    /**
     * @var array
     */
    protected array $fgs = [];

    /**
     * @var string
     */
    protected string $view = 'content-only';

    /**
     * FormGroup constructor.
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
        $this->field_id = 'input_'.Str::slug($this->name, '_');
        $this->path = trim(str_replace(['[', ']'], '.', str_replace('[]', '', $name)), '.');
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

        foreach (self::$construct_modify as $item) {
            if (is_callable($item)) {
                call_user_func($item, $this, $this->model);
            }
        }
    }

    /**
     * @param  string  $id
     * @return $this
     */
    public function setId(string $id): static
    {
        $this->field_id = $id;

        return $this;
    }

    /**
     * @return void
     */
    protected function on_build(): void
    {

    }

    /**
     * @return void
     */
    public function mount(): void
    {
        $this->makeWrapper();
    }

    /**
     * @return $this
     */
    public function vertical(): static
    {
        $this->vertical = true;

        return $this;
    }

    /**
     * After construct event.
     * @return void
     */
    protected function after_construct(): void
    {
    }

    /**
     * @return $this
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
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
     * @param  mixed  $value
     * @return $this
     */
    public function value(mixed $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param  Component  $parent
     * @return $this
     */
    public function set_parent(Component $parent): static
    {
        $this->parent_field = $parent;

        return $this;
    }

    /**
     * @return $this
     */
    public function horizontal(): static
    {
        $this->vertical = false;

        return $this;
    }

    /**
     * @return $this
     */
    public function reversed(): static
    {
        $this->reversed = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function only_input(): static
    {
        $this->only_input = true;

        return $this;
    }

    /**
     * @param  string|null  $name
     * @return $this
     */
    public function icon(string $name = null): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * @return $this
     */
    public function crypt(): static
    {
        if ($this->admin_controller) {
            $this->controller::$crypt_fields[] = $this->name;
        }

        return $this;
    }

    /**
     * @param  int  $width
     * @return $this
     */
    public function label_width(int $width): static
    {
        $this->label_width = $width;

        return $this;
    }

    /**
     * @param  array  $datas
     * @return $this
     */
    public function mergeDataList(array $datas): static
    {
        $this->data = array_merge($this->data, $datas);

        return $this;
    }

    /**
     * @param  array  $rules
     * @return $this
     */
    public function mergeRuleList(array $rules): static
    {
        $this->rules = array_merge($this->rules, $rules);

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function default($value): static
    {
        $this->default = $value;

        return $this;
    }

    /**
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
     * @param  Model|null  $model
     * @return $this
     */
    public function setModel(Model $model = null): static
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function info(string $name): static
    {
        $this->info = $name;

        return $this;
    }

    /**
     * @param  callable  $call
     * @return $this
     */
    public function fg(callable $call): static
    {
        $this->fgs[] = $call;

        return $this;
    }

    /**
     * @param  callable  $call
     * @return $this
     */
    public function value_to(callable $call): static
    {
        $this->value_to = $call;

        return $this;
    }

    /**
     * @param  string|null  $var
     * @return $this
     */
    public function stated(string $var = null): static
    {
        $this->data['stated'] = $var ? $var : '';
        $this->data['state'] = $var ? $var : '';

        return $this;
    }

    /**
     * @param  string|null  $var
     * @return $this
     */
    public function state(string $var = null): static
    {
        $this->data['state'] = $var ? $var : '';

        return $this;
    }

    /**
     * @return string|null
     */
    public function get_id(): ?string
    {
        return $this->field_id;
    }

    /**
     * @return bool|null
     */
    public function get_vertical(): ?bool
    {
        return $this->vertical;
    }

    /**
     * @return int|null
     */
    public function get_label_width(): ?int
    {
        return $this->label_width;
    }

    /**
     * @return bool|null
     */
    public function get_reversed(): ?bool
    {
        return $this->reversed;
    }

    /**
     * @param $id
     * @return $this
     */
    public function set_id($id): static
    {
        $this->field_id = Str::slug($id, '_');

        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function force_set_id($id): static
    {
        $this->field_id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function get_name(): ?string
    {
        return $this->name;
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function set_name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function get_path(): ?string
    {
        return $this->path;
    }

    /**
     * @param  string  $path
     * @return $this
     */
    public function set_path(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string|null
     */
    public function get_title(): ?string
    {
        return $this->title;
    }

    /**
     * @param  string  $title
     * @return $this
     */
    public function set_title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Make wrapper for input.
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

        $fg = $this->createComponent(InputFormGroupComponent::class);

        $group_width = 12 - $this->label_width;

        $fg->setViewData([
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

        foreach ($this->fgs as $fgs) {
            call_user_func($fgs, $fg);
        }

        $this->value = $this->create_value();

        if ($this->value_to) {
            $this->value = call_user_func($this->value_to, $this->value, $this->model);
        }

        $fg->appEnd(
            $this->field()
        )->appEnd(
            $this->app_end_field()
        );

        $this->appEnd($fg);
    }

    /**
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
     * @return mixed
     */
    abstract public function field(): mixed;

    /**
     * @return string
     */
    protected function app_end_field(): string
    {
        return '';
    }
}
