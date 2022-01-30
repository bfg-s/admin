<?php

namespace LteAdmin\Getters;

use Illuminate\Support\Collection;
use Lar\Developer\Getter;

class Role extends Getter
{
    /**
     * @var string
     */
    public static $name = 'lte.role';

    /**
     * @return Collection
     */
    public function default()
    {
        return collect([]);
    }
}
