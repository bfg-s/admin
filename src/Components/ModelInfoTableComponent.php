<?php

namespace Admin\Components;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Admin\Controllers\Controller;
use Throwable;

/**
 * @mixin ModelInfoTableComponentFields
 * @mixin ModelInfoTableComponentMethods
 */
class ModelInfoTableComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'content-only';

    /**
     * @var array|Model
     */
    protected $model;

    /**
     * @var array
     */
    protected array $rows = [];

    /**
     * @var string|null
     */
    protected ?string $last = null;

    /**
     * @param  string  $name
     * @return $this
     */
    public function info(string $name): static
    {
        if ($this->last && isset($this->rows[$this->last])) {
            $this->rows[$this->last]['info'] = $name;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function id(): static
    {
        $this->row('admin.id', 'id');

        return $this;
    }

    /**
     * @param  string|Closure|array  $field
     * @param  string  $label
     * @return $this
     */
    public function row(string $label, string|Closure|array $field): static
    {
        $this->last = uniqid('row');

        $this->rows[$this->last] = ['field' => $field, 'label' => $label, 'info' => false, 'macros' => []];

        return $this;
    }

    /**
     * @return $this
     */
    public function deleted_at(): static
    {
        $this->row('admin.deleted_at', 'deleted_at')->butty_date_time()->true_data();

        return $this;
    }

    /**
     * @return $this
     */
    public function at(): static
    {
        $this->updated_at()->created_at();

        return $this;
    }

    /**
     * @return $this
     */
    public function created_at(): static
    {
        $this->row('admin.created_at', 'created_at')->butty_date_time()->true_data();

        return $this;
    }

    /**
     * @return $this
     */
    public function updated_at(): static
    {
        $this->row('admin.updated_at', 'updated_at')->butty_date_time()->true_data();

        return $this;
    }

    /**
     * @return $this
     */
    public function active_switcher(): static
    {
        $this->row('admin.active', 'active')->yes_no();

        return $this;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this|bool|ModelInfoTableComponent|string
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (
            preg_match("/^row_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Component::hasComponentStatic($name)
        ) {
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[1], '_'));
            $label = $arguments[0] ?? ucfirst(str_replace(['.', '_'], ' ', $name));

            return $this->row(Lang::has("admin.$label") ? __("admin.$label") : $label, $name);
        } else {
            if (ModelTableComponent::hasExtension($name) && $this->last) {
                $this->rows[$this->last]['macros'][] = [$name, $arguments];

                return $this;
            }
        }

        return parent::__call($name, $arguments);
    }

    /**
     * @param  string  $name
     * @return $this|ModelInfoTableComponent
     */
    public function __get(string $name)
    {
        if (
            preg_match("/^row_(.+)$/", $name, $matches)
            && !isset(Component::$inputs[$name])
            && !Component::hasComponentStatic($name)
        ) {
            $name = str_replace(['_dot_', '__'], '.', Str::snake($matches[1], '_'));
            $label = ucfirst(str_replace(['.', '_'], ' ', $name));

            return $this->row(Lang::has("admin.$name") ? __("admin.$name") : $label, $name);
        }

        return parent::__get($name);
    }

    /**
     * @throws Throwable
     */
    protected function mount(): void
    {
        $data = [];

        if ($this->model) {
            foreach ($this->rows as $row) {
                $field = $row['field'];
                $label = $row['label'];
                $macros = $row['macros'];
                if (is_string($field)) {
                    $ddd = multi_dot_call($this->model, $field);
                    $field = is_array($ddd) || is_object($ddd) || is_null($ddd) || is_bool($ddd) ? $ddd : e($ddd);
                } elseif (is_array($field) || is_embedded_call($field)) {
                    $field = embedded_call($field, [
                        is_object($this->model) ? get_class($this->model) : 'model' => $this->model,
                        Model::class => $this->model,
                        self::class => $this,
                    ]);
                }
                foreach ($macros as $macro) {
                    $field = ModelTableComponent::callExtension($macro[0], [
                        'model' => !is_array($this->model) ? $this->model : null,
                        'value' => $field,
                        'field' => $row['field'],
                        'title' => $row['label'],
                        'props' => $macro[1],
                        (is_object($this->model) ? get_class($this->model) : gettype($this->model)) => $this->model,
                    ]);
                }
                $label = __($label);
                if ($row['info']) {
                    $label .= admin_view('components.model-info-table.info-field', [
                        'info' => __($row['info'])
                    ])->render();
                }
                $data[] = [$label, $field];
            }
        }

        $this->table()->rows($data);
    }
}
