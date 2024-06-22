<?php

namespace Admin\Resources;

use Bfg\Resource\BfgResource;
use Bfg\Resource\Traits\Model\ModelTimestampsTrait;

/**
 * Resource for the admin permission model.
 */
class AdminPermissionResource extends BfgResource
{
    use ModelTimestampsTrait;

    /**
     * Map of resource fields
     *
     * @var array
     */
    protected array $map = [
        'id',
        'path',
        'method',
        'state',
        'description',
        'admin_role_id',
        'active',
        'role' => AdminRoleResource::class,
        'updated_at',
        'created_at',
    ];
}
