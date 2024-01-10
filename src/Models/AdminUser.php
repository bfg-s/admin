<?php

namespace Admin\Models;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
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
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Fortify;

/**
 * @property int $id
 * @property string $login
 * @property string $password
 * @property string $email
 * @property string|null $name
 * @property string $avatar
 * @property string $two_factor_secret
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $two_factor_confirmed_at
 * @property-read Collection|\Admin\Models\AdminLog[] $logs
 * @property-read int|null $logs_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|\Admin\Models\AdminRole[] $roles
 * @property-read Collection|\Admin\Models\AdminBrowser[] $browsers
 * @property-read Collection|\Admin\Models\AdminEvent[] $events
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

    /**
     * @var string
     */
    protected $table = 'admin_users';

    /**
     * @var array
     */
    protected $fillable = [
        'login', 'email', 'name', 'avatar', 'password', 'two_factor_secret', 'two_factor_confirmed_at'
    ];

    /**
     * @var array
     */
    protected $guarded = [
        'password', 'remember_token',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'login' => 'string',
        'email' => 'string',
        'name' => 'string',
        'avatar' => 'string',
        "password" => 'string',
        "two_factor_secret" => 'string',
        "two_factor_confirmed_at" => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function browsers(): HasMany
    {
        return $this->hasMany(AdminBrowser::class, 'admin_user_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function events(): HasMany
    {
        return $this->hasMany(AdminEvent::class, 'admin_user_id', 'id');
    }

    /**
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(AdminRole::class, 'admin_role_user', 'admin_user_id', 'admin_role_id');
    }

    /**
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(AdminLog::class, 'admin_user_id', 'id');
    }

    /**
     * @param $avatar
     * @return string
     */
    public function getAvatarAttribute($avatar): string
    {
        return $avatar ?: 'admin/img/user.jpg';
    }
}
