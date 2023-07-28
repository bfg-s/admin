<?php

namespace Admin\Core;

use Admin\Traits\SearchFormConditionRulesTrait;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;

class PrepareExport implements FromCollection
{
    use SearchFormConditionRulesTrait;

    public static $columns = [];

    /**
     * @var Model|string
     */
    protected $model;

    /**
     * @var array
     */
    protected $ids;

    /**
     * @var string
     */
    protected $order;

    /**
     * @var string
     */
    protected $order_type;

    /**
     * @var string
     */
    protected string $table;

    /**
     * @var array
     */
    public static array $fields = [];

    /**
     * @param  string  $model
     * @param  array  $ids
     * @param  string  $order
     * @param  string  $order_type
     * @param  string  $table
     */
    public function __construct(string $model, array $ids, string $order, string $order_type, string $table)
    {
        $this->model = $model;
        $this->ids = $ids;
        $this->order = $order;
        $this->order_type = $order_type;
        $this->table = $table;
    }

    public function collection()
    {
        $query = $this->model::query();
        if ($this->ids) {
            $query = $query->whereIn('id', $this->ids);
        } else {
            if (request()->has('q')) {
                $r = request('q');
                if (is_string($r)) {
                    $query = $query->orWhere(function ($q) use ($r) {
                        foreach (static::$fields as $field) {
                            if (!str_ends_with($field['field_name'], '_at')) {
                                $q = $q->orWhere($field['field_name'], 'like', "%{$r}%");
                            }
                        }
                        return $q;
                    });
                } elseif (is_array($r)) {
                    foreach ($r as $key => $val) {
                        if ($val != null) {
                            foreach (static::$fields as $field) {
                                if ($field['field_name'] === $key) {
                                    $val = method_exists($field['class'], 'transformValue') ?
                                        $field['class']::transformValue($val) :
                                        $val;

                                    if (is_embedded_call($field['method'])) {
                                        $result = call_user_func($field['method'], $query, $val, $key);

                                        if ($result) {
                                            $query = $result;
                                        }
                                    } else {
                                        $query = $this->{$field['method']}(
                                            $query,
                                            $val,
                                            $key
                                        );
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($this->order && $this->order_type) {
            $query = $query->orderBy($this->order, $this->order_type);
        }

        $exportCollection = [];
        foreach ($query->get() as $item) {
            foreach (static::$columns as $tables) {
                foreach ($tables as $column) {
                    if (is_string($column['field'])) {
                        $ddd = multi_dot_call($item, $column['field']);
                        $exportCollection[$column['header']][] = is_array($ddd) || is_object($ddd) || is_null($ddd) || is_bool($ddd) ? $ddd : e($ddd);
                    } elseif (is_callable($column['field'])) {
                        $exportCollection[$column['header']][] = embedded_call($column['field'], [
                            $this->model => $item,
                        ]);
                    }
                }
            }
        }
        $headers = [];
        $result = [];
        $i = 1;
        foreach ($exportCollection as $head => $col) {
            $headers[] = $head;
            foreach ($col as $ic => $item) {
                $result[$ic][$i] = strip_tags($item);
            }
            $i++;
        }
        array_unshift($result, $headers);

        return collect($result);
    }
}
