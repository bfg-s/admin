<?php

declare(strict_types=1);

namespace Admin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * A model that is responsible for administrator roles.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read int|null $functions_count
 * @property-read Collection|AdminUser[] $users
 * @property-read int|null $users_count
 * @method static Builder|AdminRole makeDumpedModel()
 * @method static Builder|AdminRole newModelQuery()
 * @method static Builder|AdminRole newQuery()
 * @method static Builder|AdminRole query()
 * @method static Builder|AdminRole whereCreatedAt($value)
 * @method static Builder|AdminRole whereId($value)
 * @method static Builder|AdminRole whereName($value)
 * @method static Builder|AdminRole whereSlug($value)
 * @method static Builder|AdminRole whereUpdatedAt($value)
 * @mixin Eloquent
 */
class AdminRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug',
    ];

    /**
     * Relationship of users to administrator roles.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config('admin.auth.providers.admin.model'),
            'admin_role_user',
            'admin_role_id',
            'admin_user_id'
        );
    }
}
