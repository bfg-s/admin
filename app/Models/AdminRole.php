<?php

namespace Admin\Models;

use Bfg\Dev\Traits\DumpedModel;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AdminRole
 *
 * @package Admin\Models
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole makeDumpedModel()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Admin\Models\AdminUser[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminRole whereUpdatedAt($value)
 */
class AdminRole extends Model
{
    use DumpedModel;

    /**
     * @var string
     */
    protected $table = "admin_roles";

    /**
     * @var array
     */
    protected $fillable = [
        "name", "slug"
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(config('admin.auth.providers.admin.model'), "admin_role_user", "admin_role_id", "admin_user_id");
    }
}
