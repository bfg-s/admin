<?php

namespace LteAdmin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * Class LteSetting
 *
 * @package LteAdmin\Models
 * @property int $id
 * @property string $title
 * @property string $type
 * @property string $name
 * @property string $value
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu newQuery()
 * @method static Builder|LteMenu onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu query()
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteMenu whereUpdatedAt($value)
 * @method static Builder|LteMenu withTrashed()
 * @method static Builder|LteMenu withoutTrashed()
 * @mixin Eloquent
 */
class LteSetting extends Model
{
    use SoftDeletes;

    protected $connection = 'lte-sqlite';

    /**
     * @var string
     */
    protected $table = 'lte_settings';

    /**
     * @var array
     */
    protected $fillable = [
        'title', 'type', 'name', 'value', 'description'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'title' => 'string',
        'type' => 'string',
        'name' => 'string',
        'description' => 'string',
    ];
}
