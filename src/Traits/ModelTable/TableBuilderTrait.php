<?php

declare(strict_types=1);

namespace Admin\Traits\ModelTable;

use Admin\Components\ModelTable\BodyComponent;
use Admin\Components\ModelTable\ColumnComponent;
use Admin\Components\ModelTable\HeadComponent;
use Admin\Components\ModelTable\HeaderComponent;
use Admin\Components\ModelTable\RowComponent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait TableBuilderTrait
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function _build(): void
    {
        $header = $this->createComponent(HeadComponent::class);

        $this->appEnd($header);

        $header_count = 0;

        foreach ($this->columns as $key => $column) {
            if ((request()->has('show_deleted') && !$column['trash']) || $column['hide']) {
                continue;
            }

            $this->makeHeadTH($header, $column, $key);

            $header_count++;
        }

        $body = $this->createComponent(BodyComponent::class);

        $this->appEnd($body);

        foreach ($this->paginate ?? $this->model as $item) {
            $row = RowComponent::create();
            $this->makeBodyTR($row, $item);
            $body->appEnd($row);
        }

        $count = 0;

        if (is_array($this->model)) {
            $count = count($this->model);
        } elseif ($this->paginate) {
            $count = $this->paginate->count();
        }

        if (!$count) {
            $body->view('components.model-table.empty', [
                'header_count' => $header_count
            ]);
        }
    }

    /**
     * @param  HeadComponent  $head
     * @param  array  $column
     * @param  string|int  $key
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function makeHeadTH(HeadComponent $head, array $column, string|int $key): void
    {
        $select = request()->get($this->model_name.'_type', $this->order_type);
        $now = request()->get($this->model_name, $this->order_field) == $column['sort'];
        $type = $now ? ($select === 'desc' ? 'down' : 'up-alt') : 'down';

        $header = $head->createComponent(HeaderComponent::class);
        $header->setViewData([
            'column' => $column,
            'model_name' => $this->model_name,
            'now' => $now,
            'select' => $select,
            'type' => $type,
        ]);
        $this->columns[$key]['header'] = $header;
        $head->appEnd($header);
    }

    /**
     * @param  RowComponent  $row
     * @param $item
     */
    protected function makeBodyTR(RowComponent $row, $item): void
    {
        foreach ($this->columns as $column) {
            $value = $column['field'];

            if ((request()->has('show_deleted') && !$column['trash']) || $column['hide']) {
                continue;
            }

            $columnComponent = $row->createComponent(ColumnComponent::class);

            if (is_string($value)) {
                $ddd = multi_dot_call($item, $value);
                $value = is_array($ddd) || is_object($ddd) ? $ddd : e($ddd);
            } elseif (is_embedded_call($value)) {
                $value = call_user_func_array($value, [
                    $item, $column['label'], $columnComponent, $column['header'], $row,
                ]);
            }
            foreach ($column['macros'] as $macro) {
                $value = static::callE($macro[0], [
                    $value, $macro[1], $item, $column['field'], $column['label'], $columnComponent, $column['header'], $row,
                ]);
            }

            $columnComponent->setViewData([
                'value' => $value
            ]);

            $row->appEnd($columnComponent);
        }
    }

    /**
     * Get the array of elements to pass to the view.
     *
     * @param  LengthAwarePaginator  $page
     * @return array
     */
    protected function paginationElements(LengthAwarePaginator $page): array
    {
        $window = UrlWindow::make($page);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }
}
