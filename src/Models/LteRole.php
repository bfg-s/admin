<?php

namespace LteAdmin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use LteAdmin\Traits\DumpedModel;

/**
 * LteAdmin\Models\LteRole.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $functions_count
 * @property-read Collection|LteUser[] $users
 * @property-read int|null $users_count
 * @method static Builder|LteRole makeDumpedModel()
 * @method static Builder|LteRole newModelQuery()
 * @method static Builder|LteRole newQuery()
 * @method static Builder|LteRole query()
 * @method static Builder|LteRole whereCreatedAt($value)
 * @method static Builder|LteRole whereId($value)
 * @method static Builder|LteRole whereName($value)
 * @method static Builder|LteRole whereSlug($value)
 * @method static Builder|LteRole whereUpdatedAt($value)
 * @mixin Eloquent
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
     * @return BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('lte.auth.providers.lte.model'), 'lte_role_user', 'lte_role_id',
            'lte_user_id');
    }
}
