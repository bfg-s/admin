<?php

namespace LteAdmin\Commands\Generators;

use App;
use Blade;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\BladeDirectives;
use Lar\Tagable\Core\HTML5Library;
use Lar\Tagable\Tag;
use LteAdmin\Interfaces\LteHelpGeneratorInterface;

class GenerateBladeHelpers implements LteHelpGeneratorInterface
{
    /**
     * @var array
     */
    public static $just = [];

    /**
     * Validation method.
     *
     * @return bool
     */
    public function valid()
    {
        return App::isLocal();
    }

    /**
     * Handle call method.
     *
     * @param  Command  $command
     * @return mixed
     */
    public function handle(Command $command)
    {
        if (!is_dir(base_path('.idea'))) {
            return;
        }

        $list = [];

        $directives = collect(Blade::getCustomDirectives())->except(Tag::$components->keys()->map(function ($i) {
            return Str::camel($i);
        })->toArray())->except(HTML5Library::$tags->keys()->toArray())->keys()->toArray();

        foreach ($directives as $directive) {
            if (array_search($directive, static::$just) === false) {
                $list[] = str_repeat(' ',
                        12)."<data directive=\"@{$directive}\" injection=\"true\" prefix=\"&lt;?php echo (new ".BladeDirectives::class.'(" suffix="))-&gt;render(); ?&gt;" />';
            } else {
                $list[] = str_repeat(' ', 12)."<data directive=\"@{$directive}\" />";
            }
        }

        foreach (Tag::$components as $key => $item) {
            $key = Str::camel($key);

            $list[] = str_repeat(' ',
                    12)."<data directive=\"@{$key}\" injection=\"true\" prefix=\"&lt;?php echo (new {$item}(\" suffix=\"))-&gt;render(); ?&gt;\" />";
        }

        HTML5Library::init();

        foreach (HTML5Library::$tags as $key => $tag) {
            $class = Component::getClassNameByTag($key);

            $list[] = str_repeat(' ',
                    12)."<data directive=\"@{$key}\" injection=\"true\" prefix=\"&lt;?php echo (new {$class}(\" suffix=\"))-&gt;render(); ?&gt;\" />";
        }

        $file_data = str_replace('<insert_point />', implode("\n", $list),
            file_get_contents(__DIR__.'/stubs/blade.storm.xml'));

        file_put_contents(base_path('.idea/blade.xml'), $file_data);
    }
}
