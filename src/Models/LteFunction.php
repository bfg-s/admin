<?php

namespace Lar\LteAdmin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Lar\LteAdmin\Core\Traits\DumpedModel;

/**
 * Lar\LteAdmin\Models\LteFunction.
 *
 * @property int $id
 * @property string $slug
 * @property string|null $class
 * @property string|null $description
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\App\Models\LteRole[] $roles
 * @property-read int|null $roles_count
 * @method static Builder|LteFunction makeDumpedModel()
 * @method static Builder|LteFunction newModelQuery()
 * @method static Builder|LteFunction newQuery()
 * @method static Builder|LteFunction query()
 * @method static Builder|LteFunction whereActive($value)
 * @method static Builder|LteFunction whereClass($value)
 * @method static Builder|LteFunction whereCreatedAt($value)
 * @method static Builder|LteFunction whereDescription($value)
 * @method static Builder|LteFunction whereId($value)
 * @method static Builder|LteFunction whereSlug($value)
 * @method static Builder|LteFunction whereUpdatedAt($value)
 * @mixin Eloquent
 */
class LteFunction extends Model
{
    use DumpedModel;

    /**
     * @var Collection
     */
    public static $now;
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
     * @return BelongsToMany
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
