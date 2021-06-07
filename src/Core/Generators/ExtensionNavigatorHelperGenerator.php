<?php

namespace Lar\LteAdmin\Core\Generators;

use Illuminate\Console\Command;
use Lar\Developer\Commands\Dump\DumpExecute;
use Lar\EntityCarrier\Core\Entities\ClassEntity;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\LteAdmin\Navigate;

/**
 * Class FunctionsHelperGenerator
 * @package Lar\LteAdmin\Core
 */
class ExtensionNavigatorHelperGenerator implements DumpExecute {

    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        $namespace = namespace_entity("Lar\LteAdmin\Core");

        $namespace->class("NavigatorExtensions", function (ClassEntity $class) {

            $class->doc(function (DocumentorEntity $doc) {

                $this->generateDefaultMethods($doc);
            });
        });

        $namespace->class("NavigatorMethods", function (ClassEntity $class) {

            $class->doc(function (DocumentorEntity $doc) {

                $this->generateAllMethods($doc);
            });
        });

        return $namespace->render();
    }

    /**
     * Generate all methods
     *
     * @param  DocumentorEntity  $doc
     */
    protected function generateAllMethods(DocumentorEntity $doc)
    {
        $methods = [];

        $nav = new \ReflectionClass(Navigate::class);

        foreach ($nav->getMethods() as $method) {
            $methods[$method->getName()] = $method;
        }
        foreach ($methods as $method) {

            $ret = pars_return_from_doc($method->getDocComment());

            $doc->tagMethod('self|static|' . ($ret ? $ret : '\\'.Navigate::class), $method->getName() . "(".refl_params_entity($method->getParameters()).")", pars_description_from_doc($method->getDocComment()));
        }
    }

    /**
     * Generate default methods
     *
     * @param DocumentorEntity $doc
     * @throws \ReflectionException
     */
    protected function generateDefaultMethods(DocumentorEntity $doc)
    {
        foreach (\LteAdmin::extensions() as $name => $provider) {

            $doc->tagMethod('void', $provider::$slug, "Make extension routes ($name})");
        }
    }
}
