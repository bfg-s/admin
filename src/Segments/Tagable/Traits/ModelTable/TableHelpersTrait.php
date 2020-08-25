<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits\ModelTable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Segments\Tagable\SearchForm;

/**
 * Trait TableHelpersTrait
 * @package Lar\LteAdmin\Segments\Tagable\Traits
 */
trait TableHelpersTrait {

    /**
     * @param SearchForm|\Closure|array $instruction
     * @return $this
     */
    public function model($instruction)
    {
        if ($instruction instanceof SearchForm) {

            $this->search = $instruction;
        }

        $this->model_control[] = $instruction;

        return $this;
    }

    /**
     * @param  int  $per_page
     * @return $this
     */
    public function perPage(int $per_page)
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
    public function perPages(array $per_pages)
    {
        $this->per_pages = $per_pages;

        return $this;
    }

    /**
     * @param  string  $field
     * @param  string  $type
     * @return $this
     */
    public function orderBy(string $field, string $type = 'asc')
    {
        $this->order_field = $field;

        $this->order_type = $type;

        return $this;
    }

    /**
     * Alias of column
     * @param  string|\Closure|array|null  $label
     * @param  string|\Closure|array|null  $field
     * @return $this
     */
    public function col($label, $field = null)
    {
        return $this->column($label, $field);
    }

    /**
     * @param  string|\Closure|array|null  $label
     * @param  string|\Closure|array|null  $field
     * @return $this
     */
    public function column($label, $field = null)
    {
        if ($field === null) {

            $field = $label;

            $label = null;
        }

        $this->last = uniqid('column');

        $col = [
            'field' => $field,
            'label' => is_string($label) ? __($label) : $label,
            'sort' => false,
            'trash' => true,
            'info' => false,
            'macros' => []
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
    public function to_prepend()
    {
        $this->prepend = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function not_trash()
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
    public function info(string $info)
    {
        if (isset($this->columns[$this->last])) {

            $this->columns[$this->last]['info'] = $info;
        }

        return $this;
    }

    /**
     * @param  string|null  $field
     * @return $this
     */
    public function sort(string $field = null)
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
    public function id()
    {
        $this->column('lte.id', 'id')->true_data()->hide_om_mobile()->sort();

        return $this;
    }

    /**
     * @return $this
     */
    public function created_at()
    {
        $this->column('lte.created_at', 'created_at')->true_data()->hide_om_mobile()->sort();

        return $this;
    }

    /**
     * @return $this
     */
    public function updated_at()
    {
        $this->column('lte.updated_at', 'updated_at')->true_data()->hide_om_mobile()->sort();

        return $this;
    }

    /**
     * @return $this
     */
    public function at()
    {
        $this->updated_at()->created_at();

        return $this;
    }

    /**
     * @return $this
     */
    public function deleted_at()
    {
        $this->column('lte.deleted_at', 'deleted_at')->true_data()->hide_om_mobile()->sort();

        return $this;
    }

    /**
     * @return $this
     */
    public function active_switcher()
    {
        $this->column('lte.active', 'active')->input_switcher()->hide_om_mobile()->sort();

        return $this;
    }

    /**
     * Has models on process
     * @var array
     */
    static protected $models = [];

    /**
     * @param $model
     * @return string|false
     */
    public function getModelName()
    {
        if ($this->model_name) { return $this->model_name; }
        $class = null;
        if ($this->model instanceof Model) { $class = get_class($this->model); }
        else if ($this->model instanceof Builder) { $class = get_class($this->model->getModel()); }
        else if ($this->model instanceof Relation) { $class = get_class($this->model->getModel()); }
        else if (is_object($this->model)) { $class = get_class($this->model); }
        else if (is_string($this->model)) { $class = $this->model; }
        else if (is_array($this->model)) { $class = substr(md5(json_encode($this->model)), 0, 10); }
        $this->model_class = $class;
        $return = $class ? strtolower(class_basename($class)) : $this->getUnique();
        $prep = "";
        if (isset(static::$models[$return])) {
            $prep .= static::$models[$return];
            static::$models[$return]++;
        } else {
            static::$models[$return] = 1;
        }
        return $return.$prep;
    }

    /**
     * @return DIV|string
     */
    public function footer()
    {
        return $this->paginate ? DIV::create(['card-footer'])->view('lte::segment.model_table_footer', [
            'model' => $this->model,
            'paginator' => $this->paginate,
            'from' => (($this->paginate->currentPage() * $this->paginate->perPage()) - $this->paginate->perPage()) + 1,
            'to' => ($this->paginate->currentPage() * $this->paginate->perPage()) > $this->paginate->total() ? $this->paginate->total() : ($this->paginate->currentPage() * $this->paginate->perPage()),
            'per_page' => $this->per_page,
            'per_pages' => $this->per_pages,
            'elements' => $this->paginationElements($this->paginate),
            'page_name' => $this->model_name . "_page",
            'per_name' => $this->model_name . '_per_page',
        ]) : '';
    }
}