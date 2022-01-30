<?php

namespace LteAdmin\Core\Generators;

use Illuminate\Console\Command;
use Lar\Developer\Commands\Dump\DumpExecute;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use LteAdmin;
use LteAdmin\Navigate;
use ReflectionClass;
use ReflectionException;

class ExtensionNavigatorHelperGenerator implements DumpExecute
{
    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        $namespace = namespace_entity("LteAdmin\Core");

        $namespace->class('NavigatorExtensions', function ($class) {
            $class->doc(function ($doc) {
                /** @var DocumentorEntity $doc */
                $this->generateDefaultMethods($doc);
            });
        });

        $namespace->class('NavigatorMethods', function ($class) {
            $class->doc(function ($doc) {
                /** @var DocumentorEntity $doc */
                $this->generateAllMethods($doc);
            });
        });

        return $namespace->render();
    }

    /**
     * Generate default methods.
     *
     * @param  DocumentorEntity  $doc
     * @throws ReflectionException
     */
    protected function generateDefaultMethods($doc)
    {
        foreach (LteAdmin::extensions() as $name => $provider) {
            $doc->tagMethod('void', $provider::$slug, "Make extension routes ($name})");
        }
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

            $doc->tagMethod('self|static|'.($ret ? $ret : '\\'.Navigate::class),
                $method->getName().'('.refl_params_entity($method->getParameters()).')',
                pars_description_from_doc($method->getDocComment()));
        }
    }
}
