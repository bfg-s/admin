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
abstract class PermissionsExtensionProvider {

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
        ModelSaver::doMany(LteFunction::class, $functions = $this->functions());
        if (count($functions)) { $this->command->info('Created ' . count($functions) . ' permission functions.'); }
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

        $functions = $this->functions();
        $functions_count = 0;
        foreach ($functions as $function) {
            if (LteFunction::where('slug', $function['slug'])->delete()) {$functions_count++;}
        }
        if ($functions_count) { $this->command->info('Deleted ' . $functions_count . ' permission functions.'); }
    }

    /**
     * @param  array  $roles
     * @param  string|null  $slug
     * @param  string|null  $description
     * @return array
     */
    public function makeFunction(array $roles, string $slug = null, string $description = null): array
    {
        return [
            'slug' => $slug ? $this->provider::$slug . "_{$slug}" : $this->provider::$slug,
            'description' => $description ? $description : $this->provider::$description . ($slug ? " [{$slug}]":""),
            'roles' => collect($roles)->map(function ($item) {
                return is_numeric($item) ? $item : LteRole::where('slug', $item)->first()->id;
            })->filter()->values()->toArray()
        ];
    }

    /**
     * @return array
     */
    abstract public function functions(): array;
}