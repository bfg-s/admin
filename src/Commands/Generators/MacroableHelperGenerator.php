<?php

declare(strict_types=1);

namespace Admin\Commands\Generators;

use Admin;
use Admin\Controllers\Controller;
use Admin\Core\Delegate;
use Admin\Delegates\ModelCards;
use Admin\Interfaces\AdminHelpGeneratorInterface;
use Admin\Page;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use Closure;
use File;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Log;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;
use ReflectionException;

/**
 * The class is responsible for macro helpers, additional components and custom components and inputs.
 */
class MacroableHelperGenerator implements AdminHelpGeneratorInterface
{
    /**
     * Directories where the search for classes with a macro trait and a signature for a property with methods will be performed.
     *
     * @var string[]
     */
    public static array $dirs = [
        __DIR__.'/../../Components',
    ];

    /**
     * All existing model fields.
     *
     * @var array
     */
    public static array $fields = [];

    /**
     * All available relationship types for models.
     *
     * @var string[]
     */
    protected array $types = [
        'hasMany', 'hasManyThrough', 'hasOneThrough', 'belongsToMany', 'hasOne',
        'belongsTo', 'morphOne', 'morphTo', 'morphMany', 'morphToMany', 'morphedByMany',
    ];

    /**
     * All available model relationships.
     *
     * @var array
     */
    protected Collection|array $relations = [];

    /**
     * The content of the current model is divided into an array by rows.
     *
     * @var array
     */
    protected array $model_lines = [];

    /**
     * Current command class instance.
     *
     * @var Command
     */
    private Command $command;

    /**
     * Output helper generation function.
     *
     * @param  Command  $command
     * @return mixed
     * @throws ReflectionException
     */
    public function handle(Command $command): mixed
    {
        $this->command = $command;

        $dirs = array_merge([app_path()], static::$dirs);

        foreach (array_keys(Admin::extensionProviders()) as $provider) {
            $dirs[] = dirname((new ReflectionClass($provider))->getFileName());
        }

        $classes = [];

        foreach ($dirs as $dir) {
            $files = collect(File::allFiles($dir))
                ->map(static function (SplFileInfo $file) {
                    return $file->getPathname();
                })
                ->filter(static function (string $file) {
                    return Str::is('*.php', $file) && is_file($file);
                })
                ->map('class_in_file')
                ->merge([Page::class, Controller::class])
                ->filter(static function ($class) {
                    try {
                        return class_exists($class);
                    } catch (Throwable) {
                    }

                    return false;
                });

            $classes = array_merge($classes, $files->toArray());
        }

        $macroable_classes = [];

        foreach ($classes as $class) {
            try {
                $reflection = new ReflectionClass($class);

                if ($reflection->isInterface()) {
                    continue;
                }
            } catch (Throwable $exception) {
                $command->error($exception->getMessage());
                Log::error($exception);
                continue;
            }

            $coreTrait = 'Admin\\Core\\Traits\\Macroable';

            $traits = array_keys($reflection->getTraits());

            $ns = body_namespace_element($class);
            $doc = $reflection->getDocComment();

            if (count($traits) && in_array($coreTrait, $traits)) {
                $macroable_classes[$ns][] = [
                    'type' => 'macro',
                    'class' => $class,
                    'name' => class_basename($class),
                    'doc' => $doc,
                    'ref' => $reflection,
                ];
            }

            if ($doc !== false) {
                foreach ($this->getDocVariables($doc, 'methods') as $method) {
                    $macroable_classes[$ns][] = [
                        'type' => 'methods',
                        'class' => $class,
                        'name' => class_basename($class),
                        'doc' => $doc,
                        'methods' => $method,
                        'ref' => $reflection,
                    ];
                }
            }
        }

        $isset_classes = [];

        foreach ($macroable_classes as $namespace_name => $data) {

            foreach ($data as $class) {
                $type = $class['type'];

                if ($type === 'macro') {
                    $class['macro_return'] = '\\'.trim($class['class'], '\\');
                    if ($mr = get_doc_var($class['doc'], 'macro_return')) {
                        $class['macro_return'] .= '|\\'.trim($mr);
                    }

                    $name = get_doc_var($class['doc'], 'helper_name');
                    if (!$name) {
                        $name = "{$class['name']}MacroList";
                    } else {
                        $name = "{$name}MacroList";
                    }

                    if (!isset($isset_classes[$namespace_name][$name])) {
                        $isset_classes[$namespace_name][$name] = $name;
                    }
                } elseif ($type === 'methods') {
                    $name = get_doc_var($class['doc'], 'helper_name');
                    if (!$name) {
                        $name = "{$class['name']}Methods";
                    } else {
                        $name = "{$name}Methods";
                    }
                    if (!strpos($class['methods'], '::')) {
                        $class['methods'] = $class['class'].'::'.$class['methods'];
                    }
                    $class['methods'] = str_replace(['static', 'self'], $class['class'], $class['methods']);
                    if (!preg_match(
                        '/((.*)\:\:([\$a-zA-Z0-9\_]+))\s?(\(.*\))?\s?(.*)/m',
                        $class['methods'],
                        $class['methods']
                    )) {
                        continue;
                    }
                    $class['methods'][3] = trim($class['methods'][3], '$');
                    if (!isset($class['methods'][4]) || !$class['methods'][4]) {
                        $class['methods'][4] = false;
                    }
                    if (!class_exists($class['methods'][2]) || !property_exists(
                            $class['methods'][2],
                            $class['methods'][3]
                        )) {
                        continue;
                    } elseif (method_exists($class['methods'][2], 'getHelpMethodList')) {
                        $class['methods']['data'] = call_user_func([$class['methods'][2], 'getHelpMethodList']);
                    } else {
                        $c = $class['methods'][2];
                        $p = $class['methods'][3];
                        $refProp = new ReflectionProperty($c, $p);
                        $refProp->setAccessible(true);
                        $class['methods']['data'] = $refProp->getValue();
                    }

                    if (!isset($isset_classes[$namespace_name][$name])) {
                        $class_obj = class_entity($name);
                        $class_obj->namespace($namespace_name);

                        $class_obj->doc(function ($doc) use ($class) {
                            /** @var DocumentorEntity $doc */
                            $this->extendMethods($doc, $class);
                        });

                        $nameClass = Str::snake($name);
                        $file = base_path("vendor/_laravel_idea/_ide_helper_{$nameClass}.php");
                        file_put_contents($file, "<?php \n\n".$class_obj->render());

                        $isset_classes[$namespace_name][$name] = $name;
                    }
                }
            }
        }

        $this->createSearchAndColAndRowFields();

        return null;
    }

