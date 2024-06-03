<?php

declare(strict_types=1);

namespace Admin\Commands\Generators;

use Admin;
use Admin\Interfaces\AdminHelpGeneratorInterface;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use Illuminate\Console\Command;

/**
 * The class is responsible for generating IDE extension helpers for navigation.
 */
class ExtensionNavigatorHelperGenerator implements AdminHelpGeneratorInterface
{
    /**
     * Output helper generation function.
     *
     * @param  Command  $command
     * @return string
     */
    public function handle(Command $command): string
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
     */
    protected function generateDefaultMethods(DocumentorEntity $doc): void
    {
        foreach (Admin::extensions() as $name => $provider) {
            $doc->tagMethod('void', $provider::$slug, "Make extension routes ($name})");
        }
    }
}
