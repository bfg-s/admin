<?php

declare(strict_types=1);

namespace Admin\Commands\Generators;

use Admin;
use Admin\Interfaces\AdminHelpGeneratorInterface;
use App\Admin\Delegates\ModelInfoTable;
use App\Admin\Delegates\ModelTable;
use App\Admin\Delegates\SearchForm;
use File;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Log;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class MacroDelegatorsHelperGenerator implements AdminHelpGeneratorInterface
{
    /**
     * @var string[]
     */
    public static $dirs = [
        __DIR__.'/../../Delegates',
    ];

    /**
     * @var Command
     */
    private $command;

    /**
     * @param  Command  $command
     * @return mixed|string
     * @throws ReflectionException
     */
    public function handle(Command $command)
    {
        $this->command = $command;

        $dirs = array_merge([
            //admin_app_path()
        ], static::$dirs);

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
                ->filter(static function ($class) {
                    try {
                        return class_exists($class);
                    } catch (Throwable $throwable) {
                    }
                    return false;
                });

            $classes = array_merge($classes, $files->toArray());
        }

        foreach ($classes as $class) {
            $name = class_basename($class);
            try {
                $refl = new ReflectionClass($class);
            } catch (Throwable $exception) {
                $command->error($exception->getMessage());
                Log::error($exception);
                continue;
            }

            if ($refl->hasProperty('macros')) {
                $macroProperty = $refl->getProperty('macros');
                $macroProperty->setAccessible(true);
                $protectedValue = $macroProperty->getValue();
                if ($protectedValue && is_array($protectedValue)) {
                    $properties = array_keys($protectedValue);

                    $class = class_entity('MacroMethodsFor'.$name);
                    $class->namespace('Admin\Delegates');

                    $class->doc(function ($doc) use ($properties) {
                        foreach ($properties as $property) {
                            $doc->tagMethod(
                                'mixed|static',
                                $property."(...\$parameters)",
                                "Macro method {$property}"
                            );
                        }
                    });

                    $nameClass = Str::snake('MacroMethodsFor'.$name);
                    $file = base_path("vendor/_laravel_idea/_ide_helper_{$nameClass}.php");
                    file_put_contents($file, "<?php \n\n".$class->render());
                }
            }
        }
    }
}
