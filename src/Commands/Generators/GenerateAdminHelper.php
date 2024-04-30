<?php

declare(strict_types=1);

namespace Admin\Commands\Generators;

use Admin\Components\Component;
use Admin\Interfaces\AdminHelpGeneratorInterface;
use Admin\Page;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use Illuminate\Console\Command;
use ReflectionClass;

class GenerateAdminHelper implements AdminHelpGeneratorInterface
{
    /**
     * Handle call method.
     *
     * @param  Command  $command
     * @return string
     */
    public function handle(Command $command)
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
            $this->generateComponents2($doc);
        });

        return $namespace->render();
    }

    /**
     * Generate components.
     *
     * @param  DocumentorEntity  $doc
     */
    protected function generateComponents($doc)
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
     * Generate components.
     *
     * @param  DocumentorEntity  $doc
     */
    protected function generateComponents2($doc)
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
