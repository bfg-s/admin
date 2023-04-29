<?php

namespace Admin\Commands\Generators;

use File;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Lar\LJS\JaxController;
use Admin\Interfaces\AdminHelpGeneratorInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Component\Finder\SplFileInfo;

class GenerateNewJaxHelper implements AdminHelpGeneratorInterface
{
    /**
     * @var array
     */
    protected $after_ts = [];

    /**
     * @param  Command  $command
     * @return mixed|void
     * @throws ReflectionException
     */
    public function handle(Command $command)
    {
        $map = $this->getMap();

        $s = str_repeat(' ', 8);
        $s4 = str_repeat(' ', 4);
        $ts = "declare interface JaxModelInterface {\n";
        $count = count($map);
        $i = 0;

        foreach ($map as $name => $item) {
            $copy = $item;
            $has_invoke = false;
            if (is_array($copy) && $key = array_search('__invoke', $copy)) {
                unset($copy['__invoke']);
                $has_invoke = true;
            }
            if (is_array($copy) && count($copy)) {
                $ts .= "{$s4}{$name}";
                $ts .= ': '.$this->buildTs2($ts, $item, $name);
            } else {
                if ($has_invoke) {
                    $ts .= "{$s4}{$name}";
                    $ts .= "(...params: any[]): Promise<JaxModelInterface>;\n";
                }
            }
            $i++;
        }

        $ts .= "{$s4}params (...withs: any[]): JaxModelInterface";
        $ts .= "\n{$s4}param (name: string, value: any): JaxModelInterface";
        $ts .= "\n{$s4}blob (name: string, value: any): JaxModelInterface";
        $ts .= "\n{$s4}blobs (fields: any): JaxModelInterface";
        $ts .= "\n{$s4}state (from_to: string, to?: string): JaxModelInterface";
        $ts .= "\n{$s4}path (path: string, ...params: any): JaxModelInterface\n";
        $ts .= '}';
        $ts .= "\n".implode("\n", array_reverse($this->after_ts));
        $ts .= "\ndeclare interface Window {";
        $ts .= "\n{$s4}JaxModel: JaxModelInterface";
        $ts .= "\n{$s4}jax: JaxModelInterface";
        $ts .= "\n}";

        $dir = resource_path(config('layout.js_doc', 'js/doc'));

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($dir.'/jax.d.ts', $ts);
    }

    /**
     * @return array
     */
    protected function getMap()
    {
        $has = collect();
        foreach (JaxController::$namespaces as $path => $ns) {
            if (!is_numeric($path) && is_dir(base_path($path))) {
                $dir = base_path($path);

                $files = collect(File::allFiles($dir))
                    ->map(function (SplFileInfo $file) {
                        return $file->getPathname();
                    })
                    ->filter(function (string $file) {
                        return Str::is('*.php', $file) && is_file($file);
                    })
                    ->mapWithKeys(function (string $file, $key) use ($ns) {
                        $cf = class_in_file($file);

                        return $cf ?
                            [$cf => str_replace("$ns\\", '', $cf)] :
                            [$key => false];
                    })
                    ->filter(function ($path, string $class) use ($ns) {
                        return $class && class_exists($class) && Str::is("$ns*", $class);
                    });

                $has = $has->merge($files);
            }
        }

        $result = [];
        $has->map(function ($path, $class) use (&$result) {
            $name = implode(
                '.',
                array_map(
                    "\\Illuminate\\Support\\Str::snake",
                    explode(
                        '\\', $path
                    )
                )
            );

            $ref = new ReflectionClass($class);

            Arr::set(
                $result,
                $name,
                $this->filterMethods(
                    $ref->getMethods(ReflectionMethod::IS_PUBLIC),
                    $ref
                )
            );
        });

        return $result;
    }

    /**
     * @param  array  $methods
     * @param  ReflectionClass  $ref
     * @return array
     */
    protected function filterMethods(array $methods, ReflectionClass $ref)
    {
        $result = [];
        $ignore = ['access'];
        if ($ref->getParentClass()) {
            /** @var ReflectionMethod $method */
            foreach ($ref->getParentClass()->getMethods() as $method) {
                $ignore[] = $method->name;
            }
        }

        /** @var ReflectionMethod $method */
        foreach ($methods as $method) {
            if (
                !in_array($method->name, $ignore) &&
                (!preg_match('/^__.*/', $method->name) || $method->name === '__invoke')
            ) {
                $result[] = $method->name;
            }
        }

        return $result;
    }

    /**
     * @param  string  $document
     * @param  array  $items
     * @param  string  $name_parent
     * @return string
     */
    protected function buildTs2(string $document, array $items, string $name_parent)
    {
        $n = 'JaxModel'.ucfirst(Str::camel($name_parent)).'Interface';
        $s = str_repeat(' ', 4);
        $document = "declare interface $n {\n";
        $i = 0;
        $count = count($items);
        $has_invoke = false;

        foreach ($items as $name => $item) {
            if (!is_array($item)) {
                if ($item !== '__invoke') {
                    $document .= "{$s}{$item}(...params: any[]): Promise<$n>;\n".(($count - 1) != $i ? $s : '');
                } else {
                    $has_invoke = true;
                }
            } else {
                $document .= "{$s}{$name}: ";

                $document .= $this->buildTs2($document, $item, $name_parent.'_'.$name).
                    (($count - 1) != $i ? $s : '');
            }

            $i++;
        }

        $document .= '}';

        $this->after_ts[] = $document;

        return "$n".($has_invoke ? "|Promise<$n>|Function" : '').";\n";
    }
}
