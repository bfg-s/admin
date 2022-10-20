<?php

namespace LteAdmin\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Bfg\Entity\Core\Entities\DocumentorEntity;
use LteAdmin\Core\ModelSaver;
use LteAdmin\Models\LteFileStorage;
use LteAdmin\Models\LtePermission;
use LteAdmin\Models\LteRole;
use ReflectionClass;
use ReflectionException;

class LteDbDumpCommand extends Command
{
    /**
     * @var string
     */
    public static $file_name = 'LteAdminDumpSeeder';

    /**
     * @var array
     */
    protected static $models = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lte:db_dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make lte admin db seeds';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        static::$models = array_merge([
            LteRole::class,
            LtePermission::class,
            config('lte.auth.providers.lte.model'),
            LteFileStorage::class,
        ], static::$models);
    }

    /**
     * @param  string  $model
     */
    public static function addModel(string $model)
    {
        static::$models[] = $model;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function handle()
    {
        if (class_exists(static::$file_name)) {
            $this->class_exists(static::$file_name);
        }

        $class = class_entity(static::$file_name);
        $class->doc(static function (DocumentorEntity $doc) {
            $doc->description(static::$file_name.' Class');
            $doc->tagCustom('date', now()->toDateTimeString());
        });
        $class->offAutoUse();
        $class->wrap('php');
        $class->use(Seeder::class);
        $class->use(ModelSaver::class);
        $class->extend('Seeder');

        /** @var Model $model */
        foreach (static::$models as $key => $model) {
            $model = new $model();
            static::$models[$key] = $model;
            if (method_exists($model, 'scopeMakeDumpedModel')) {
                $data = $model::makeDumpedModel();
                if ($data !== false) {
                    $class->prop('protected:'.$model->getTable(), entity(array_entity($data)->minimized()->render()));
                    $class->use(get_class($model));
                } else {
                    unset(static::$models[$key]);
                }
            }
        }

        $method = $class->method('run')
            ->docDescription('Run the database seeds.')
            ->docReturnType('void')
            ->line()
            ->line("\DB::statement('SET FOREIGN_KEY_CHECKS=0;');");

        foreach (static::$models as $model) {
            $model_name = class_basename(get_class($model));
            $method->line("ModelSaver::doMany($model_name::class, \$this->".$model->getTable().');');
        }
        $method->line("\DB::statement('SET FOREIGN_KEY_CHECKS=1;');");

        $render = $class->render();

        $file = database_path('seeds/'.static::$file_name.'.php');

        file_put_contents($file, $render);

        $this->info('Dump created on '.static::$file_name.' seed class.');
    }

    /**
     * @param  string  $class
     * @throws ReflectionException
     */
    protected function class_exists(string $class)
    {
        $ref = new ReflectionClass($class);

        $date = get_doc_var($ref->getDocComment(), 'date');

        if ($date) {
            $name = str_replace(['-', ' ', ':'], '_', $date);

            if (!is_dir(database_path('seeds/LteAdminDumps'))) {
                mkdir(database_path('seeds/LteAdminDumps'), 0777, true);
            }

            file_put_contents(
                database_path("seeds/LteAdminDumps/{$name}.dump"),
                file_get_contents($ref->getFileName())
            );
        }
    }
}
