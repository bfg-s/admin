<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Lar\LteAdmin\Core\Traits\DumpedModel;

/**
 * Lar\LteAdmin\Models\LteRole.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\LteFunction[] $functions
 * @property-read int|null $functions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\Lar\LteAdmin\Models\LteUser[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|LteRole makeDumpedModel()
 * @method static \Illuminate\Database\Eloquent\Builder|LteRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LteRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LteRole query()
 * @method static \Illuminate\Database\Eloquent\Builder|LteRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteRole whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteRole whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LteRole extends Model
{
    use DumpedModel;

    /**
     * @var string
     */
    protected $table = 'lte_roles';

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'slug',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('lte.auth.providers.lte.model'), 'lte_role_user', 'lte_role_id', 'lte_user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function functions()
    {
        return $this->belongsToMany(LteFunction::class, 'lte_role_function', 'lte_role_id', 'lte_function_id');
    }
}
