<?php

use Composer\InstalledVersions;
use Illuminate\Support\Collection;

if (!request()->ajax() || request()->is('*dashboard*')) {
    if (class_exists(InstalledVersions::class)) {
        \LteAdmin\LteAdmin::$version = InstalledVersions::getPrettyVersion('lar/lte-admin');
    } else {
        $lock_file = base_path('composer.lock');
        if (is_file($lock_file)) {
            $lock = file_get_contents($lock_file);
            $json = json_decode($lock, 1);
            $admin = collect($json['packages'])->where('name', 'lar/lte-admin')->first();
            if ($admin && isset($admin['version'])) {
                \LteAdmin\LteAdmin::$version = ltrim($admin['version'], 'v');
            }
        }
    }
}

Collection::macro('nestable_pluck', function (
    string $value,
    string $key,
    $root = 'Root',
    string $order = 'order',
    string $parent_field = 'parent_id',
    string $input = '&nbsp;&nbsp;&nbsp;'
) {
    $nestable_count = function ($parent_id) use ($parent_field, &$nestable_count) {
        $int = 1;
        $parent = $this->where('id', $parent_id)->first();
        if ($parent->{$parent_field}) {
            $int += $nestable_count($parent->{$parent_field});
        }

        return $int;
    };

    /** @var Collection $return */
    $return = $this->sortBy($order)->mapWithKeys(static function ($item) use (
        $value,
        $key,
        $parent_field,
        $input,
        $nestable_count
    ) {
        $inp_cnt = 0;
        if ($item->{$parent_field}) {
            $inp_cnt += $nestable_count($item->{$parent_field});
        }

        return [$item->{$key} => str_repeat($input, $inp_cnt).$item->{$value}];
    });

    if ($root) {
        $return->prepend($root, 0);
    }

    return $return;
});
