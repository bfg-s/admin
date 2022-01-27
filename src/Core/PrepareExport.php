<?php

namespace Lar\LteAdmin\Core;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\FromCollection;
use Request;
use Route;

class PrepareExport implements FromCollection
{
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
        Route::dispatch(
            Request::create(
                Request::server('HTTP_REFERER')
            )
        )->getContent();
    }

    public function collection()
    {
        $query = $this->model::query();
        if ($this->ids) {
            $query = $query->whereIn('id', $this->ids);
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
                $result[$ic][$i] = $item;
            }
            $i++;
        }
        array_unshift($result, $headers);

        return collect($result);
    }
}
