<?php

namespace Lar\LteAdmin\Controllers\Administrators;

use Lar\LteAdmin\Controllers\AdministratorsController;
use Lar\LteAdmin\Models\LteUser;
use Lar\LteAdmin\Segments\Sheet;
use Lar\LteAdmin\Segments\Tagable\ModelTable;

/**
 * Class IndexController
 * @package Lar\LteAdmin\Controllers\Administrators
 */
class IndexController extends AdministratorsController {

    /**
     * @return Sheet
     */
    public function index()
    {
        return Sheet::create('lte.admin_list', function (ModelTable $table) {

            $table->search->id();
            $table->search->email('email', 'lte.email_address');
            $table->search->input('login', 'lte.login_name', '=%');
            $table->search->input('name', 'lte.name', '=%');
            $table->search->at();

            $table->id();
            $table->column('lte.avatar', 'avatar')->avatar();
            $table->column('lte.role', [$this, 'show_role']);
            $table->column('lte.email_address', 'email')->sort();
            $table->column('lte.login_name', 'login')->sort();
            $table->column('lte.name', 'name')->sort();
            $table->at();
            $table->controlDelete(function (LteUser $user) { return $user->id !== 1 && admin()->id !== $user->id; });
            $table->disableChecks();
        });
    }
}