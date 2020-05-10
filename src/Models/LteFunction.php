<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class LtePermission
 * @package Lar\LteAdmin\Models
 */
class LteFunction extends Model
{
    /**
     * @var string
     */
    protected $table = "lte_functions";

    /**
     * @var string[]
     */
    protected $fillable = [
        "slug", "description", "active"
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'active' => 1
    ];

    /**
     * @var Collection
     */
    static $now;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(LteRole::class, "lte_role_function", "lte_function_id", "lte_role_id");
    }
}
