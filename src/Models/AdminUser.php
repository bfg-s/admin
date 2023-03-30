<?php

namespace Admin\Models;

use Eloquent;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Admin\Traits\DumpedModel;

/**
 * App\Models\LteUser.
 *
 * @property int $id
 * @property string $login
 * @property string $password
 * @property string $email
 * @property string|null $name
 * @property string $avatar
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|\App\Models\LteLog[] $logs
 * @property-read int|null $logs_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|\App\Models\LteRole[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser makeDumpedModel()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser newQuery()
 * @method static Builder|AdminUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminUser query()
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
 * @method static Builder|AdminUser withTrashed()
 * @method static Builder|AdminUser withoutTrashed()
 * @mixin Eloquent
 */
class AdminUser extends Model implements AuthenticatableContract
{
    use Authenticatable;
    use Authorizable;
    use AdminUserPermission;
    use Notifiable;
    use SoftDeletes;
    use DumpedModel;

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
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AdminRole::class, 'lte_role_user', 'lte_user_id', 'lte_role_id');
    }

    /**
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AdminLog::class, 'lte_user_id', 'id');
    }

    /**
     * @param $avatar
     * @return string
     */
    public function getAvatarAttribute($avatar)
    {
        return $avatar ?: 'admin/img/user.jpg';
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
