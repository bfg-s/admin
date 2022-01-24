<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lar\LteAdmin\Core\Traits\DumpedModel;

/**
 * Lar\LteAdmin\Models\LteFunction.
 *
 * @property int $id
 * @property string $slug
 * @property string|null $class
 * @property string|null $description
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Collection|\App\Models\LteRole[] $roles
 * @property-read int|null $roles_count
 * @method static \Illuminate\Database\Eloquent\Builder|LteFunction makeDumpedModel()
 * @method static \Illuminate\Database\Eloquent\Builder|LteFunction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LteFunction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LteFunction query()
 * @method static \Illuminate\Database\Eloquent\Builder|LteFunction whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteFunction whereClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteFunction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteFunction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteFunction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteFunction whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteFunction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LteFunction extends Model
{
    use DumpedModel;

    /**
     * @var string
     */
    protected $table = 'lte_functions';

    /**
     * @var string[]
     */
    protected $fillable = [
        'slug', 'class', 'description', 'active',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'active' => 1,
    ];

    /**
     * @var Collection
     */
    public static $now;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(LteRole::class, 'lte_role_function', 'lte_function_id', 'lte_role_id');
    }

    /**
     * @return array
     */
    public function toDump()
    {
        $functions_array = $this->toArray();

        $functions_array['roles'] = $this->roles->pluck('id')->toArray();

        return $functions_array;
    }
}
