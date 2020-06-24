<?php

namespace Lar\LteAdmin;

use Lar\LteAdmin\Core\TableExtends\Decorations;
use Lar\LteAdmin\Core\TableExtends\Display;
use Lar\LteAdmin\Core\TableExtends\Editables;
use Lar\LteAdmin\Core\TableExtends\Formatter;
use Lar\LteAdmin\Segments\Tagable\ModelTable;

/**
 * Class LteBoot
 * @package Lar\LteAdmin
 */
class LteBoot
{
    /**
     * Table extensions
     * @var array
     */
    protected static $table_classes = [
        Editables::class,
        Formatter::class,
        Decorations::class,
        Display::class
    ];
    
    /**
     * Run boot Lte scripts
     */
    public static function run()
    {
        if (is_file(lte_app_path('bootstrap.php'))) {

            include lte_app_path('bootstrap.php');
        }

        include __DIR__ . '/bootstrap.php';

        foreach (static::$table_classes as $item) {

            ModelTable::addExtensionClass($item);
        }

        foreach (\LteAdmin::extensions() as $extension) {

            if ($extension->included()) {

                $extension->config()->boot();
            }
        }
    }
}
