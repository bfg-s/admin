<?php

namespace LteAdmin\Tests\Models;

use App\Models\Address;
use App\Models\Commentary;
use App\Models\Favorite;
use App\Models\Mailing;
use App\Models\Review;
use App\Models\Shop;
use App\Models\Support;
use Database\Factories\UserFactory;
use Eloquent;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Support\Carbon;

/**
 * User Class
 *
 * @package App\Models
 * @property int $id
 * @property string|null $photo
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $username
 * @property string $email
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property bool $active
 * @property string|null $lat
 * @property string|null $lon
 * @property string|null $ip
 * @property bool $is_provider
 * @property string|null $remember_token
 * @property Carbon|null $unlock_at
 * @property Carbon|null $email_verified_at
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Address|null $address
 * @property-read Collection|Commentary[] $commentaries
 * @property-read int|null $commentaries_count
 * @property-read Collection|Favorite[] $favorites
 * @property-read int|null $favorites_count
 * @property-read Collection|Mailing[] $mailing
 * @property-read int|null $mailing_count
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection|Review[] $reviews
 * @property-read int|null $reviews_count
 * @property-read Collection|Shop[] $shops
 * @property-read int|null $shops_count
 * @property-read Collection|Support[] $supports
 * @property-read int|null $supports_count
 * @method static UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsProvider($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhoto($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUnlockAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 * @method static Builder|User withTrashed()
 * @method static Builder|User withoutTrashed()
 * @mixin Eloquent
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /**
     * The table associated with the model.
     * @return string
     */
    protected $table = 'test_users';

    /**
     * The attributes that are mass assignable.
     * @return array
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be cast.
     * @return array
     */
    protected $casts = [
        'username' => 'string',
        'email' => 'string',
        'password' => 'string',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * @return array
     */
    protected $hidden = [
        'password'
    ];
}
