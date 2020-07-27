<?php


namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\RootTools\GatesTool;
use Lar\LteAdmin\Components\RootTools\GeneralInformationTool;

/**
 * Class RootTools
 * @package Lar\LteAdmin\Components
 */
class RootTools extends DIV
{
    /**
     * @var array
     */
    static $tabs = [
        GeneralInformationTool::class,
        GatesTool::class
    ];

    /**
     * @var string[]
     */
    protected $props = [
        'id' => 'root-tools',
        'collapse', 'container-fluid'
    ];

    /**
     * @var string[]
     */
    public $execute = [
        "build"
    ];

    /**
     * Build tools
     */
    protected function build()
    {
        $this->row()
            ->col()
            ->card('Root tools')->success()
            ->tabs()
            ->tabList(static::$tabs);
    }
}