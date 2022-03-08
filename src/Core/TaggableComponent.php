<?php

namespace LteAdmin\Core;

use Lar\Layout\Abstracts\Component;
use LteAdmin\Controllers\Controller;

class TaggableComponent extends Component
{
    /**
     * @var string[]
     */
    protected static $collection = [

    ];

    /**
     * TagableComponent constructor.
     */
    public function __construct()
    {
        static::injectCollection(static::$collection);
        static::injectCollection(Controller::getExplanationList());
    }
}
