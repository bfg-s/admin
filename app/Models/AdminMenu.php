<?php

namespace Admin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminMenu
 *
 * @package Admin\Models
 * @property int $id
 * @property int|null $parent_id
 * @property int $order
 * @property string|null $icon
 * @property string $title
 * @property string $action
 * @property string $type
 * @property string $target
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|AdminMenu[] $childs
 * @property-read int|null $childs_count
 * @property-read AdminMenu|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AdminMenu extends Model
{
    /**
     * @var string
     */
    protected $table = 'admin_menu';

    /**
     * @var array
     */
    protected $fillable = [
        'parent_id', 'order', 'icon', 'title', 'action', 'type', 'target', 'active'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'parent_id' => 'int',
        'order' => 'int',
        'icon' => 'string',
        'title' => 'string',
        'action' => 'string',
        'type' => 'string',
        'target' => 'string',
        'active' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent()
    {
        return $this->hasOne(AdminMenu::class, 'id', 'patent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->hasMany(AdminMenu::class, "parent_id", "id");
    }
}
