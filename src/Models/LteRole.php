<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LteRole
 *
 * @package Lar\LteAdmin\Models
 */
class LteRole extends Model
{
    /**
     * @var string
     */
    protected $table = "lte_roles";

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
        return $this->belongsToMany(config('lte.auth.providers.lte.model'), "lte_role_user", "lte_role_id", "lte_user_id");
    }
}
