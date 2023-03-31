<?php

namespace Admin\Traits;

use Closure;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Admin\Controllers\AdministratorsController;
use Admin\Controllers\FunctionsController;
use Admin\Controllers\MenuController;
use Admin\Controllers\PermissionController;
use Admin\Controllers\RolesController;
use Admin\Core\NavGroup;
use Admin\Core\NavItem;
use Admin\Admin;
use Admin\Models\AdminMenu;
use Admin\Navigate;

trait NavDefaultTools
{
    /**
     * Make auto default tools.
     * @return $this
     */
    public function makeDefaults(): static
    {
        $this->lteAdministrationGroup(static function (NavGroup $group) {
            $group->lteAdministrators();
            $group->lteRoles();
            $group->ltePermission();
            $group->lteMenu();
        });

        return $this;
    }

    /**
     * Make default administration group.
     * @param  array|Closure  $call
     * @return NavGroup
     */
    public function lteAdministrationGroup(array|Closure $call): NavGroup
    {
        return $this->group('admin.administration', 'administration', static function (NavGroup $group) use ($call) {
            if (is_embedded_call($call)) {
                call_user_func($call, $group);
            }
        })->icon_cogs();
    }

    /**
     * Make administrator list tool.
     * @param  string|null  $action
     * @return NavItem
     */
    public function lteAdministrators(string $action = null): NavItem
    {
        return $this->item('admin.administrators', 'administrators')
            ->resource('admin_user', $action ?? AdministratorsController::class)
            ->icon_users_cog();
    }

    /**
     * Make roles list tool.
     * @param  string|null  $action
     * @return NavItem
     */
    public function lteRoles(string $action = null): NavItem
    {
        return $this->item('admin.roles', 'roles')
            ->resource('admin_role', $action ?? RolesController::class)
            ->icon_user_secret();
    }

    /**
     * Make permissions list tool.
     * @param  string|null  $action
     * @return NavItem
     */
    public function ltePermission(string $action = null): NavItem
    {
        return $this->item('admin.permission', 'permission')
            ->resource('admin_permission', $action ?? PermissionController::class)
            ->icon_ban();
    }

    /**
     * Make menu list tool.
     * @param  string|null  $action
     * @return NavItem
     */
    public function lteMenu(string $action = null): NavItem
    {
        return $this->item('admin.admin_menu', 'menu')
            ->resource('admin_menu', $action ?? MenuController::class)
            ->icon_bars();
    }

    public function makeMenu(): void
    {
        $db = config('admin.connections.admin-sqlite.database');

        if (is_file($db) && Schema::connection('admin-sqlite')->hasTable('admin_menu')) {
            AdminMenu::where('active', 1)
                ->orderBy('order')
                ->whereNull('parent_id')
                ->with('child')
                ->get()
                ->map(fn(AdminMenu $menu) => $this->injectRemoteMenu($menu));
        }
    }

    protected function injectRemoteMenu(AdminMenu $menu, NavGroup $rootGroup = null): void
    {
        $rootGroup = $rootGroup ?: $this;

        if ($menu->type === 'item') {
            $rootGroup->item($menu->name, $menu->route, Str::parseCallback($menu->action))
                ->icon($menu->icon);
        } else {
            if ($menu->type === 'resource') {
                $rootGroup->item($menu->name)
                    ->resource($menu->route, $menu->action)
                    ->icon($menu->icon)
                    ->except(...($menu->except ?: []));
            } else {
                $rootGroup->group(
                    $menu->name,
                    $menu->route,
                    fn(NavGroup $group) => $menu->child->map(fn(AdminMenu $lteMenu) => $this->injectRemoteMenu($lteMenu,
                        $group))
                )->icon($menu->icon);
            }
        }
    }

    /**
     * @return void
     */
    public function makeExtensions(): void
    {
        $extensions = Admin::$nav_extensions;

        if (count($extensions) > 1) {
            $this->menu_header('admin.extensions');
        }

        foreach ($extensions as $key => $extension) {
            if ($key === 'application') {
                continue;
            }

            if (is_array($extension)) {
                foreach ($extension as $item) {
                    Navigate::$extension = $item;

                    $item->navigator($this);

                    Navigate::$extension = null;
                }
            } else {
                Navigate::$extension = $extension;

                $extension->navigator($this);

                Navigate::$extension = null;
            }

            unset(Admin::$nav_extensions[$key]);
        }
    }

    /**
     * Make default access group.
     * @param  Closure|array  $call
     * @return NavGroup
     */
    public function lteAccessGroup($call): NavGroup
    {
        return $this->group('admin.access', 'access', static function (NavGroup $group) use ($call) {
            if (is_embedded_call($call)) {
                call_user_func($call, $group);
            }
        })->icon_universal_access();
    }
}
