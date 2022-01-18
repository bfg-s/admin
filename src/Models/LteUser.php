<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Lar\LteAdmin\Core\CheckUserFunction;
use Lar\LteAdmin\Core\Traits\DumpedModel;

/**
 * Class LteUser.
 *
 * @package Lar\LteAdmin\Models
 */
class LteUser extends Model implements AuthenticatableContract
{
    use Authenticatable,
        Authorizable,
        LteUserPermission,
        Notifiable,
        SoftDeletes,
        DumpedModel;

    /**
     * @var string
     */
    protected $table = 'lte_users';

    /**
     * @var array
     */
    protected $fillable = [
        'login', 'email', 'name', 'avatar', 'password',
    ];

    /**
     * @var array
     */
    protected $guarded = [
        'password', 'remember_token',
    ];

    protected $casts = [];

    /**
     * @var string[][]
     */
    protected static $functions = [];

    /**
     * @var CheckUserFunction[]
     */
    protected static $check_user_func_instances = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(LteRole::class, 'lte_role_user', 'lte_user_id', 'lte_role_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs()
    {
        return $this->hasMany(LteLog::class, 'lte_user_id', 'id');
    }

    /**
     * @return string[]
     */
    public function functions()
    {
        if (! isset(static::$functions[$this->id])) {
            static::$functions[$this->id] = LteFunction::withCount(['roles' => function ($many) {
                $many->whereIn('lte_role_id', $this->roles->pluck('id')->toArray());
            }])->where('active', 1)
                ->get('slug')
                ->where('roles_count', '!=', 0)
                ->pluck('slug', 'slug')->toArray();
        }

        return static::$functions[$this->id];
    }

    /**
     * @param $avatar
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        return $avatar ? $avatar : 'lte-admin/img/user.jpg';
    }

    /**
     * @return CheckUserFunction
     */
    public function func()
    {
        if (! isset(static::$check_user_func_instances[$this->id])) {
            static::$check_user_func_instances[$this->id] = new CheckUserFunction($this->functions());
        }

        return static::$check_user_func_instances[$this->id];
    }

    /**
     * @return array
     */
    public function toDump()
    {
        $user_array = $this->toArray();

        $user_array['roles'] = $this->roles->pluck('id')->toArray();

        return $user_array;
    }
}
