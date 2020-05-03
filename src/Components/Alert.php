<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;

class Alert extends DIV
{
    /**
     * @var array
     */
    protected $props = [
        'alert', 'role' => 'alert'
    ];

    /**
     * @var bool
     */
    public $opened_mode = true;

    /**
     * Col constructor.
     * @param  string|null  $title
     * @param  string|null  $icon
     * @param  mixed  ...$params
     */
    public function __construct(string $title = null, string $icon = null, ...$params)
    {
        parent::__construct();

        if ($title) {

            $h4 = $this->h4(['alert-heading']);

            if ($icon) {

                $h4->i([$icon]);
                $h4->text(':space');
            }

            if ($title) {

                $h4->text(__($title));
            }
        }

        $this->when($params);
    }
}