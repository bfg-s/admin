<?php

namespace Lar\LteAdmin\Core\Generators;

use Illuminate\Console\Command;
use Lar\Developer\Commands\Dump\DumpExecute;
use Lar\EntityCarrier\Core\Entities\ClassEntity;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\LteBoot;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class MacroableHelperGenerator
 * @package Lar\LteAdmin\Core\Generators
 */
class MacroableHelperGenerator implements DumpExecute {

    /**
     * @var string[]
     */
    static $dirs = [
        __DIR__ . '/../../Segments'
    ];

    /**
     * @var Command
     */
    private $command;

    /**
     * @param  Command  $command
     * @return mixed|string
     */
    public function handle(Command $command)
    {
        $this->command = $command;

        $dirs = array_merge([app_path()], static::$dirs);

        foreach (array_keys(\LteAdmin::extensionProviders()) as $provider) {

            $dirs[] = dirname((new \ReflectionClass($provider))->getFileName());
        }

        $classes = [];
        
        foreach ($dirs as $dir) {

            $files = collect(\File::allFiles($dir))
                ->map(function (SplFileInfo $file) { return $file->getPathname(); })
                ->filter(function (string $file) { return \Str::is('*.php', $file) && is_file($file); })
                ->map(function (string $file) {
                    $cf = class_in_file($file);
                    return $cf['class'] ? trim($cf['namespace'] . "\\" . $cf['class'], '\\') : false;
                })
                ->filter(function (string $class) { return $class && class_exists($class); });

            $classes = array_merge($classes, $files->toArray());
        }

        $macroable_classes = [];

        foreach ($classes as $class) {
            try {
                $refl = new \ReflectionClass($class);

                if ($refl->isInterface()) {
                    continue ;
                }

            } catch (\Exception $exception) {
                $command->error($exception->getMessage());
                continue ;
            }

            $coreTrait = "Lar\\LteAdmin\\Core\\Traits\\Macroable";

            $traits = array_keys($refl->getTraits());

            $ns = body_namespace_element($class);
            $doc = $refl->getDocComment();

            if (count($traits) && in_array($coreTrait, $traits)) {

                $macroable_classes[$ns][] = [
                    'type' => 'macro',
                    'class' => $class,
                    'name' => class_basename($class),
                    'doc' => $doc,
                    'ref' => $refl
                ];
            }

            if ($methods = get_doc_var($doc, 'methods')) {

                $macroable_classes[$ns][] = [
                    'type' => 'methods',
                    'class' => $class,
                    'name' => class_basename($class),
                    'doc' => $doc,
                    'methods' => $methods,
                    'ref' => $refl
                ];
            }
        }

        $r_ns = "";

        $isset_classes = [];

        foreach ($macroable_classes as $namespace_name => $data) {

            $namespace = namespace_entity($namespace_name);

            foreach ($data as $class) {

                $type = $class['type'];

                if ($type === 'macro') {
                    $class['macro_return'] = "self|static|\\" . trim($class['class'], "\\");
                    if ($mr = get_doc_var($class['doc'], 'macro_return')) $class['macro_return'] .= "|\\" . trim($mr);

                    $name = get_doc_var($class['doc'], 'helper_name');
                    if (!$name) { $name = "{$class['name']}MacroList"; }
                    else  { $name = "{$name}MacroList"; }

                    if (!isset($isset_classes[$namespace_name][$name])) {

                        $namespace->class($name, function (ClassEntity $class_obj) use ($class) {

                            $class_obj->doc(function (DocumentorEntity $doc) use ($class) {

                                $this->macroMethods($doc, $class);
                            });
                        });

                        $isset_classes[$namespace_name][$name] = $name;
                    }
                }

                else if ($type === 'methods') {

                    $name = get_doc_var($class['doc'], 'helper_name');
                    if (!$name) { $name = "{$class['name']}Methods"; }
                    else  { $name = "{$name}Methods"; }
                    if (!strpos($class['methods'], '::')) { $class['methods'] = $class['class'].'::'.$class['methods']; }
                    $class['methods'] = str_replace(['static', 'self'], $class['class'], $class['methods']);
                    if(!preg_match('/((.*)\:\:([\$a-zA-Z0-9\_]+))\s?(\(.*\))?\s?(.*)/m', $class['methods'], $class['methods'])) {
                        continue ;
                    }
                    $class['methods'][3] = trim($class['methods'][3], '$');
                    if (!isset($class['methods'][4]) || !$class['methods'][4]) {
                        $class['methods'][4] = false;
                    }
                    if (!class_exists($class['methods'][2]) || !property_exists($class['methods'][2], $class['methods'][3])) {
                        continue ;
                    } else {
                        $c = $class['methods'][2];
                        $p = $class['methods'][3];
                        $class['methods']['data'] = $c::$$p;
                    }

                    if (!isset($isset_classes[$namespace_name][$name])) {

                        $namespace->class($name, function (ClassEntity $class_obj) use ($class) {

                            $class_obj->doc(function (DocumentorEntity $doc) use ($class) {

                                $this->extendMethods($doc, $class);
                            });
                        });

                        $isset_classes[$namespace_name][$name] = $name;
                    }
                }

            }
            $r_ns .= $namespace->render();
        }

        return $r_ns;
    }

