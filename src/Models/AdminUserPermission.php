<?php

declare(strict_types=1);

namespace Admin\Models;

/**
 * The user model trait is intended for methods that help to find out everything about the role of the user in the admin panel.
 */
trait AdminUserPermission
{
    /**
     * Find out the number of user roles.
     *
     * @return int
     */
    public function haveRoles(): int
    {
        return $this->roles->count();
    }

    /**
     * Find out whether the user has the specified roles.
     *
     * @param  array  $roles
     * @return bool
     */
    public function hasRoles(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find out if the specified user role exists.
     *
     * @param  string  $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return (bool) $this->roles->where('slug', $role)->count();
    }

    /**
     * Check if the user is root.
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return $this->hasRole('root');
    }

    /**
     * Check if the user is an administrator.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if the user is a moderator.
     *
     * @return bool
     */
    public function isModerator(): bool
    {
        return $this->hasRole('moderator');
    }

    /**
     * Check whether the user is root or administrator.
     *
     * @return bool
     */
    public function isRootAdmin(): bool
    {
        return $this->hasRoles(['root', 'admin']);
    }
}
