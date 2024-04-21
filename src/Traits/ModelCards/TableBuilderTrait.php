<?php

declare(strict_types=1);

namespace Admin\Traits\ModelCards;

use Admin\Components\ModelCards\CardComponent;
use Admin\Components\ModelTable\HeadComponent;
use Admin\Components\ModelTableComponent;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;

trait TableBuilderTrait
{
    /**
     * @return void
     */
    protected function build(): void
    {
        $header = $this->createComponent(HeadComponent::class);

        $this->appEnd($header);

        $header_count = 0;

        foreach ($this->rows as $key => $row) {
            if ((request()->has('show_deleted') && !$row['trash']) || $row['hide']) {
                continue;
            }

            $header_count++;
        }

        foreach ($this->paginate ?? $this->model as $item) {
            $this->makeBodyCard($item);
        }

        $count = 0;

        if (is_array($this->model)) {
            $count = count($this->model);
        } elseif ($this->paginate) {
            $count = $this->paginate->count();
        }

        if (! $count) {
            $this->view('components.model-cards.empty', [
                'header_count' => $header_count
            ]);
        }
    }

    /**
     * @param $item
     */
    protected function makeBodyCard($item): void
    {
        $cardComponent = $this->createComponent(CardComponent::class);

        foreach ($this->rows as $key => $row) {
            $value = $row['field'];

            if ((request()->has('show_deleted') && !$row['trash']) || $row['hide']) {
                continue;
            }

            if (is_string($value)) {
                $ddd = multi_dot_call($item, $value);
                $value = is_array($ddd) || is_object($ddd) ? $ddd : e($ddd);
            } elseif (is_embedded_call($value)) {
                $value = call_user_func_array($value, [
                    $item, $row['label'], $cardComponent, null, $row,
                ]);
            }
            foreach ($row['macros'] as $macro) {
                $value = ModelTableComponent::callE($macro[0], [
                    $value, $macro[1], $item, $row['field'], $row['label'], $cardComponent, null, $row,
                ]);
            }

            $this->rows[$key]['value'] = $value;
        }

        $cardComponent->setViewData([
            'rows' => $this->rows,
            'model' => $item,
            'avatarField' => $this->avatarField,
            'titleField' => $this->titleField,
            'subtitleField' => $this->subtitleField,
            'buttons' => $this->buttons,
            'checkBox' => $this->checkBox,
        ]);

        $this->appEnd($cardComponent);
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
