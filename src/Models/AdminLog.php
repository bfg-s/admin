<?php

namespace Admin\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Admin\Traits\DumpedModel;

/**
 * Admin\Models\LteLog.
 *
 * @property string $title
 * @property string|null $detail
 * @property string $ip
 * @property string|null $url
 * @property string|null $route
 * @property string|null $method
 * @property string $user_agent
 * @property string $session_id
 * @property int|null $admin_user_id
 * @property int|null $web_id
 * @property string|null $icon
 * @property Carbon|null $created_at
 * @property-read \App\Models\LteUser|null $admin_user
 * @method static Builder|AdminLog makeDumpedModel()
 * @method static Builder|AdminLog newModelQuery()
 * @method static Builder|AdminLog newQuery()
 * @method static Builder|AdminLog query()
 * @method static Builder|AdminLog whereCreatedAt($value)
 * @method static Builder|AdminLog whereDetail($value)
 * @method static Builder|AdminLog whereIcon($value)
 * @method static Builder|AdminLog whereIp($value)
 * @method static Builder|AdminLog whereLteUserId($value)
 * @method static Builder|AdminLog whereMethod($value)
 * @method static Builder|AdminLog whereRoute($value)
 * @method static Builder|AdminLog whereSessionId($value)
 * @method static Builder|AdminLog whereTitle($value)
 * @method static Builder|AdminLog whereUrl($value)
 * @method static Builder|AdminLog whereUserAgent($value)
 * @method static Builder|AdminLog whereWebId($value)
 * @mixin Eloquent
 */
class AdminLog extends Model
{
    use DumpedModel;

    public const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'admin_logs';

    /**
     * @var array
     */
    protected $fillable = [
        'icon', 'admin_user_id', 'title', 'detail', 'ip', 'url', 'route', 'method', 'session_id', 'user_agent', 'web_id',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'icon' => 'string',
        'admin_user_id' => 'int',
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
    public function admin_user(): HasOne
    {
        return $this->hasOne(AdminUser::class, 'id', 'admin_user_id');
    }
}
