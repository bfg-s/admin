<?php

declare(strict_types=1);

namespace Admin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $functions_count
 * @property-read Collection|AdminUser[] $users
 * @property-read int|null $users_count
 * @method static Builder|AdminRole makeDumpedModel()
 * @method static Builder|AdminRole newModelQuery()
 * @method static Builder|AdminRole newQuery()
 * @method static Builder|AdminRole query()
 * @method static Builder|AdminRole whereCreatedAt($value)
 * @method static Builder|AdminRole whereId($value)
 * @method static Builder|AdminRole whereName($value)
 * @method static Builder|AdminRole whereSlug($value)
 * @method static Builder|AdminRole whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AdminBrowser extends Model
{
    /**
     * @var string
     */
    protected $table = 'admin_browsers';

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'ip', 'user_agent', 'session_id', 'notification_settings', 'active', 'admin_user_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'name' => 'string',
        'ip' => 'string',
        'user_agent' => 'string',
        'session_id' => 'string',
        'notification_settings' => 'array',
        'active' => 'bool',
        'admin_user_id' => 'int'
    ];

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(AdminUser::class, 'id', 'admin_user_id');
    }
}
