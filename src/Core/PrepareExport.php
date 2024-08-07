<?php

declare(strict_types=1);

namespace Admin\Core;

use Admin\Traits\SearchFormConditionRulesTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Throwable;

/**
 * The part of the kernel that is responsible for exporting data from the model table.
 */
class PrepareExport implements FromCollection
{
    use SearchFormConditionRulesTrait;

    /**
     * Added columns for export.
     *
     * @var array
     */
    public static array $columns = [];

    /**
     * Table fields for export.
     *
     * @var array
     */
    public static array $fields = [];

    /**
     * Exported model.
     *
     * @var Model|string
     */
    protected string|Model $model;

    /**
     * Exported identifiers.
     *
     * @var array
     */
    protected array $ids;

    /**
     * Sort exported data by this field.
     *
     * @var string
     */
    protected string $order;

    /**
     * Type of sorting of the exported data.
     *
     * @var string
     */
    protected string $order_type;

    /**
     * Current table of exported data.
     *
     * @var string
     */
    protected string $table;

    /**
     * PrepareExport constructor.
     *
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

    /**
     * Get a collection of exported data.
     *
     * @return Collection
     * @throws Throwable
     */
    public function collection(): Collection
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

                $result[$ic][$i] = strip_tags((string) $item);
            }
            $i++;
        }
        array_unshift($result, $headers);

        return collect($result);
    }
}
