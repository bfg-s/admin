<?php

namespace Admin\Models;

use Bfg\Dev\Traits\DumpedModel;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;

/**
 * Class AdminUser
 *
 * @package Admin\Models
 * @property-read string $avatar
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Admin\Models\AdminRole[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser makeDumpedModel()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser newQuery()
 * @method static \Illuminate\Database\Query\Builder|AdminUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser query()
 * @method static \Illuminate\Database\Query\Builder|AdminUser withTrashed()
 * @method static \Illuminate\Database\Query\Builder|AdminUser withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property string $login
 * @property string $password
 * @property string $email
 * @property string|null $name
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser whereUpdatedAt($value)
 */
class AdminUser extends Model implements AuthenticatableContract
{
    use Authenticatable,
        Authorizable,
        AdminUserPermission,
        Notifiable,
        SoftDeletes,
        DumpedModel;

    /**
     * @var string
     */
    protected $table = "admin_users";

    /**
     * @var array
     */
    protected $fillable = [
        "login", "email", "name", "avatar", "password"
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
        return $this->belongsToMany(AdminRole::class, "admin_role_user", "admin_user_id", "admin_role_id");
    }

    /**
     * @param $avatar
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        return $avatar ? $avatar : admin_asset_url_path('images/user.jpg');
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
