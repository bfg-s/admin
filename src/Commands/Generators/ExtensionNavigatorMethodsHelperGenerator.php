<?php

namespace LteAdmin\Commands\Generators;

use Illuminate\Console\Command;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use LteAdmin;
use LteAdmin\Interfaces\LteHelpGeneratorInterface;
use LteAdmin\Navigate;
use ReflectionClass;
use ReflectionException;

class ExtensionNavigatorMethodsHelperGenerator implements LteHelpGeneratorInterface
{
    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        $class = class_entity('NavigatorMethods');
        $class->namespace("LteAdmin\Core");

        $class->doc(function ($doc) {
            /** @var DocumentorEntity $doc */
            $this->generateAllMethods($doc);
        });

        return $class->render();
    }

    /**
     * Generate all methods.
     *
     * @param  DocumentorEntity  $doc
     */
    protected function generateAllMethods($doc)
    {
        $methods = [];

        $nav = new ReflectionClass(Navigate::class);

        foreach ($nav->getMethods() as $method) {
            $methods[$method->getName()] = $method;
        }
        foreach ($methods as $method) {
            $ret = pars_return_from_doc($method->getDocComment());

            $doc->tagMethod(
                'self|static|'.($ret ? $ret : '\\'.Navigate::class),
                $method->getName().'('.refl_params_entity($method->getParameters()).')',
                pars_description_from_doc($method->getDocComment())
            );
        }
    }
}
