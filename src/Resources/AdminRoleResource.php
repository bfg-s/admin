<?php

namespace Admin\Resources;

use Bfg\Resource\BfgResource;
use Bfg\Resource\Traits\Model\ModelTimestampsTrait;

/**
 * Resource for the admin role model.
 */
class AdminRoleResource extends BfgResource
{
    use ModelTimestampsTrait;

    /**
     * Map of resource fields
     *
     * @var array
     */
    protected array $map = [
        'id',
        'name',
        'slug',
        'updated_at',
        'created_at',
    ];
}
