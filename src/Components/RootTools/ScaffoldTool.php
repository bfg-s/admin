<?php

namespace Lar\LteAdmin\Components\RootTools;

use Lar\LteAdmin\Components\Vue\ScaffoldTools;
use Lar\LteAdmin\Segments\Tagable\Col;
use Lar\LteAdmin\Segments\Tagable\Field;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\Row;
use Lar\LteAdmin\Segments\Tagable\TabContent;

/**
 * Class GeneralInformationTool
 * @package Lar\LteAdmin\Components\RootTools
 */
class ScaffoldTool extends TabContent
{
    /**
     * @var string
     */
    protected $icon = "fas fa-cubes";

    /**
     * @var string
     */
    protected $title = "Scaffolding";

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
        $this->appEnd(ScaffoldTools::create(['fields' => $f]));
    }
}