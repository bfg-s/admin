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
class TableOriginalMacrosesHelperGenerator implements DumpExecute {

    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        $namespace = namespace_entity("Lar\LteAdmin\Core");

        $namespace->class("TableOriginMacrosDoc", function (ClassEntity $class) {

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

            $params = null;

            if (is_array($data)) {

                $ref = new \ReflectionClass($data[0]);
                $params = $ref->getMethod($data[1])->getParameters();

            } else if ($data instanceof \Closure) {

                $ref = new \ReflectionFunction($data);
                $params = $ref->getParameters();
            }

            if ($params) {

                $doc->tagMethod("mixed", 'macro_'.$func."(".refl_params_entity($params).")", "Table macros {$func}");
            }
        }
    }
}