<?php

namespace Lar\LteAdmin\Getters;

use Illuminate\Support\Collection;
use Lar\Developer\Getter;
use Lar\LteAdmin\Models\LteFunction;

class Functions extends Getter
{
    /**
     * @var string
     */
    public static $name = 'lte.functions';

    /**
     * @return \Illuminate\Database\Eloquent\Collection|Collection|LteFunction[]
     */
    public static function list()
    {
        return LteFunction::where('active', 1)->get();
    }

    /**
     * @return Collection
     */
    public function default()
    {
        return collect([]);
    }
}
