<?php

namespace LteAdmin\Tests\Admin;

use LteAdmin\Core\NavGroup;
use LteAdmin\Core\NavigatorExtensionProvider;
use LteAdmin\Interfaces\ActionWorkExtensionInterface;
use LteAdmin\Tests\Admin\Controllers\UsersController;

/**
 * Navigator Class.
 * @package LteAdmin\Tests\Admin
 */
class Navigator extends NavigatorExtensionProvider implements ActionWorkExtensionInterface
{
    /**
     * @return void
     */
    public function handle(): void
    {
        $this->item('Users')
            ->resource('users', UsersController::class)
            ->icon_users();

        $this->lteAdministrationGroup(static function (NavGroup $group) {
            $group->lteAdministrators();

            $group->lteRoles();

            $group->ltePermission();
        });
    }
}
