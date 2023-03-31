<?php

namespace Admin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class LteSetting
 *
 * @package Admin\Models
 * @property int $id
 * @property string $title
 * @property string $type
 * @property string $name
 * @property string $value
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu newQuery()
 * @method static Builder|AdminMenu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminMenu whereUpdatedAt($value)
 * @method static Builder|AdminMenu withTrashed()
 * @method static Builder|AdminMenu withoutTrashed()
 * @mixin Eloquent
 */
class AdminSetting extends Model
{
    use SoftDeletes;

    protected $connection = 'admin-sqlite';

    /**
     * @var string
     */
    protected $table = 'admin_settings';

    /**
     * @var array
     */
    protected $fillable = [
        'group', 'title', 'type', 'name', 'value', 'description'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'group' => 'string',
        'title' => 'string',
        'type' => 'string',
        'name' => 'string',
        'description' => 'string',
    ];
}
