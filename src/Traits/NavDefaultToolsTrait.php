<?php

declare(strict_types=1);

namespace Admin\Traits;

use Admin\AdminEngine;
use Admin\Controllers\AdministratorsController;
use Admin\Controllers\PermissionController;
use Admin\Controllers\RolesController;
use Admin\Core\NavGroup;
use Admin\Core\NavItem;
use Admin\NavigateEngine;
use Closure;

/**
 * Trait with default tools for the navigator.
 */
trait NavDefaultToolsTrait
{
    /**
     * Make default admin panel tools.
     *
     * @return $this
     */
    public function makeDefaults(callable|null $callback = null): static
    {
        $this->adminAdministrationGroup(static function (NavGroup $group) use ($callback) {
            $group->adminAdministrators();
            $group->adminRoles();
            $group->adminPermission();
            if ($callback) {
                call_user_func($callback, $group);
            }
        });

        return $this;
    }

    /**
     * Make default administration group.
     *
     * @param  array|Closure  $call
     * @return NavGroup
     */
    public function adminAdministrationGroup(array|Closure $call): NavGroup
    {
        return $this->group('admin.administration', 'administration', static function (NavGroup $group) use ($call) {
            if (is_embedded_call($call)) {
                call_user_func($call, $group);
            }
        })->icon_cogs();
    }

    /**
     * Make administrator list tool.
     *
     * @param  string|null  $action
     * @return NavItem
     */
    public function adminAdministrators(string $action = null): NavItem
    {
        return $this->item('admin.administrators', 'administrators')
            ->resource('admin_user', $action ?? AdministratorsController::class)
            ->icon_users_cog();
    }

    /**
     * Make roles list tool.
     *
     * @param  string|null  $action
     * @return NavItem
     */
    public function adminRoles(string $action = null): NavItem
    {
        return $this->item('admin.roles', 'roles')
            ->resource('admin_role', $action ?? RolesController::class)
            ->icon_user_secret();
    }

    /**
     * Make permissions list tool.
     *
     * @param  string|null  $action
     * @return NavItem
     */
    public function adminPermission(string $action = null): NavItem
    {
        return $this->item('admin.permission', 'permission')
            ->resource('admin_permission', $action ?? PermissionController::class)
            ->icon_ban();
    }

    /**
     * Create extension menu items.
     *
     * @return void
     */
    public function makeExtensions(): void
    {
        $extensions = AdminEngine::$nav_extensions;

        foreach ($extensions as $key => $extension) {
            if ($key === 'application') {
                continue;
            }

            if (is_array($extension)) {
                foreach ($extension as $item) {
                    NavigateEngine::$extension = $item;

                    $item->navigator($this);

                    NavigateEngine::$extension = null;
                }
            } else {
                NavigateEngine::$extension = $extension;

                $extension->navigator($this);

                NavigateEngine::$extension = null;
            }

            unset(AdminEngine::$nav_extensions[$key]);
        }
    }
}
