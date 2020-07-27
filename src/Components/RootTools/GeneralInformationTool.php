<?php

namespace Lar\LteAdmin\Components\RootTools;

use Lar\LteAdmin\Segments\Tagable\Col;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\Row;
use Lar\LteAdmin\Segments\Tagable\TabContent;

/**
 * Class GeneralInformationTool
 * @package Lar\LteAdmin\Components\RootTools
 */
class GeneralInformationTool extends TabContent
{
    /**
     * @var string
     */
    protected $icon = "fas fa-info-circle";

    /**
     * @var string
     */
    protected $title = "General information";

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
        $this->row(function (Row $row) {

            if (lte_now()) {
                $row->col(6)->model_info_table(lte_now(), function (ModelInfoTable $table) {

                    $table->row('ID', 'id')->badge('dark');
                    $table->row('Parent ID', 'parent_id')->badge('dark');
                    $table->row('Resource', 'resource')->yes_no();
                    $table->row('Type', function () { return lte_model_type(); });
                    $table->row('Icon', 'icon')->copied();
                });

                $row->col(6)->model_info_table(lte_now(), function (ModelInfoTable $table) {

                    $table->row('Route', 'route')->copied();
                    $table->row('Action', function () {
                        $lte = lte_now();
                        return isset($lte['action']) ? $lte['action'] :
                            (isset($lte['resource']) && isset($lte['resource']['action']) ? $lte['resource']['action'] : null);
                    })->trim('\\')->copied();
                    $table->row('Model', function () { return gets()->lte->menu->model; })->to_string()->copied();
                    $table->row('Extension', 'extension')->to_string()->copied();
                    $table->row('Link', 'link')->copied();
                });
            }

            else {

                $this->div()->textCenter()->textMuted()->w100()
                    ->h3('No information available!');
            }
        });
    }
}