<?php

namespace Admin\Resources;

use Bfg\Resource\BfgResource;
use Bfg\Resource\Traits\Model\ModelTimestampsTrait;

/**
 * Resource for the admin user model.
 */
class AdminUserResource extends BfgResource
{
    use ModelTimestampsTrait;

    /**
     * Map of resource fields
     *
     * @var array
     */
    protected array $map = [
        'id',
        'login',
        'email',
        'name',
        'avatar',
        'two_factor_confirmed_at',
        'roles' => AdminRoleResource::class,
        'updated_at',
        'created_at',
    ];
}
