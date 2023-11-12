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
 * @property string $name
 * @property string $color
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
class AdminEventTemplate extends Model
{
    /**
     * @var string
     */
    protected $table = 'admin_events_templates';

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'color', 'admin_user_id'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'name' => 'string',
        'color' => 'string',
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
