<?php

namespace Lar\LteAdmin\Core\Generators;

use Illuminate\Console\Command;
use Lar\Developer\Commands\Dump\DumpExecute;
use Lar\EntityCarrier\Core\Entities\ClassEntity;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Segments\Tagable\Field;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\SearchForm;

/**
 * Class FormGroupHelperGenerator
 * @package Lar\LteAdmin\Core\Generators
 */
class SearchFormHelperGenerator implements DumpExecute {

    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        $namespace = namespace_entity("Lar\LteAdmin\Core");

        $namespace->class("FormSearchComponents", function (ClassEntity $class) {

            $class->doc(function (DocumentorEntity $doc) {

                $this->generateDefaultMethods($doc);
            });
        });

        return $namespace->render();
    }

    /**
     * Generate default methods
     *
     * @param DocumentorEntity $doc
     * @throws \ReflectionException
     */
    protected function generateDefaultMethods(DocumentorEntity $doc)
    {
        foreach (SearchForm::$field_components as $name => $provider) {

            if (property_exists($provider, 'condition')) {
                $condition = $provider::$condition;
            } else {
                $condition = '=%';
            }

            $doc->tagMethod('\\'.$provider, $name."(string \$name, string \$label, \$condition = '{$condition}')", "Make search field ($name})");
        }

        foreach (SearchForm::get_macro_names() as $macro_name) {

            $ref = SearchForm::get_macro_reflex($macro_name);

            $doc->tagMethod(

                "\\".SearchForm::class,
                $macro_name . "(".refl_params_entity($ref->getParameters()).")",
                "SearchForm Macro $macro_name"
            );
        }
    }
}