<?php

declare(strict_types=1);

namespace Admin\Traits;

use Illuminate\Database\Eloquent\Model;

trait SearchFormConditionRulesTrait
{
    /**
     * @param  mixed  $model
     * @return mixed
     */
    public function makeModel(mixed $model): mixed
    {
        $r = request()->has('q') ? request('q') : [];

        if (is_array($r)) {

            foreach ($this->fields as $field) {

                if (! isset($r[$field['field_name']])) {
                    $val = $field['class']->getValue() ?: $field['class']->getDefault();
                    if ($val) {
                        $r[$field['field_name']] = $val;
                    }
                }

            }
        }

        if ($r) {

            if (is_string($r)) {
                if ($this->global_search_fields) {
                    $i = 0;
                    foreach ($this->global_search_fields as $global_search_field) {
                        $find = collect($this->fields)->where('field_name', $global_search_field)->first();
                        if ($find && (!isset($find['method']) || !is_embedded_call($find['method']))) {
                            if ($i) {
                                $model = $model->orWhere($global_search_field, 'like', "%{$r}%");
                            } else {
                                $model = $model->where($global_search_field, 'like', "%{$r}%");
                            }
                            $i++;
                        }
                    }
                } else {
                    $model = $model->orWhere(function ($q) use ($r) {
                        foreach ($this->fields as $field) {
                            if (!str_ends_with($field['field_name'], '_at')) {
                                $q = $q->orWhere($field['field_name'], 'like', "%{$r}%");
                            }
                        }
                        return $q;
                    });
                }
            } elseif (is_array($r)) {

                foreach ($r as $key => $val) {
                    if ($val != null) {
                        foreach ($this->fields as $field) {
                            if ($field['field_name'] === $key) {
                                $val = method_exists($field['class'], 'transformValue') ?
                                    $field['class']::transformValue($val) :
                                    $val;

                                if (is_embedded_call($field['method'])) {
                                    $result = call_user_func($field['method'], $model, $val, $key);

                                    if ($result) {
                                        $model = $result;
                                    }
                                } else {
                                    $model = $this->{$field['method']}(
                                        $model,
                                        $val,
                                        $key
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }

        return $model;
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function equally(mixed $model, $value, $key): mixed
    {
        return $model->where($key, '=', $value);
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function not_equal(mixed $model, $value, $key): mixed
    {
        return $model->where($key, '!=', $value);
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function more_or_equal(mixed $model, $value, $key): mixed
    {
        return $model->where($key, '>=', $value);
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function less_or_equal(mixed $model, $value, $key): mixed
    {
        return $model->where($key, '<=', $value);
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function more(mixed $model, $value, $key): mixed
    {
        return $model->where($key, '>', $value);
    }

    /**
     * @param  Model  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function less(mixed $model, $value, $key): mixed
    {
        return $model->where($key, '<', $value);
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function like_right(mixed $model, $value, $key): mixed
    {
        return $model->where($key, 'like', '%'.$value);
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function like_left(mixed $model, $value, $key): mixed
    {
        return $model->where($key, 'like', $value.'%');
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function like_any(mixed $model, $value, $key): mixed
    {
        return $model->where($key, 'like', '%'.$value.'%');
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function nullable(mixed $model, $value, $key): mixed
    {
        if ($value) {
            return $model->whereNull($key);
        }

        return $model;
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function not_nullable(mixed $model, $value, $key): mixed
    {
        if ($value) {
            return $model->whereNotNull($key);
        }

        return $model;
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function where_in(mixed $model, $value, $key): mixed
    {
        return $model->whereIn($key, $value);
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function where_not_in(mixed $model, $value, $key): mixed
    {
        return $model->whereNotIn($key, $value);
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function where_between(mixed $model, $value, $key): mixed
    {
        return $model->whereBetween($key, $value);
    }

    /**
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function where_not_between(mixed $model, $value, $key): mixed
    {
        return $model->whereNotBetween($key, $value);
    }
}
