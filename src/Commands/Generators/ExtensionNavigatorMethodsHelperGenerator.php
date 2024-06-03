<?php

declare(strict_types=1);

namespace Admin\Commands\Generators;

use Admin\Facades\Admin;
use Admin\Interfaces\AdminHelpGeneratorInterface;
use Admin\NavigateEngine;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use Illuminate\Console\Command;
use ReflectionClass;

/**
 * The class which is responsible for duplicating all navigation methods.
 */
class ExtensionNavigatorMethodsHelperGenerator implements AdminHelpGeneratorInterface
{
    /**
     * Output helper generation function.
     *
     * @param  Command  $command
     * @return string
     */
    public function handle(Command $command): string
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
     * Generate helper all methods.
     *
     * @param  DocumentorEntity  $doc
     */
    protected function generateAllMethods(DocumentorEntity $doc): void
    {
        $methods = [];

        $nav = new ReflectionClass(NavigateEngine::class);

        foreach ($nav->getMethods() as $method) {
            $methods[$method->getName()] = $method;
        }
        foreach ($methods as $method) {
            $ret = pars_return_from_doc($method->getDocComment());

            $doc->tagMethod(
                'self|static|'.($ret ?: '\\'.NavigateEngine::class),
                $method->getName().'('.refl_params_entity($method->getParameters()).')',
                pars_description_from_doc($method->getDocComment())
            );
        }

        foreach (Admin::extensions() as $extension) {
            if ($extension::$slug !== 'application') {
                $doc->tagMethod(
                    'self|static|'.'\\'.NavigateEngine::class,
                    $extension::$slug.'()',
                    'Extension menu'
                );
            }
        }
    }
}
