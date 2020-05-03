<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class LtePermission
 * @package Lar\LteAdmin\Models
 */
class LtePermission extends Model
{
    /**
     * @var string
     */
    protected $table = "lte_permission";

    /**
     * @var string[]
     */
    protected $fillable = [
        "path", "method", "state", "lte_role_id", "active" // state: open, close
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'method' => 'array'
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'active' => 1
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function role()
    {
        return $this->hasOne(LteRole::class, 'id', 'lte_role_id');
    }

    /**
     * @return bool
     */
    public static function check()
    {
        if (!lte_user()) {

            return true;
        }

        $roles = lte_user()->roles->pluck('id')->toArray();

        $permissions = static::whereIn('lte_role_id', $roles)->where('active', 1)->get();

        $closes = $permissions->where('state', 'close');

        $result = true;

        /** @var LtePermission $close */
        foreach ($closes as $close) {

            $path = trim('*/'.config('lte.route.prefix'), '/').'/'.trim($close->path, '/');

            if (($close->method[0] === '*' || array_search(request()->getMethod(), $close->method) !== false) && request()->is($path)) {

                $result = false;
                break;
            }
        }

        if (!$result) {

            $opens = $permissions->where('state', 'open');

            /** @var LtePermission $close */
            foreach ($opens as $open) {

                $path = trim('*/'.config('lte.route.prefix'), '/').'/'.trim($open->path, '/');

                if (($open->method[0] === '*' || array_search(request()->getMethod(), $open->method) !== false) && request()->is($path)) {

                    $result = true;
                    break;
                }
            }
        }


        return $result;
    }
}
