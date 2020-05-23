<?php

namespace Lar\LteAdmin\Core\Generators;

use Illuminate\Console\Command;
use Lar\Developer\Commands\Dump\DumpExecute;
use Lar\EntityCarrier\Core\Entities\ClassEntity;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\Layout\Tags\TABLE;

/**
 * Class FunctionsHelperGenerator
 * @package Lar\LteAdmin\Core
 */
class TableMacrosesHelperGenerator implements DumpExecute {

    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        \Lar\Layout\Tags\TABLE::addMacroClass(\Lar\LteAdmin\Core\TableMacros::class);

        $namespace = namespace_entity("Lar\LteAdmin\Core");

        $namespace->class("TableMacrosDoc", function (ClassEntity $class) {

            $class->doc(function (DocumentorEntity $doc) {

                $this->generateDefaultMethods($doc);
            });
        });

        return $namespace->render();
    }

    /**
     * Generate default methods
     *
     * @param DocumentorEntity $doc
     * @throws \ReflectionException
     */
    protected function generateDefaultMethods(DocumentorEntity $doc)
    {
        foreach (TABLE::$column_macros as $func => $data) {

            $doc->tagMethod("\\".\Lar\LteAdmin\Segments\Tagable\Table::class, $func."(...\$params)", "Table macros {$func}");
        }
    }
}