    /**
     * @param  DocumentorEntity  $doc
     * @param  array  $class_data
     */
    protected function extendMethods(DocumentorEntity $doc, array $class_data)
    {
        $m = $class_data['methods'];
        foreach ($m['data'] as $method => $method_class) {

            if (is_array($method_class) && isset($method_class[0])) {

                $method_class = is_object($method_class[0]) ? get_class($method_class[0]) : $method_class[0];
            }

            if (is_array($method_class)) {

                continue;
            }

            try {
                if ($method_class instanceof \Closure) {
                    $ref = new \ReflectionFunction($method_class);
                } else {
                    $ref = new \ReflectionClass($method_class);
                }
            } catch (\Exception $e) {
                //dd($method, $method_class);
                $this->command->error($e->getMessage());
            }
            $params = $m[4];
            if ($method_class instanceof \Closure) {
                if (!$params) {
                    $params = "(".refl_params_entity($ref->getParameters()).")";
                }
            } else {
                if (!$params && $ref->hasMethod('__construct')) {
                    $params = "(".refl_params_entity($ref->getMethod('__construct')->getParameters()).")";
                }
            }

            $upd = function ($m) use ($class_data, $method_class) {
                $cond = trim($m[1]);
                $cond = array_map('trim', explode("||", $cond));
                $var = trim($cond[0], "$");
                $default = $cond[1] ?? '';
                $c = $class_data['class'];
                if (property_exists($method_class, $var)) {
                    $condition = $method_class::$$var;
                } else if (property_exists($c, $var)) {
                    $condition = $c::$$var;
                } else {
                    $condition = $default;
                }
                return $condition;
            };

            $type = "self|static|\\" . trim($m[5] ? $m[5] : $method_class) . "|\\" . $class_data['class'];

            $type = preg_replace_callback('/\{\{(.*)\}\}/', function ($m) use ($upd) {
                return $upd($m);
            }, $type);

            $method = preg_replace_callback('/\{\{(.*)\}\}/', function ($m) use ($upd) {
                return $upd($m);
            }, $method);

            $params = preg_replace_callback('/\{\{(.*)\}\}/', function ($m) use ($upd) {
                return $upd($m);
            }, $params);

            $doc->tagMethod(
                $type,
                $method . $params,
                "Method $method"
            );
        }
    }

    /**
     * Generate default methods
     *
     * @param  DocumentorEntity  $doc
     * @param  array  $class_data
     * @throws \ReflectionException
     */
    protected function macroMethods(DocumentorEntity $doc, array $class_data)
    {
        $class = $class_data['class'];
        /** @var Macroable $class */
        foreach ($class::get_macro_names() as $macro_name) {

            $ref = $class::get_macro_reflex($macro_name);

            $doc->tagMethod(
                $class_data['macro_return'],
                $macro_name . "(".refl_params_entity($ref->getParameters()).")",
                "Field Macro $macro_name"
            );
        }
    }
}