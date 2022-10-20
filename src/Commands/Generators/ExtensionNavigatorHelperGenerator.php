<?php

namespace LteAdmin\Commands\Generators;

use Illuminate\Console\Command;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use LteAdmin;
use LteAdmin\Interfaces\LteHelpGeneratorInterface;
use LteAdmin\Navigate;
use ReflectionClass;
use ReflectionException;

class ExtensionNavigatorHelperGenerator implements LteHelpGeneratorInterface
{
    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        $class = class_entity('NavigatorExtensions');
        $class->namespace("LteAdmin\Core");

        $class->doc(function ($doc) {
            /** @var DocumentorEntity $doc */
            $this->generateDefaultMethods($doc);
        });

        return $class->render();
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
}
