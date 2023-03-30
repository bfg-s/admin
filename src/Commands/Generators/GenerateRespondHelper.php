<?php

namespace Admin\Commands\Generators;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Bfg\Entity\Core\Entities\ClassEntity;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use Bfg\Entity\Core\Entities\ParamEntity;
use Lar\Layout\Respond;
use Lar\Tagable\Core\HTML5Library;
use Admin\Interfaces\AdminHelpGeneratorInterface;
use ReflectionClass;
use ReflectionException;

class GenerateRespondHelper implements AdminHelpGeneratorInterface
{
    /**
     * @var array
     */
    protected static $on_generate_doc = [];

    /**
     * @param  Closure|array  $call
     */
    public static function onGenerateDoc($call)
    {
        if (is_embedded_call($call)) {
            static::$on_generate_doc[] = $call;
        }
    }

    /**
     * Handle call method.
     *
     * @param  Command  $command
     * @return mixed
     */
    public function handle(Command $command)
    {
        $namespace = namespace_entity("Lar\Layout");

        $namespace->class('RespondDoc', function ($class) {
            /** @var ClassEntity $class */
            foreach (static::$on_generate_doc as $global) {
                call_user_func($global, $class);
            }

            $this->generateClassDoc($class);
        });

        return $namespace->render();
    }

    /**
     * Class generator.
     *
     * @param  ClassEntity  $class
     */
    protected function generateClassDoc($class)
    {
        HTML5Library::init();

        $class->doc(function ($doc) {
            /** @var DocumentorEntity $doc */
            $this->generateDefaultMethods($doc);
            $this->generateMacro($doc);
        });
    }

    /**
     * Generate default methods.
     *
     * @param  DocumentorEntity  $doc
     * @throws ReflectionException
     */
    protected function generateDefaultMethods($doc)
    {
        $ref_class = new ReflectionClass(Respond::class);

        $ref_collect = new ReflectionClass(Collection::class);

        foreach ($ref_class->getMethods() as $method) {
            if (!$ref_collect->hasMethod($method->name) && !$method->isPrivate() && !$method->isConstructor() && $method->name != '__call' && $method->name != '__callStatic' && $method->name != 'offsetExists' && $method->name != 'offsetGet' && $method->name != 'offsetSet' && $method->name != 'offsetUnset' && $method->name != 'parent') {
                $name = preg_replace('/^(\_)/', '', $method->name);

                $rd = pars_return_from_doc($method->getDocComment());

                $params = trim(ParamEntity::buildFromReflection($method->getParameters()));

                $doc->tagMethod(

                    (!empty($rd) ? "{$rd}|" : '').'\\'.Respond::class.'|$this',

                    $name.'('.$params.')',

                    pars_description_from_doc($method->getDocComment())

                );
            }
        }
    }

    /**
     * @param  DocumentorEntity  $doc
     */
    protected function generateMacro($doc)
    {
        foreach (Respond::get_macro_names() as $macro_name) {
            $doc->tagMethod(

                '\\'.Respond::class.'|$this',

                $macro_name.'()',

                "Macro $macro_name"

            );
        }
    }
}
