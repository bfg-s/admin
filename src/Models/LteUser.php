<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LteUser
 *
 * @package Lar\LteAdmin\Models
 */
class LteUser extends Model implements AuthenticatableContract
{
    use Authenticatable, LteUserPermission;

    /**
     * @var string
     */
    protected $table = "lte_users";

    /**
     * @var array
     */
    protected $fillable = [
        "login", "email", "name", "avatar"
    ];

    /**
     * @var array
     */
    protected $guarded = [
        "password", "remember_token"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(LteRole::class, "lte_role_user", "lte_user_id", "lte_role_id");
    }

    /**
     * @param $avatar
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        return $avatar ? $avatar : 'lte-admin/img/user.jpg';
    }
}
