<?php

namespace Admin\Resources;

use Bfg\Resource\BfgResource;
use Bfg\Resource\Traits\Model\ModelTimestampsTrait;

/**
 * Resource for the admin log model.
 */
class AdminLogResource extends BfgResource
{
    use ModelTimestampsTrait;

    /**
     * Map of resource fields
     *
     * @var array
     */
    protected array $map = [
        'id',
        'icon',
        'title',
        'detail',
        'ip',
        'url',
        'route',
        'method',
        'session_id',
        'user_agent',
        'web_id',
        'admin_user' => AdminUserResource::class,
        'created_at',
    ];
}
