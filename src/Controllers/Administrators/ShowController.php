<?php

namespace Lar\LteAdmin\Controllers\Administrators;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Controllers\AdministratorsController;
use Lar\LteAdmin\Controllers\UserController;
use Lar\LteAdmin\Models\LteLog;
use Lar\LteAdmin\Segments\Info;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\CardBody;
use Lar\LteAdmin\Segments\Tagable\ModelInfoTable;
use Lar\LteAdmin\Segments\Tagable\Timeline;

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
        })->next(function (DIV $div) {

            $div->card('lte.timeline')
                ->danger()->body(function (CardBody $body) {

                    UserController::timelineComponent($body, $this->model()->logs());
                });
        });
    }
}
