<?php

namespace Admin\Traits\ModelTable;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Admin\Components\ButtonsComponent;
use Admin\Core\PrepareExport;
use Illuminate\Support\Str;

trait TableHelpersTrait
{
    /**
     * Has models on process.
     * @var array
     */
    protected static array $models = [];

    /**
     * @var bool
     */
    protected bool $init = false;

    /**
     * @param  int  $per_page
     * @return $this
     */
    public function perPage(int $per_page): static
    {
        if (is_int($this->per_page)) {
            $this->per_page = $per_page;
        }

        return $this;
    }

    /**
     * @param  array  $per_pages
     * @return $this
     */
    public function perPages(array $per_pages): static
    {
        $this->per_pages = $per_pages;

        return $this;
    }

    /**
     * @param  string  $field
     * @param  string  $type
     * @return $this
     */
    public function orderBy(string $field, string $type = 'asc'): static
    {
        $this->order_field = $field;

        $this->order_type = $type;

        return $this;
    }

    /**
     * @param ...$delegates
     * @return $this
     */
    public function buttons(...$delegates): static
    {
        $title = isset($delegates[0]) && is_string($delegates[0])
            ? $delegates[0]
            : null;

        $this->col(
            $title,
            fn($model) => ButtonsComponent::create()
                ->model($model)
                ->delegatesNow($delegates)
        );

        return $this;
    }

    /**
     * Alias of column.
     * @param  array|string|Closure|null  $label
     * @param  array|string|Closure|null  $field
     * @return $this
     */
    public function col(array|string|Closure|null $label, array|string|Closure $field = null): static
    {
        return $this->column($label, $field);
    }

    /**
     * @param  string|Closure|array|null  $label
     * @param  array|string|Closure|null  $field
     * @return $this
     */
    public function column($label, array|string|Closure $field = null): static
    {
        if ($field === null) {
            $field = $label;

            $label = null;
        }

        $this->last = uniqid('column');

        $key = Str::slug($this->model_name.(is_string($field) ? '_'.$field : ''), '_');

        $col = [
            'field' => $field,
            'label' => is_string($label) ? __($label) : $label,
            'sort' => false,
            'trash' => true,
            'info' => false,
            'key' => is_string($field) ? $key : null,
            'hide' => request()->has($key) && request($key) == 0,
            'macros' => [],
        ];

        if ($this->prepend) {
            $this->prepend = false;
            array_unshift($this->columns, $col);
        } else {
            $this->columns[$this->last] = $col;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function only_export(callable $callback = null): static
    {
        if (isset($this->columns[$this->last])) {
            $this->to_export($callback);
            unset($this->columns[$this->last]);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function to_export(callable $callback = null): static
    {
        if (isset($this->columns[$this->last])) {
            PrepareExport::$columns[$this->model_name][$this->last] = [
                'header' => $this->columns[$this->last]['label'],
                'field' => $callback ?: $this->columns[$this->last]['field'],
            ];
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function to_prepend(): static
    {
        $this->prepend = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function not_trash(): static
    {
        if (isset($this->columns[$this->last])) {
            $this->columns[$this->last]['trash'] = false;
        }

        return $this;
    }

    /**
     * @param  string  $info
     * @return $this
     */
    public function info(string $info): static
    {
        if (isset($this->columns[$this->last])) {
            $this->columns[$this->last]['info'] = $info;
        }

        return $this;
    }

    /**
     * @param  string|null  $showLanguageCode
     * @return $this
     */
    public function lang(string $showLanguageCode = null): static
    {
        $showLanguageCode = $showLanguageCode ?: App::getLocale();

        if (
            isset($this->columns[$this->last])
            && is_string($this->columns[$this->last]['field'])
        ) {
            $this->columns[$this->last]['field'] .= ".{$showLanguageCode}";
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function to_hide(string $key = null): static
    {
        if ($key) {
            $this->columns[$this->last]['key']
                = $this->model_name.'_'.Str::slug($key, '_');
        }
        if (
            !$this->columns[$this->last]['key']
            && $this->columns[$this->last]['sort']
        ) {
            $this->columns[$this->last]['key']
                = $this->columns[$this->last]['sort'];
        }
        if (
            isset($this->columns[$this->last])
            && $this->columns[$this->last]['key']
        ) {
            $this->hasHidden = true;
            $this->columns[$this->last]['hide']
                = !(request($this->columns[$this->last]['key']) == 1);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function id(): static
    {
        $this->column('admin.id', 'id')->true_data()->hide_on_mobile()->sort();

        return $this;
    }

    /**
     * @param  string|null  $field
     * @return static
     */
    public function sort(string $field = null): static
    {
        if (isset($this->columns[$this->last])) {
            $this->columns[$this->last]['sort'] =
                $field ?
                    $field :
                    (
                    is_string($this->columns[$this->last]['field']) ?
                        $this->columns[$this->last]['field'] :
                        false
                    );
        }

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
        $this->column('admin.created_at', 'created_at')->beautiful_date_time()->true_data()->hide_on_mobile()->sort();

        return $this;
    }

    /**
     * @return $this
     */
    public function updated_at(): static
    {
        $this->column('admin.updated_at', 'updated_at')->beautiful_date_time()->true_data()->hide_on_mobile()->sort();

        return $this;
    }

    /**
     * @return $this
     */
    public function deleted_at(): static
    {
        $this->column('admin.deleted_at', 'deleted_at')->beautiful_date_time()->true_data()->hide_on_mobile()->sort();

        return $this;
    }

    /**
     * @return $this
     */
    public function active_switcher(): static
    {
        $this->column('admin.active', 'active')->input_switcher()->hide_on_mobile()->sort();

        return $this;
    }

    /**
     * @return string
     */
    public function getModelName(): string
    {
        if ($this->model_name) {
            return $this->model_name;
        }

        $class = null;
        if ($this->model instanceof Model) {
            $class = get_class($this->model);
        } elseif ($this->model instanceof Builder) {
            $class = get_class($this->model->getModel());
        } elseif ($this->model instanceof Relation) {
            $class = get_class($this->model->getModel());
        } elseif (is_object($this->model)) {
            $class = get_class($this->model);
        } elseif (is_string($this->model)) {
            $class = $this->model;
        } elseif (is_array($this->model)) {
            $class = substr(md5(json_encode($this->model)), 0, 10);
        }
        $this->model_class = $class;
        $return = $class ? strtolower(class_basename($class)) : 'object_'.spl_object_id($this);
        $prep = '';
        if (isset(static::$models[$return])) {
            $prep .= static::$models[$return];
            static::$models[$return]++;
        } else {
            static::$models[$return] = 1;
        }

        return $this->model_name = $return.$prep;
    }

    /**
     * @return View|string
     */
    public function footer(): string|View
    {
        return $this->paginate ? admin_view('components.model-table.footer', [
            'model' => $this->model,
            'paginator' => $this->paginate,
            'from' => (($this->paginate->currentPage() * $this->paginate->perPage()) - $this->paginate->perPage()) + 1,
            'to' => min(($this->paginate->currentPage() * $this->paginate->perPage()), $this->paginate->total()),
            'per_page' => $this->per_page,
            'per_pages' => $this->per_pages,
            'elements' => $this->paginationElements($this->paginate),
            'page_name' => $this->model_name.'_page',
            'per_name' => $this->model_name.'_per_page',
        ]) : '';
    }
}
