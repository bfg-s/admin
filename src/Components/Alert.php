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
     * @param  mixed  ...$params
     */
    public function __construct(string $title = null, ...$params)
    {
        parent::__construct();

        if ($title) {

            $this->h4(['alert-heading'])->text($title);
        }

        $this->when($params);
    }
}