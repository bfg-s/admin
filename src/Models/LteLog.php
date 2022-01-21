<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Lar\LteAdmin\Core\Traits\DumpedModel;

class LteLog extends Model
{
    use DumpedModel;

    const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'lte_logs';

    /**
     * @var array
     */
    protected $fillable = [
        'icon', 'lte_user_id', 'title', 'detail', 'ip', 'url', 'route', 'method', 'session_id', 'user_agent', 'web_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'icon' => 'string',
        'lte_user_id' => 'int',
        'title' => 'string',
        'detail' => 'string',
        'ip' => 'string',
        'url' => 'string',
        'route' => 'string',
        'method' => 'string',
        'session_id' => 'string',
        'user_agent' => 'string',
        'web_id' => 'int',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function lte_user()
    {
        return $this->hasOne(LteUser::class, 'id', 'lte_user_id');
    }
}
