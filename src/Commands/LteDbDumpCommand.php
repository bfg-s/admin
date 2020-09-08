<?php

namespace Lar\LteAdmin\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Lar\EntityCarrier\Core\Entities\DocumentorEntity;
use Lar\LteAdmin\Core\ModelSaver;
use Lar\LteAdmin\Models\LteFileStorage;
use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Models\LtePermission;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Models\LteUser;

/**
 * Class LteDbDumpCommand
 * @package Lar\LteAdmin\Commands
 */
class LteDbDumpCommand extends Command
{
    /**
     * @var string
     */
    protected $file_name = "LteAdminDumpSeeder";

    /**
     * @var array
     */
    protected static $models = [
        LteUser::class,
        LteFileStorage::class,
        LteFunction::class,
        LtePermission::class,
        LteRole::class
    ];

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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \ReflectionException
     */
    public function handle()
    {
        if (class_exists($this->file_name)) {

            $this->class_exists($this->file_name);
        }

        $class = class_entity($this->file_name);
        $class->doc(function (DocumentorEntity $doc) {
            $doc->description($this->file_name . " Class");
            $doc->tagCustom('date', now()->toDateTimeString());
        });
        $class->offAutoUse();
        $class->wrap('php');
        $class->use(Seeder::class);
        $class->use(ModelSaver::class);
        $class->extend(Seeder::class);

        /** @var Model $model */
        foreach (static::$models as $key => $model) {
            $model = new $model;
            static::$models[$key] = $model;
            if (method_exists($model, 'scopeMakeDumpedModel')) {
                $data = $model::makeDumpedModel();
                if ($data !== false) {
                    $class->prop('protected:' . $model->getTable(), entity(array_entity($data)->minimized()->render()));
                    $class->use(get_class($model));
                } else {
                    unset(static::$models[$key]);
                }
            }
        }

        $method = $class->method('run')
            ->docDescription('Run the database seeds.')
            ->docReturnType('void')
            ->line();

        foreach (static::$models as $model) {
            $model_name = class_basename(get_class($model));
            $method->line("ModelSaver::do($model_name::class, \$this->".$model->getTable().");");
        }

        $render = $class->render();

        $file = database_path("seeds/{$this->file_name}.php");

        file_put_contents($file, $render);

        $this->info("Dump created on {$this->file_name} seed class.");
    }

    /**
     * @param  string  $class
     * @throws \ReflectionException
     */
    protected function class_exists(string $class)
    {
        $ref = new \ReflectionClass($class);

        $date = get_doc_var($ref->getDocComment(), 'date');

        if ($date) {

            $name = str_replace(['-',' ',':'], '_', $date);

            if (!is_dir(database_path("seeds/LteAdminDumps"))) {
                mkdir(database_path("seeds/LteAdminDumps"), 0777, true);
            }

            file_put_contents(
                database_path("seeds/LteAdminDumps/{$name}.dump"),
                file_get_contents($ref->getFileName())
            );
        }
    }

    /**
     * @param  string  $model
     */
    public static function addModel(string $model)
    {
        static::$models[] = $model;
    }
}
