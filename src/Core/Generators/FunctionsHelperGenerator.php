<?php

namespace Lar\LteAdmin\Core\Generators;

use Exception;
use Illuminate\Console\Command;
use Lar\Developer\Commands\Dump\DumpExecute;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\LteAdmin\Models\LteFunction;
use ReflectionException;
use Schema;

class FunctionsHelperGenerator implements DumpExecute
{
    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        $namespace = namespace_entity("Lar\LteAdmin\Core");

        $namespace->wrap('php');

        $namespace->class('FunctionsDoc', function ($class) {
            $class->doc(function ($doc) {
                /** @var DocumentorEntity $doc */
                $this->generateDefaultMethods($doc);
            });
        });

        file_put_contents(base_path('_ide_helper_lte_func.php'), $namespace->render());
    }

    /**
     * Generate default methods.
     *
     * @param  DocumentorEntity  $doc
     * @throws ReflectionException
     */
    protected function generateDefaultMethods($doc)
    {
        try {
            if (Schema::hasTable((new LteFunction)->getTable())) {
                foreach (LteFunction::all() as $func) {
                    $doc->tagPropertyRead('bool', $func->slug,
                        "Check if the user has access to the function ({$func->description})");
                }
            }
        } catch (Exception $exception) {
        }
    }
}
