<?php

namespace Admin\Commands\Generators;

use Illuminate\Console\Command;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use Admin;
use Admin\Interfaces\AdminHelpGeneratorInterface;
use ReflectionException;

class ExtensionNavigatorHelperGenerator implements AdminHelpGeneratorInterface
{
    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        $class = class_entity('NavigatorExtensions');
        $class->namespace("Admin\Core");

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
        foreach (Admin::extensions() as $name => $provider) {
            $doc->tagMethod('void', $provider::$slug, "Make extension routes ($name})");
        }
    }
}
