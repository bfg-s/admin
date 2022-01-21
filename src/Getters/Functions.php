<?php

namespace Lar\LteAdmin\Getters;

use Lar\Developer\Getter;
use Lar\LteAdmin\Models\LteFunction;

class Functions extends Getter
{
    /**
     * @var string
     */
    public static $name = 'lte.functions';

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|\Lar\LteAdmin\Models\LteFunction[]
     */
    public static function list()
    {
        return LteFunction::where('active', 1)->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function default()
    {
        return collect([]);
    }
}
