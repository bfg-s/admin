<?php

namespace Lar\LteAdmin\Segments\Tagable\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait SearchFormConditionRulesTrait
 * @package Lar\LteAdmin\Segments\Tagable\Traits
 */
trait SearchFormConditionRulesTrait {

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function equally($model, $key, $value)
    {
        return $model->where($key, '=', $value);
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function not_equal($model, $key, $value)
    {
        return $model->where($key, '!=', $value);
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function more_or_equal($model, $key, $value)
    {
        return $model->where($key, '>=', $value);
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function less_or_equal($model, $key, $value)
    {
        return $model->where($key, '<=', $value);
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function more($model, $key, $value)
    {
        return $model->where($key, '>', $value);
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function less($model, $key, $value)
    {
        return $model->where($key, '<', $value);
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function like_right($model, $key, $value)
    {
        return $model->where($key, 'like', "%" . $value);
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function like_left($model, $key, $value)
    {
        return $model->where($key, 'like', $value . "%");
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function like_any($model, $key, $value)
    {
        return $model->where($key, 'like', "%" . $value . "%");
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function nullable($model, $key, $value)
    {
        if ($value) {

            return $model->whereNull($key);
        }

        return $model;
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function not_nullable($model, $key, $value)
    {
        if ($value) {

            return $model->whereNotNull($key);
        }

        return $model;
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function where_in($model, $key, $value)
    {
        return $model->whereIn($key, $value);
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function where_not_in($model, $key, $value)
    {
        return $model->whereNotIn($key, $value);
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function where_between($model, $key, $value)
    {
        return $model->whereBetween($key, $value);
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return Model
     */
    protected function where_not_between($model, $key, $value)
    {
        return $model->whereNotBetween($key, $value);
    }

    /**
     * @param  Model  $model
     * @return Model
     */
    public function makeModel($model)
    {
        if (request()->has('q')) {
            $r = request('q');
            foreach ($r as $key => $val) {
                if ($val) {
                    foreach ($this->fields as $field) {
                        if ($field['field_name'] === $key) {
                            $model = $this->{$field['method']}(
                                $model,
                                $key,
                                method_exists($field['class'], 'transformValue') ?
                                    $field['class']::transformValue($val) :
                                    $val
                            );
                        }
                    }
                }
            }
        }
        
        return $model;
    }
}