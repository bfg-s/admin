<?php

namespace LteAdmin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use LteAdmin\Traits\DumpedModel;

/**
 * LteAdmin\Models\LteLog.
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
 * @property Carbon|null $created_at
 * @property-read \App\Models\LteUser|null $lte_user
 * @method static Builder|LteLog makeDumpedModel()
 * @method static Builder|LteLog newModelQuery()
 * @method static Builder|LteLog newQuery()
 * @method static Builder|LteLog query()
 * @method static Builder|LteLog whereCreatedAt($value)
 * @method static Builder|LteLog whereDetail($value)
 * @method static Builder|LteLog whereIcon($value)
 * @method static Builder|LteLog whereIp($value)
 * @method static Builder|LteLog whereLteUserId($value)
 * @method static Builder|LteLog whereMethod($value)
 * @method static Builder|LteLog whereRoute($value)
 * @method static Builder|LteLog whereSessionId($value)
 * @method static Builder|LteLog whereTitle($value)
 * @method static Builder|LteLog whereUrl($value)
 * @method static Builder|LteLog whereUserAgent($value)
 * @method static Builder|LteLog whereWebId($value)
 * @mixin Eloquent
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
     * @return HasOne
     */
    public function lte_user(): HasOne
    {
        return $this->hasOne(LteUser::class, 'id', 'lte_user_id');
    }
}
