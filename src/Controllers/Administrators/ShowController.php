<?php

namespace Lar\LteAdmin\Controllers\Administrators;

use Lar\LteAdmin\Controllers\AdministratorsController;
use Lar\LteAdmin\Segments\Info;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;

/**
 * Class ShowController
 * @package Lar\LteAdmin\Controllers\Administrators
 */
class ShowController extends AdministratorsController
{
    /**
     * @return Info
     */
    public function show()
    {
        return Info::create(function (ModelInfoTable $table, Card $card) {
            $card->defaultTools(function ($type) {
                return $type === 'delete' && $this->model()->id == 1 ? false : true;
            });

            $table->row('lte.avatar', 'avatar')->avatar(150);
            $table->row('lte.role', [$this, 'show_role']);
            $table->row('lte.email_address', 'email');
            $table->row('lte.login_name', 'login');
            $table->row('lte.name', 'name');
            $table->at();
        });
    }
}