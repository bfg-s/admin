<?php

namespace Lar\LteAdmin\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

trait DumpedModel
{
    /**
     * @param  Builder  $q
     * @return array|false
     */
    public function scopeMakeDumpedModel(Builder $q)
    {
        if (method_exists($this, 'initializeSoftDeletes')) {
            $q = $q->withTrashed();
        }

        if (method_exists($this, 'toDump')) {
            return $q->get()->map(static function ($model) {
                return $model->toDump();
            })->toArray();
        }

        $result = $q->get()->toArray();

        return count($result) ? $result : false;
    }
}
