<?php

namespace LteAdmin\Models;

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
use LteAdmin\Traits\DumpedModel;

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
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser makeDumpedModel()
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser newQuery()
 * @method static Builder|LteUser onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser query()
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser whereLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteUser whereUpdatedAt($value)
 * @method static Builder|LteUser withTrashed()
 * @method static Builder|LteUser withoutTrashed()
 * @mixin Eloquent
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
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(LteRole::class, 'lte_role_user', 'lte_user_id', 'lte_role_id');
    }

    /**
     * @return HasMany
     */
    public function logs()
    {
        return $this->hasMany(LteLog::class, 'lte_user_id', 'id');
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
     * @return array
     */
    public function toDump()
    {
        $user_array = $this->toArray();

        $user_array['roles'] = $this->roles->pluck('id')->toArray();

        return $user_array;
    }
}
