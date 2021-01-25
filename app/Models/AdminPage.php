<?php

namespace Admin\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * Admin\Models\AdminPage
 *
 * @property int $id
 * @property int|null $parent_id
 * @property int $order
 * @property string|null $icon
 * @property string $title
 * @property string|null $description
 * @property string $action
 * @property string $type
 * @property string $target
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|AdminPage[] $childs
 * @property-read int|null $childs_count
 * @property-read AdminPage|null $parent
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $position
 * @method static \Illuminate\Database\Eloquent\Builder|AdminPage wherePosition($value)
 */
class AdminPage extends Model
{
    const TYPE_LINK = 'link',
        TYPE_MODAL = 'modal',
        TYPE_CALL = 'call';

    const TARGET_SELF = 'self',
        TARGET_BLANK = 'blank';

    const POSITION_MENU = 'menu',
        POSITION_BOTTOM = 'bottom',
        POSITION_NAVBAR = 'navbar';

    /**
     * @var string
     */
    protected $table = 'admin_pages';

    /**
     * @var array
     */
    protected $fillable = [
        'parent_id', 'order', 'icon', 'title', 'description', 'action', 'type', 'target', 'active'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'parent_id' => 'int',
        'order' => 'int',
        'icon' => 'string',
        'title' => 'string',
        'description' => 'string',
        'action' => 'string',
        'type' => 'string', // [link], modal, call
        'target' => 'string', // [self], blank
        'position' => 'string', // [menu], bottom, navbar
        'active' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parent()
    {
        return $this->hasOne(AdminPage::class, 'id', 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->hasMany(AdminPage::class, "parent_id", "id")
            ->with(['childs', 'parent']);
    }
}
