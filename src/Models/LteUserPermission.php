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

    /**
     * @param  string  $role
     * @return bool
     */
    public function hasRole(string $role)
    {
        return !!$this->roles->where('slug', $role)->count();
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return $this->hasRole('root');
    }

    /**
     * @return bool
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * @return bool
     */
    public function isRootAdmin()
    {
        return $this->hasRoles(['root','admin']);
    }

    /**
     * @return bool
     */
    public function isModerator()
    {
        return $this->hasRole('moderator');
    }
}
