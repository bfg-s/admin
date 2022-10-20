<?php

namespace LteAdmin\Commands\Generators;

use Closure;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Bfg\Entity\Core\Entities\ClassEntity;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Respond;
use Lar\Tagable\Core\HTML5Library;
use Lar\Tagable\Tag;
use LteAdmin\Interfaces\LteHelpGeneratorInterface;
use ReflectionClass;
use ReflectionException;

class GenerateLteHelper implements LteHelpGeneratorInterface
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
     * @return void
     */
    public function handle(Command $command)
    {
        $class = class_entity('LarDoc');
        $class->namespace("Lar\Layout");

        /** @var ClassEntity $class */
        $class->extend('\\Lar\\Tagable\\Tag');

        foreach (static::$on_generate_doc as $global) {
            call_user_func($global, $class);
        }

        $this->generateClassDoc($class);

        return $class->render();
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
            $this->generateEventAttributes($doc);
            $this->generateAttributes($doc);
//            $this->generateTags($doc);
            $this->generateComponents($doc);
            $this->generateDefaultMethods($doc);
        });
    }

    /**
     * Generate event attribute setter.
     *
     * @param  DocumentorEntity  $doc
     */
    protected function generateEventAttributes($doc)
    {
        HTML5Library::$events->map(function ($item, $key) use ($doc) {
            $name_key = Str::camel('on_'.$key);

            $doc->tagMethod(
                'self|static|\\'.Component::class,
                $name_key.'($data)',
                "Set {$key} Event"
            );

            $doc->tagMethod(
                'self|static|\\'.Component::class,
                '_'.$name_key.'($data)',
                "Set parent {$key} Event"
            );

            $doc->tagMethod(
                'self|static|\\'.Component::class,
                $name_key.'If($eq, $data)',
                "Set {$key} Event if \$eq == true"
            );

            $doc->tagMethod(
                'self|static|\\'.Component::class,
                '_'.$name_key.'If($eq, $data)',
                "Set parent {$key} Event if \$eq == true"
            );
        });
    }

    /**
     * Generate attributes.
     *
     * @param  DocumentorEntity  $doc
     */
    protected function generateAttributes($doc)
    {
        HTML5Library::$attributes->map(function ($item, $key) use ($doc) {
            if ($key == 'data-*') {
                return $item;
            }

            $key = str_replace('-', '_', $key);

            $name_key = Str::camel('set_'.$key);
            $name_key2 = Str::camel('get_'.$key);

            $doc->tagMethod(
                'self|static|\\'.Component::class,
                $name_key."(string \${$key}_data = \"\")",
                "Set {$key} Attribute"
            );

            $doc->tagMethod(
                'self|static|\\'.Component::class,
                '_'.$name_key."(string \${$key}_data = \"\")",
                "Set parent {$key} Attribute"
            );

            $doc->tagMethod(
                'self|static|\\'.Component::class,
                $name_key."If(\$eq, string \${$key}_data = \"\")",
                "Set {$key} Attribute if \$eq == true"
            );

            $doc->tagMethod(
                'self|static|\\'.Component::class,
                '_'.$name_key."If(\$eq, string \${$key}_data = \"\")",
                "Set parent {$key} Attribute if \$eq == true"
            );

            $doc->tagMethod(
                'string',
                $name_key2.'()',
                "Get {$key} Attribute"
            );

            $doc->tagMethod(
                'string',
                '_'.$name_key2.'()',
                "Get parent {$key} Attribute"
            );
        });
    }

    /**
     * Generate components.
     *
     * @param  DocumentorEntity  $doc
     */
    protected function generateComponents($doc)
    {
        Tag::$components->map(function ($item, $key) use ($doc) {
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

            $doc->tagMethod(
                '\\'.$item,
                '_'.$key.'('.$params.')',
                "Add Component {$key} to parent"
            );
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
        $ref_class = new ReflectionClass(Tag::class);

        foreach ($ref_class->getMethods() as $method) {
            if (!$method->isPrivate() && !$method->isConstructor() && $method->name != '__call' && $method->name != '__callStatic' && $method->name != 'offsetExists' && $method->name != 'offsetGet' && $method->name != 'offsetSet' && $method->name != 'offsetUnset') {
                $name = preg_replace('/^(\_)/', '', $method->name);

                $return = pars_return_from_doc($method->getDocComment());
                if ($return == 'Tag' || $return == 'static' || $return == 'self') {
                    $return = '';
                }

                $doc->tagMethod(
                    'self|static|\\'.Component::class.($name === 'ljs' || $name === 'lj' ? '|\\'.Respond::class : '')
                    .($return ? "|$return" : ''),
                    $name.'('.refl_params_entity($method->getParameters()).')',
                    pars_description_from_doc($method->getDocComment())
                );

                $doc->tagMethod(
                    'self|static|\\'.Component::class.($name === 'ljs' || $name === 'lj' ? '|\\'.Respond::class : '')
                    .($return ? "|$return" : ''),
                    '_'.$name.'('.refl_params_entity($method->getParameters()).')',
                    'Apply to parent. '.pars_description_from_doc($method->getDocComment())
                );
            }
        }
    }

    /**
     * Generate tags.
     *
     * @param  DocumentorEntity  $doc
     */
    protected function generateTags($doc)
    {
        HTML5Library::$tags->map(function ($item, $key) use ($doc) {
            $class = Component::getClassNameByTag($key);

            if (class_exists($class) && !isset(Tag::$components[$key])) {
                $doc->tagMethod(
                    '\\'.$class,
                    $key.'(...$when)',
                    "Add tag {$key}"
                );

                $doc->tagMethod(
                    '\\'.$class,
                    '_'.$key.'(...$when)',
                    "Add tag {$key} ot parent"
                );
            }
        });
    }
}
