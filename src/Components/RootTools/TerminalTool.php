<?php

namespace Lar\LteAdmin\Components\RootTools;

use Lar\LteAdmin\Components\Vue\ScaffoldTools;
use Lar\LteAdmin\Components\Vue\TerminalTools;
use Lar\LteAdmin\Segments\Tagable\Col;
use Lar\LteAdmin\Segments\Tagable\Field;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\Row;
use Lar\LteAdmin\Segments\Tagable\TabContent;

/**
 * Class GeneralInformationTool
 * @package Lar\LteAdmin\Components\RootTools
 */
class TerminalTool extends TabContent
{
    /**
     * @var string
     */
    protected $icon = "fas fa-terminal";

    /**
     * @var string
     */
    protected $title = "Terminal";

    /**
     * @var string[]
     */
    protected $props = [
        'tab-pane',
        'role' => 'tabpanel',
    ];

    /**
     * @var array
     */
    public $execute = [
        'build'
    ];

    /**
     * Build tab
     */
    protected function build()
    {
        $f = array_merge(['none'], array_keys(Field::$form_components));
        $this->appEnd(TerminalTools::create());
    }
}