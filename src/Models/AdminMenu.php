<?php

namespace Admin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * @package Admin\Models
 * @property int $id
 * @property string $name
 * @property string $icon
 * @property string $route
 * @property string|null $action
 * @property string $type
 * @property array|null $except
 * @property int $order
 * @property bool $active
 * @property int|null $parent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|AdminMenu[] $child
 * @property-read int|null $child_count
 * @property-read AdminMenu|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu newQuery()
 * @method static Builder|AdminMenu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereUpdatedAt($value)
 * @method static Builder|AdminMenu withTrashed()
 * @method static Builder|AdminMenu withoutTrashed()
 * @mixin Eloquent
 */
class AdminMenu extends Model
{
    use SoftDeletes;

    protected $connection = 'admin-sqlite';

    /**
     * @var string
     */
    protected $table = 'admin_menu';

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'icon', 'route', 'action', 'type', 'except', 'order', 'active', 'parent_id'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'icon' => 'string',
        'route' => 'string',
        'action' => 'string',
        'type' => 'string',
        'except' => 'array',
        'order' => 'integer',
        'active' => 'boolean',
        'parent_id' => 'integer',
    ];

    /**
     * @return HasOne
     */
    public function parent()
    {
        return $this->hasOne(AdminMenu::class, 'id', 'patent_id');
    }

    /**
     * @return HasMany
     */
    public function child()
    {
        return $this->hasMany(AdminMenu::class, "parent_id", "id")
            ->with('child');
    }
}
