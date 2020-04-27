<?php

namespace Lar\LteAdmin\Models;

/**
 * Trait LteUserPermission
 *
 * @package Lar\LteAdmin\Models
 */
trait LteUserPermission
{
    /**
     * @return int
     */
    public function haveRoles()
    {
        return $this->roles->count();
    }

    /**
     * @param  array  $roles
     * @return bool
     */
    public function hasRoles(array $roles)
    {
        return !!$this->roles->whereIn('slug', $roles)->count();
    }
}
