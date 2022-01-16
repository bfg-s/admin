<?php

namespace Lar\LteAdmin\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class PrepareExport implements FromCollection
{
    static $columns = [];

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

    public function __construct(string $model, array $ids, string $order, string $order_type)
    {
        $this->model = $model;
        $this->ids = $ids;
        $this->order = $order;
        $this->order_type = $order_type;
        \Route::dispatch(
            \Request::create(
                \Request::server('HTTP_REFERER')
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
            foreach (static::$columns as $column) {
                if (is_string($column['field'])) {
                    $exportCollection[$column['header']][] = multi_dot_call($item, $column['field']);
                } else if (is_callable($column['field'])) {
                    $exportCollection[$column['header']][] = app()->call($column['field']);
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
