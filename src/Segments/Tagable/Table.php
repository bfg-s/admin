<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Tags\DIV;

/**
 * Class Table
 * @package Lar\LteAdmin\Segments\Tagable
 * @mixin \Lar\LteAdmin\Core\TableOriginMacrosDoc
 * @mixin \Lar\LteAdmin\Core\TableMacrosDoc
 */
class Table extends DIV {

    /**
     * @var bool
     */
    protected $only_content = true;

    /**
     * @var array|Model|mixed|null
     */
    protected $model;

    /**
     * @var \Lar\LteAdmin\Components\Table
     */
    protected $table;

    /**
     * @var \Closure[]
     */
    protected $table_rendered = [];

    /**
     * @var array
     */
    protected $columns = [];

    /**
     * @var string
     */
    protected $last_column;

    /**
     * Table constructor.
     * @param  null|Model|array|\Closure  $model
     * @param  \Closure|null  $after
     */
    public function __construct($model = null, \Closure $after = null)
    {
        parent::__construct();

        if ($model instanceof \Closure) {

            $this->model = gets()->lte->menu->model;

            $this->table = new \Lar\LteAdmin\Components\Table($this->model);

            $model($this);

        } else {

            if (!$model) {

                $model = gets()->lte->menu->model;
            }

            if (is_string($model) && class_exists($model)) {

                $model = new $model;
            }

            $this->model = $model;

            $this->table = new \Lar\LteAdmin\Components\Table($this->model);
        }

        if ($after) {

            $after($this);
        }

        $this->toExecute('buildTable');
    }

    /**
     * @param \Closure|array $instructions
     * @return $this
     */
    public function model($instructions)
    {
        $this->table->model($instructions);

        return $this;
    }

    /**
     * @param  int  $per_page
     * @return $this
     */
    public function per_page(int $per_page)
    {
        $this->table->perPage($per_page);

        return $this;
    }

    /**
     * @param array|string $per_pages
     * @return $this
     */
    public function display_by($per_pages)
    {
        $this->table->perPages($per_pages);

        return $this;
    }

    /**
     * @param  string|null  $field
     * @return $this
     */
    public function order_desc(string $field = null)
    {
        $this->table->orderDesc($field);

        return $this;
    }

    /**
     * @param  string  $title
     * @param string|\Closure $field
     * @return $this
     */
    public function column(string $title, $field)
    {
        $this->last_column = uniqid('column');

        $this->columns[$this->last_column] = [
            'title' => $title,
            'field' => $field,
            'macros' => [],
            'sort' => null,
            'prepend' => false
        ];

        return $this;
    }

    /**
     * @param  string|null  $field
     * @return $this
     */
    public function sort(string $field = null)
    {
        if ($this->last_column) {

            $col = $this->columns[$this->last_column];

            if (!$field && is_string($col['field'])) {

                $this->columns[$this->last_column]['sort'] = $col['field'];

            } else if ($field) {

                $this->columns[$this->last_column]['sort'] = $field;
            }
        }

        return $this;
    }

    /**
     * @param  \Closure  $closure
     * @return $this
     */
    public function table_rendered(\Closure $closure)
    {
        $this->table_rendered[] = $closure;

        return $this;
    }

    /**
     * Build table
     */
    protected function buildTable()
    {
        $this->table->merge_rendered($this->table_rendered);

        $this->table->disableDefaultsId();

        foreach ($this->columns as $column) {

            $column = $this->build_wrapper($column);

            $this->table->column($column['title'], $column['field'], $column['sort'], $column['prepend']);
        }

        $this->appEnd($this->table);
    }

    /**
     * @param  array  $column
     * @return array
     */
    protected function build_wrapper(array $column)
    {
        if (is_string($column['field'])) {
            foreach ($column['macros'] as $key => $macro) {
                if (!$key) {
                    $column['field'] = $macro[0].":".$column['field'].(count($macro[1]) ? ",".implode(',', $macro[1]):'');
                } else {
                    $column['field'] = $macro[0].(count($macro[1]) ? "(".implode(',', $macro[1]).")":'').":".$column['field'];
                }
            }
        }

        return $column;
    }

    /**
     * @param $name
     * @param $arguments
     * @return $this|bool|Table|\Lar\Tagable\Tag|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        $macros = \Lar\Layout\Tags\TABLE::$column_macros;

        if (isset($macros[$name]) && $this->last_column) {

            $this->columns[$this->last_column]['macros'][] = [$name, $arguments];

            return $this;
        }

        return parent::__call($name, $arguments); // TODO: Change the autogenerated stub
    }

    /**
     * @param $name
     * @param $arguments
     * @return Table|mixed
     * @throws \Exception
     */
    public static function __callStatic($name, $arguments)
    {
        if (preg_match('/^macro_(.*)/', $name, $m)) {

            return \Lar\Layout\Tags\TABLE::callMacro($m[1], ...$arguments);
        }

        return parent::__callStatic($name, $arguments); // TODO: Change the autogenerated stub
    }
}