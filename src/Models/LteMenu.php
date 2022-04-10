<?php

namespace LteAdmin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class LteMenu
 *
 * @package LteAdmin\Models
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
 * @property-read Collection|LteMenu[] $child
 * @property-read int|null $child_count
 * @property-read LteMenu|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu newQuery()
 * @method static Builder|LteMenu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereUpdatedAt($value)
 * @method static Builder|LteMenu withTrashed()
 * @method static Builder|LteMenu withoutTrashed()
 * @mixin Eloquent
 */
class LteMenu extends Model
{
    use SoftDeletes;

    protected $connection = 'lte-sqlite';

    /**
     * @var string
     */
    protected $table = 'lte_menu';

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
        return $this->hasOne(LteMenu::class, 'id', 'patent_id');
    }

    /**
     * @return HasMany
     */
    public function child()
    {
        return $this->hasMany(LteMenu::class, "parent_id", "id")
            ->with('child');
    }
}