    /**
     * Get all variables of the specified dock block.
     *
     * @param  string  $doc
     * @param  string  $var_name
     * @return array
     */
    public static function getDocVariables(string $doc, string $var_name): array
    {
        $result = [];

        foreach (explode("\n", $doc) as $line) {
            if (preg_match('/@'.$var_name.'\s(.*)/m', $line, $matches)) {
                $result[] = isset($matches[1]) ? trim($matches[1]) : null;
            }
        }

        return $result;
    }

    /**
     * Generate extension methods and properties of components, model fields and their connections.
     *
     * @param  \Bfg\Entity\Core\Entities\DocumentorEntity  $doc
     * @param  array  $class_data
     * @return void
     * @throws ReflectionException
     */
    protected function extendMethods(DocumentorEntity $doc, array $class_data): void
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
                if ($method_class instanceof Closure) {
                    $ref = new ReflectionFunction($method_class);
                } else {
                    $ref = new ReflectionClass($method_class);
                }
            } catch (Throwable $e) {
                $this->command->error($e->getMessage());
                Log::error($e);
            }
            $params = $m[4];
            if ($method_class instanceof Closure) {
                if (!$params) {
                    $params = '('.refl_params_entity($ref->getParameters()).')';
                }
            } else {
                if (!$params && $ref->hasMethod('__construct')) {
                    $params = '('.refl_params_entity($ref->getMethod('__construct')->getParameters()).')';
                }
            }
            $isProperty = $params === '(likeProperty)';
            $isAny = $params === '(likeAny)';

            if ($ref->hasMethod('__construct')) {
                $params = preg_replace(
                    "/\*\s*\)$/",
                    refl_params_entity($ref->getMethod('__construct')->getParameters()).')',
                    $params
                );
                $params = preg_replace(
                    "/^\(\s*\*/",
                    refl_params_entity($ref->getMethod('__construct')->getParameters()).')',
                    $params
                );
            }

            $upd = function ($m) use ($class_data, $method_class) {
                $cond = trim($m[1]);
                $cond = array_map('trim', explode('||', $cond));
                $var = trim($cond[0], '$');
                $default = $cond[1] ?? '';
                $c = $class_data['class'];
                if (property_exists($method_class, $var)) {
                    $condition = $method_class::$$var;
                } elseif (property_exists($c, $var)) {
                    $condition = $c::$$var;
                } else {
                    $condition = $default;
                }

                return $condition;
            };

            $type = '\\'.trim($m[5] ?: $method_class);

            $type = preg_replace_callback('/\{\{(.*)\}\}/', static function ($m) use ($upd) {
                return $upd($m);
            }, $type);

            $method = preg_replace_callback('/\{\{(.*)\}\}/', static function ($m) use ($upd) {
                return $upd($m);
            }, $method);

            $params = preg_replace_callback('/\{\{(.*)\}\}/', static function ($m) use ($upd) {
                return $upd($m);
            }, $params);

            if (!$isProperty || $isAny) {
                $doc->tagMethod(
                    $type,
                    $method.($params ?: "(string \$name, callable|string \$label = null)"),
                    "Method $method"
                );
            }

            if (
                $isProperty
                || $isAny
                || $type == '\Admin\Components\ModelTableComponent'
                || $type == '\Admin\Components\ModelCardsComponent'
                || $type == '\Admin\Components\ModelInfoTableComponent'
            ) {
                $doc->tagPropertyRead(
                    $type,
                    $method,
                    "Property $method"
                );
            }

            if (in_array($method, array_keys(Admin\Components\Component::$inputs))) {
                if (!in_array($method, ['info_id', 'info_created_at', 'info_updated_at'])) {
                    foreach ($this->getModelFields() as $field) {
                        $camelField = Str::snake($field);

                        if ($camelField) {
                            $doc->tagMethod(
                                $type,
                                $method.'_'.$camelField."(callable|string \$label = null)",
                                "Method {$method}_{$camelField}"
                            );
                            $doc->tagPropertyRead(
                                $type,
                                $method.'_'.$camelField,
                                "Property {$method}_{$camelField}"
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Get the fields of all models that are in the application.
     *
     * @return array
     * @throws ReflectionException
     */
    protected function getModelFields(): array
    {
        if (static::$fields) {
            return static::$fields;
        }

        $files = collect(File::allFiles(app_path()))
            ->map(static function (SplFileInfo $file) {
                return $file->getPathname();
            })
            ->filter(static function (string $file) {
                return Str::is('*.php', $file) && is_file($file);
            })
            ->map('class_in_file')
            ->filter(static function ($class) {
                try {
                    return class_exists($class) && method_exists($class, 'getFillable');
                } catch (Throwable $throwable) {
                }

                return false;
            });

        $fields = $files->map(function ($class) {
            $fillable = (new $class)->getFillable();
            $class = new ReflectionClass($class);
            $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                if (preg_match('/^get(.*)Attribute$/', $method->name, $m)) {
                    $fillable[] = Str::snake($m[1]);
                }
            }
            return $fillable;
        })->collapse()->unique()->toArray();

        $this->relations = $files->map(function ($class) {
            $ref = new ReflectionClass($class);
            $relations = $this->getAllRelations($ref);
            $result = [];
            /** @var Relation $relation */
            foreach ($relations as $n => $relation) {
                $fillable = $relation->getModel()->getFillable();
                $class = new ReflectionClass($relation->getModel());
                $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
                foreach ($methods as $method) {
                    if (preg_match('/^get(.*)Attribute$/', $method->name, $m)) {
                        $fillable[] = Str::snake($m[1]);
                    }
                }

                $result[] = [
                    'name' => $n,
                    'fillable' => $fillable,
                ];
            }
            return $result;
        })->filter()->collapse();

        return static::$fields = $fields;
    }

    /**
     * Get the relationships of all models that are in the application.
     *
     * @param  \ReflectionClass  $ref
     * @return array
     */
    protected function getAllRelations(ReflectionClass $ref): array
    {
        $this->model_lines = explode("\n", file_get_contents($ref->getFileName()));
        $traits = $ref->getTraits();
        $traitMethodNames = [];
        foreach ($traits as $trait) {
            $traitMethods = $trait->getMethods();
            foreach ($traitMethods as $traitMethod) {
                $traitMethodNames[] = $traitMethod->getName();
            }
        }

        $currentMethod = collect(explode('::', __METHOD__))->last();
        $methods = $ref->getMethods(ReflectionMethod::IS_PUBLIC);

        $model_class_name = $ref->getName();

        return collect($methods)->filter(function ($method) use ($model_class_name, $traitMethodNames, $currentMethod) {
            $methodName = $method->getName();
            if (!in_array($methodName, $traitMethodNames)
                && !str_starts_with($methodName, '__')
                && $method->class === $model_class_name
                && !$method->isStatic()
                && $methodName != $currentMethod
            ) {
                $r = new ReflectionMethod($model_class_name, $methodName);
                $parameters = $r->getParameters();
                return collect($parameters)->filter(function ($parameter) {
                    return !$parameter->isOptional();
                })->isEmpty();
            }
            return false;
        })->mapWithKeys(function (ReflectionMethod $method) use ($ref) {
            $methodName = $method->getName();
            $model_content = $this->getModelDataByLines($method->getStartLine(), $method->getEndLine());
            if (
                preg_match('/return \$this->('.implode('|', $this->types).')\s*\(.*\)\s*;/', $model_content)
            ) {
                $relation = $ref->newInstance()->$methodName();
                if (is_subclass_of($relation, Relation::class)) {
                    return [$methodName => $relation];
                }
            }
            return [];
        })->toArray();
    }

    /**
     * Get text from the model at the beginning of the line and at the end of the line.
     *
     * @param  int  $start
     * @param  int  $end
     * @return string
     */
    protected function getModelDataByLines(int $start, int $end): string
    {
        return implode("\n", array_slice($this->model_lines, $start - 1, ($end - $start) + 1));
    }

    /**
     * Creates model helper properties and methods for searches and tables.
     *
     * @return void
     */
    public function createSearchAndColAndRowFields(): void
    {
        /** ModelTable helpers start */
        $class = class_entity("ModelTableComponentFields");
        $class->namespace('Admin\Components');

        $class->doc(function ($doc) {
            $method = 'col';
            $methods = [];
            $modelTableType = "\\".Admin\Components\ModelTableComponent::class."|\\".ModelTable::class."|\\".Delegate::class;
            /** @var DocumentorEntity $doc */
            foreach ($this->getModelFields() as $field) {
                $camelField = Str::snake($field);
                if ($camelField) {
                    $doc->tagMethod(
                        $modelTableType,
                        $method.'_'.$camelField."(callable|string \$label = null)",
                        "Method {$method}_{$camelField}"
                    );
                    $doc->tagPropertyRead(
                        $modelTableType,
                        $method.'_'.$camelField,
                        "Property {$method}_{$camelField}"
                    );
                    $doc->tagPropertyRead(
                        $modelTableType,
                        'sort_in_'.$camelField,
                        "Property sort_in_{$camelField}"
                    );
                }
            }

            foreach ($this->relations as $relation) {
                foreach ($this->getModelFields() as $field) {
                    if ($field) {
                        $camelField = Str::snake($field);
                        $m = $method.'_'.$relation['name'].'__'.$camelField;
                        if ($camelField && !in_array($m, $methods)) {
                            $doc->tagMethod(
                                $modelTableType,
                                $m."(callable|string \$label = null)",
                                "Method {$m}"
                            );
                            $doc->tagPropertyRead(
                                $modelTableType,
                                $m,
                                "Property {$m}"
                            );
                            $methods[] = $m;
                        }
                    }
                }
            }
        });

        $nameClass = Str::snake('ModelTableComponentFields');
        $file = base_path("vendor/_laravel_idea/_ide_helper_{$nameClass}.php");
        file_put_contents($file, "<?php \n\n".$class->render());
        /** ModelTable helpers end */

        /** ModelInfoTable helpers start */
        $class = class_entity('ModelInfoTableComponentFields');
        $class->namespace('Admin\Components');

        $class->doc(function ($doc) {
            $method = 'row';
            $methods = [];
            $modelInfoType = "\\".Admin\Components\ModelInfoTableComponent::class."|\\".ModelInfoTable::class."|\\".Delegate::class;
            /** @var DocumentorEntity $doc */
            foreach ($this->getModelFields() as $field) {
                $camelField = Str::snake($field);
                if ($camelField) {
                    $methods[] = $method.'_'.$camelField;

                    $doc->tagMethod(
                        $modelInfoType,
                        $method.'_'.$camelField."(callable|string \$label = null)",
                        "Method {$method}_{$camelField}"
                    );
                    $doc->tagPropertyRead(
                        $modelInfoType,
                        $method.'_'.$camelField,
                        "Property {$method}_{$camelField}"
                    );
                }
            }

            foreach ($this->relations as $relation) {
                foreach ($this->getModelFields() as $field) {
                    if ($field) {
                        $camelField = Str::snake($field);
                        $m = $method.'_'.$relation['name'].'__'.$camelField;
                        if ($camelField && !in_array($m, $methods)) {
                            $doc->tagMethod(
                                $modelInfoType,
                                $m."(callable|string \$label = null)",
                                "Method {$m}"
                            );
                            $doc->tagPropertyRead(
                                $modelInfoType,
                                $method.'_'.$relation['name'].'__'.$camelField,
                                "Property {$method}_{$relation['name']}__{$camelField}"
                            );
                            $methods[] = $m;
                        }
                    }
                }
            }
        });

        $nameClass = Str::snake('ModelInfoTableComponentFields');
        $file = base_path("vendor/_laravel_idea/_ide_helper_{$nameClass}.php");
        file_put_contents($file, "<?php \n\n".$class->render());
        /** ModelInfoTable helpers end */

        /** ModelCards helpers start */
        $class = class_entity('ModelCardsComponentFields');
        $class->namespace('Admin\Components');

        $class->doc(function ($doc) {
            $method = 'row';
            $methods = [];
            $modelInfoType = "\\".Admin\Components\ModelCardsComponent::class."|\\".ModelCards::class."|\\".Delegate::class;
            /** @var DocumentorEntity $doc */
            foreach ($this->getModelFields() as $field) {
                $camelField = Str::snake($field);
                if ($camelField) {
                    $methods[] = $method.'_'.$camelField;

                    $doc->tagMethod(
                        $modelInfoType,
                        $method.'_'.$camelField."(callable|string \$label = null)",
                        "Method {$method}_{$camelField}"
                    );
                    $doc->tagPropertyRead(
                        $modelInfoType,
                        $method.'_'.$camelField,
                        "Property {$method}_{$camelField}"
                    );
                }
            }

            foreach ($this->relations as $relation) {
                foreach ($this->getModelFields() as $field) {
                    if ($field) {
                        $camelField = Str::snake($field);
                        $m = $method.'_'.$relation['name'].'__'.$camelField;
                        if ($camelField && !in_array($m, $methods)) {
                            $doc->tagMethod(
                                $modelInfoType,
                                $m."(callable|string \$label = null)",
                                "Method {$m}"
                            );
                            $doc->tagPropertyRead(
                                $modelInfoType,
                                $method.'_'.$relation['name'].'__'.$camelField,
                                "Property {$method}_{$relation['name']}__{$camelField}"
                            );
                            $methods[] = $m;
                        }
                    }
                }
            }
        });

        $nameClass = Str::snake('ModelCardsComponentFields');
        $file = base_path("vendor/_laravel_idea/_ide_helper_{$nameClass}.php");
        file_put_contents($file, "<?php \n\n".$class->render());
        /** ModelCards helpers end */

        $class = class_entity('SearchFormComponentFields');
        $class->namespace('Admin\Components');

        $class->doc(function ($doc) {
            foreach (Admin\Components\SearchFormComponent::$field_components as $input => $class) {
                /** @var DocumentorEntity $doc */
                foreach ($this->getModelFields() as $field) {
                    $camelField = Str::snake($field);

                    $method = 'in';
                    $searchFormType = "\\".$class."|\\".Admin\Components\SearchFormComponent::class."|\\".SearchForm::class."|\\".Delegate::class;
                    if ($camelField) {
                        $doc->tagMethod(
                            $searchFormType,
                            $method.'_'.$input.'_'.$camelField."(callable|string \$label = null, callable|string \$condition = null)",
                            "Method {$method}_{$input}_{$camelField}"
                        );
                        $doc->tagPropertyRead(
                            $searchFormType,
                            $method.'_'.$input.'_'.$camelField,
                            "Property {$method}_{$input}_{$camelField}"
                        );
                    }
                }
            }
        });

        $nameClass = Str::snake('SearchFormComponentFields');
        $file = base_path("vendor/_laravel_idea/_ide_helper_{$nameClass}.php");
        file_put_contents($file, "<?php \n\n".$class->render());
    }
}
