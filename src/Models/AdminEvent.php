<?php

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
 * @property string $title
 * @property string $description
 * @property string $url
 * @property int $admin_user_id
 * @property string|\Carbon\Carbon|null $start
 * @property string|\Carbon\Carbon|null $end
 * @property AdminUser|null $user
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
class AdminEvent extends Model
{
    /**
     * @var string
     */
    protected $table = 'admin_events';

    /**
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'url', 'start', 'end', 'admin_user_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'title' => 'string',
        'description' => 'string',
        'url' => 'string',
        'start' => 'datetime',
        'end' => 'datetime',
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
