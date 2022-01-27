<?php

namespace Lar\LteAdmin\Commands;

use Arr;
use Illuminate\Console\Command;
use Lar\LteAdmin\Components\ModalBodyComponent;
use Lar\LteAdmin\Components\ModalComponent;
use Lar\LteAdmin\Controllers\ModalController;
use Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class LteModalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'lte:modal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make lte modal';

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
     */
    public function handle()
    {
        $name = $this->name();
        $namespace = $this->namespace();
        $path = $this->rp();

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $class = class_entity($name)->wrap('php');
        $class->namespace($namespace);
        $class->use(ModalComponent::class);
        $class->use(ModalBodyComponent::class);
        $class->extend(ModalController::class);
        $class->method('create')
            ->param('body', null, ModalBodyComponent::class)
            ->param('modal', null, ModalComponent::class)
            ->line()
            ->line("\$modal->title('{$name}');")
            ->line("\$body->text('{$namespace}\\{$name}');");

        file_put_contents($path.'/'.$name.'.php', $class);

        $this->info("Modal [$namespace\\$name] generated!");
    }

    /**
     * @return mixed
     */
    protected function name()
    {
        $return = ucfirst(Str::camel(Arr::last($this->segments())));

        if (!preg_match('/Modal$/', $return)) {
            $return .= 'Modal';
        }

        return $return;
    }

    /**
     * @return array
     */
    protected function segments()
    {
        return array_map('Str::snake', explode('/', $this->argument('name')));
    }

    /**
     * @return string
     */
    protected function namespace()
    {
        return $this->option('dir') ? implode('\\',
            array_map('ucfirst',
                array_map('Str::camel',
                    explode('/', $this->option('dir'))
                )
            )
        ) : lte_app_namespace('Modals').$this->path('\\');
    }

    /**
     * @param  string  $delimiter
     * @return string
     */
    protected function path(string $delimiter = '/')
    {
        $segments = $this->segments();

        unset($segments[array_key_last($segments)]);

        $add = '';

        if (count($segments)) {
            $add .= $delimiter.implode($delimiter, array_map('ucfirst', array_map('Str::camel', $segments)));
        }

        return $add;
    }

    /**
     * @return string
     */
    protected function rp()
    {
        if ($this->option('dir')) {
            return '/'.trim(base_path($this->option('dir').'/'.trim($this->path(), '/')), '/');
        }

        return '/'.trim(lte_app_path('Modals/'.trim($this->path(), '/')), '/');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'Name of modal'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['dir', 'd', InputOption::VALUE_OPTIONAL, 'Directory of creation'],
        ];
    }
}
