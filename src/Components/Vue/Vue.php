<?php

declare(strict_types=1);

namespace Admin\Components\Vue;

use Admin\Components\Component;
use Illuminate\Support\Str;

/**
 * Abstract class Vue components.
 */
abstract class Vue extends Component
{
    /**
     * Counter Vue of elements on the admin panel page.
     *
     * @var array
     */
    protected static array $count = [];

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        $this->attr('name', $this->element);

        if (!isset(static::$count[$this->element])) {
            static::$count[$this->element] = 0;
        } else {
            static::$count[$this->element]++;
        }

        $num = static::$count[$this->element];

        $id = Str::slug($this->element, '_').($num ? '_'.$num : '');

        $this->attr('id', $id);

        $this->attr('data-load', 'vueInit');
    }
}
