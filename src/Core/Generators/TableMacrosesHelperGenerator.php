<?php

namespace Lar\LteAdmin\Core\Generators;

use Illuminate\Console\Command;
use Lar\Developer\Commands\Dump\DumpExecute;
use Lar\EntityCarrier\Core\Entities\ClassEntity;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\LteAdmin\Segments\Tagable\ModelTable;

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
        foreach (ModelTable::getExtensionList() as $func => $data) {

            $doc->tagMethod("self|static|\\".\Lar\LteAdmin\Segments\Tagable\ModelTable::class, $func."(...\$params)", "Table macros {$func}");
        }

        foreach (ModelTable::get_macro_names() as $macro_name) {

            $ref = ModelTable::get_macro_reflex($macro_name);

            $doc->tagMethod(

                "\\".ModelTable::class,
                $macro_name . "(".refl_params_entity($ref->getParameters()).")",
                "ModelTable Macro $macro_name"
            );
        }
    }
}