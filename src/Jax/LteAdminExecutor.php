<?php

namespace Lar\LteAdmin\Jax;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Lar\LJS\JaxController;
use Lar\LJS\JaxExecutor;
use Lar\LteAdmin\Controllers\ModalController;
use Lar\LteAdmin\LteBoot;
use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Resources\LteFunctionResource;

/**
 * Class LteAdminExecutor
 * @package Lar\LteAdmin\Jax
 */
class LteAdminExecutor extends JaxExecutor
{
    public function __construct()
    {
        JaxController::on_mounted(function ($executor, $method, $params, $executor_class_name) {
            lte_log_warning("Call executing command", "{$executor_class_name}@{$method}", "fas fa-exchange-alt");
        });
    }

    /**
     * Public method access
     *
     * @return bool
     */
    public function access() {

        return !\LteAdmin::guest();
    }
}
