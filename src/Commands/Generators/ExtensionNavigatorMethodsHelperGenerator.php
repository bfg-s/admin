<?php

declare(strict_types=1);

namespace Admin\Commands\Generators;

use Admin\Facades\AdminFacade;
use Admin\Interfaces\AdminHelpGeneratorInterface;
use Admin\Navigate;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use Illuminate\Console\Command;
use ReflectionClass;

class ExtensionNavigatorMethodsHelperGenerator implements AdminHelpGeneratorInterface
{
    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        $class = class_entity('NavigatorMethods');
        $class->namespace("Admin\Core");

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
                'self|static|'.($ret ?: '\\'.Navigate::class),
                $method->getName().'('.refl_params_entity($method->getParameters()).')',
                pars_description_from_doc($method->getDocComment())
            );
        }

        foreach (AdminFacade::extensions() as $extension) {
            if ($extension::$slug !== 'application') {
                $doc->tagMethod(
                    'self|static|'.'\\'.Navigate::class,
                    $extension::$slug.'()',
                    'Extension menu'
                );
            }
        }
    }
}
