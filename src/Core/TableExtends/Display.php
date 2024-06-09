<?php

declare(strict_types=1);

namespace Admin\Core\TableExtends;

use Admin\Components\ModelTable\ColumnComponent;
use Admin\Components\ModelTable\HeaderComponent;
use Admin\Components\ModelTable\RowComponent;
use Illuminate\Database\Eloquent\Model;

/**
 * The part of the kernel that is responsible for working with displaying the model table.
 */
class Display
{
    /**
     * Hide a column on a mobile device.
     *
     * @param $value
     * @param  array  $props
     * @param  Model|array|null  $model
     * @param  null  $field
     * @param  null  $title
     * @param  ColumnComponent|null  $td
     * @param  HeaderComponent|null  $th
     * @param  RowComponent|null  $tr
     * @return mixed
     */
    public function hide_on_mobile(
        $value,
        array $props = [],
        Model|array $model = null,
        $field = null,
        $title = null,
        ColumnComponent $td = null,
        HeaderComponent $th = null,
        RowComponent $tr = null
    ): mixed {
        $td?->hideOnMobile();
        $th?->hideOnMobile();

        return $value;
    }
}
