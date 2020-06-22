<?php

namespace Lar\LteAdmin\Core;

use Illuminate\Console\Command;
use Lar\LteAdmin\ExtendProvider;
use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Models\LteRole;

/**
 * Class InstallExtensionProvider
 * @package Lar\LteAdmin\Core
 */
class PermissionsExtensionProvider {

    /**
     * @var Command
     */
    public $command;
    /**
     * @var ExtendProvider
     */
    public $provider;

    /**
     * InstallExtensionProvider constructor.
     * @param  Command  $command
     * @param  ExtendProvider  $provider
     */
    public function __construct(Command $command, ExtendProvider $provider)
    {
        $this->command = $command;
        $this->provider = $provider;
    }

    /**
     * Make all extension permissions
     */
    public function up()
    {
        if (method_exists($this, 'roles')) {
            $roles = $this->roles();
            if (is_array($roles)) {
                ModelSaver::doMany(LteRole::class, $roles);
                if (count($roles)) { $this->command->info('Created ' . count($roles) . ' roles.'); }
            }
        }
        $pushed = ModelSaver::doMany(LteFunction::class, array_merge([$this->makeFunction('access')], $this->functions()));
        if ($pushed->count()) { $this->command->info('Created ' . $pushed->count() . ' permission functions.'); }
    }

    /**
     * Drop all extension permissions
     * @throws \Exception
     */
    public function down()
    {
        if (method_exists($this, 'roles')) {
            $roles = $this->roles();
            if (is_array($roles)) {
                $roles_count = 0;
                foreach ($roles as $role) {
                    if (LteRole::where('slug', $role['slug'])->delete()) {$roles_count++;}
                }
                if ($roles_count) {
                    $this->command->info('Deleted '.$roles_count.' roles.');
                }
            }
        }

        $functions_count = 0;
        foreach (array_merge([$this->makeFunction('access')], $this->functions()) as $function) {
            if (is_array($function) && count($function) && LteFunction::where('slug', $function['slug'])->delete()) {
                $functions_count++;
            }
        }
        if ($functions_count) { $this->command->info('Deleted ' . $functions_count . ' permission functions.'); }
    }

    /**
     * @param  array  $roles
     * @param  string|null  $slug
     * @param  string|null  $description
     * @return array
     */
    public function makeFunction(string $slug, array $roles = ['*'], string $description = null): array
    {
        return [
            'slug' => $slug,
            'class' => get_class($this->provider),
            'description' => $this->provider::$description . ($description ? " [$description]" : (\Lang::has("lte.about_method.{$slug}") ? " [@lte.about_method.{$slug}]":" [{$slug}]")),
            'roles' => $roles === ['*'] ? LteRole::all()->pluck('id')->toArray() : collect($roles)->map(function ($item) {
                return is_numeric($item) ? $item : LteRole::where('slug', $item)->first()->id;
            })->filter()->values()->toArray()
        ];
    }

    /**
     * @return array
     */
    public function functions(): array {

        return [];
    }
}