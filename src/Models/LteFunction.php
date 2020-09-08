<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lar\LteAdmin\Core\Traits\DumpedModel;

/**
 * Class LtePermission
 * @package Lar\LteAdmin\Models
 */
class LteFunction extends Model
{
    use DumpedModel;
    
    /**
     * @var string
     */
    protected $table = "lte_functions";

    /**
     * @var string[]
     */
    protected $fillable = [
        "slug", "class", "description", "active"
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
