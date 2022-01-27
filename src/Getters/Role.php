<?php

namespace Lar\LteAdmin\Getters;

use Illuminate\Support\Collection;
use Lar\Developer\Getter;

class Role extends Getter
{
    /**
     * @var string
     */
    public static $name = 'lte.role';

    public static function functions()
    {
    }

    /**
     * @return Collection
     */
    public function default()
    {
        return collect([]);
    }
}
