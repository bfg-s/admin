<?php

namespace Lar\LteAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Lar\LteAdmin\Core\Traits\DumpedModel;

/**
 * Lar\LteAdmin\Models\LteLog.
 *
 * @property string $title
 * @property string|null $detail
 * @property string $ip
 * @property string|null $url
 * @property string|null $route
 * @property string|null $method
 * @property string $user_agent
 * @property string $session_id
 * @property int|null $lte_user_id
 * @property int|null $web_id
 * @property string|null $icon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Models\LteUser|null $lte_user
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog makeDumpedModel()
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog query()
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereDetail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereLteUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereSessionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LteLog whereWebId($value)
 * @mixin \Eloquent
 */
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
