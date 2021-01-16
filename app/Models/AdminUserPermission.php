<?php

namespace Admin\Models;

/**
 * Trait AdminUserPermission
 * @package Admin\Models
 */
trait AdminUserPermission
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
        foreach ($roles as $role) {

            if ($this->hasRole($role)) {

                return true;
            }
        }

        return false;
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
