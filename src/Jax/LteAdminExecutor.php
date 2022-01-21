<?php

namespace Lar\LteAdmin\Jax;

use Lar\LJS\JaxController;
use Lar\LJS\JaxExecutor;

class LteAdminExecutor extends JaxExecutor
{
    public function __construct()
    {
        JaxController::on_mounted(static function ($executor, $method, $params, $executor_class_name) {
            lte_log_warning('Call executing command', "{$executor_class_name}@{$method}", 'fas fa-exchange-alt');
        });
    }

    /**
     * Public method access.
     *
     * @return bool
     */
    public function access()
    {
        return ! \LteAdmin::guest();
    }
}
