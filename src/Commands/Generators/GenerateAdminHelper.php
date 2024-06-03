<?php

declare(strict_types=1);

namespace Admin\Commands\Generators;

use Admin\Components\Component;
use Admin\Interfaces\AdminHelpGeneratorInterface;
use Admin\Page;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use Illuminate\Console\Command;
use ReflectionClass;

/**
 * The class that is responsible for the methods of components that are imported using the magic method.
 */
class GenerateAdminHelper implements AdminHelpGeneratorInterface
{
    /**
     * Output helper generation function.
     *
     * @param  Command  $command
     * @return string
     */
    public function handle(Command $command): string
    {
        $namespace = namespace_entity("Admin\\Components");

        $class = $namespace->class('Components')
            ->abstractClass();
        $class2 = $namespace->class('PageComponents')
            ->abstractClass();

        $class->extend('\\'.Component::class);

        $class->doc(function ($doc) {
            $this->generateComponents($doc);
        });
        $class2->doc(function ($doc) {
            $this->generateComponentsInPage($doc);
        });

        return $namespace->render();
    }

    /**
     * Generate helpers methods for components.
     *
     * @param  DocumentorEntity  $doc
     */
    protected function generateComponents(DocumentorEntity $doc): void
    {
        collect(Component::$components)->map(function ($item, $key) use ($doc) {
            if (!class_exists($item)) {
                return $item;
            }

            $ref = new ReflectionClass($item);

            $params = '';

            if ($ref->hasMethod('__construct')) {
                $params = refl_params_entity($ref->getMethod('__construct')->getParameters(), false, false);
            }

            $doc->tagMethod(
                '\\'.$item,
                $key.'('.$params.')',
                "Add Component {$key}"
            );
        });
    }

    /**
     * Generate helper methods for components in Page class.
     *
     * @param  DocumentorEntity  $doc
     */
    protected function generateComponentsInPage(DocumentorEntity $doc): void
    {
        collect(Component::$components)->map(function ($item, $key) use ($doc) {
            if (!class_exists($item)) {
                return $item;
            }

            $ref = new ReflectionClass($item);

            $params = '';

            if ($ref->hasMethod('__construct')) {
                $params = refl_params_entity($ref->getMethod('__construct')->getParameters(), false, false);
            }

            $doc->tagMethod(
                '\\'.Page::class,
                $key.'('.$params.')',
                "Add Component {$key}"
            );
        });
    }
}
