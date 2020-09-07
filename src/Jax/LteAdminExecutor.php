<?php

namespace Lar\LteAdmin\Jax;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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
    /**
     * Public method access
     * 
     * @return bool
     */
    public function access() {
        
        return !\LteAdmin::guest();
    }
}
