<?php

declare(strict_types=1);

namespace Admin\Traits;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait with conditions for the search form.
 */
trait SearchFormConditionRulesTrait
{
    /**
     * Build a query to the model using selected fields and search form conditions.
     *
     * @param  mixed  $model
     * @return mixed
     */
    public function makeModel(mixed $model): mixed
    {
        $requestQuery = request('q');

        if (is_array($requestQuery)) {

            foreach ($this->contents as $field) {

                if (! isset($requestQuery[$field['field_name']])) {
                    $val = $field['class']->getValue() ?: $field['class']->getDefault();
                    if ($val) {
                        $requestQuery[$field['field_name']] = $val;
                    }
                }
            }
        }

        if ($requestQuery) {

            if (is_string($requestQuery)) {
                if ($this->global_search_fields) {
                    $i = 0;
                    foreach ($this->global_search_fields as $global_search_field) {
                        $find = collect($this->contents)->where('field_name', $global_search_field)->first();
                        if ($find && (!isset($find['method']) || !is_embedded_call($find['method']))) {
                            if ($i) {
                                $model = $model->orWhere($global_search_field, 'like', "%{$requestQuery}%");
                            } else {
                                $model = $model->where($global_search_field, 'like', "%{$requestQuery}%");
                            }
                            $i++;
                        }
                    }
                } else {
                    $model = $model->orWhere(function ($q) use ($requestQuery) {
                        foreach ($this->contents as $field) {
                            if (!str_ends_with($field['field_name'], '_at')) {
                                $q = $q->orWhere($field['field_name'], 'like', "%{$requestQuery}%");
                            }
                        }
                        return $q;
                    });
                }
            } elseif (is_array($requestQuery)) {

                foreach ($requestQuery as $key => $val) {
                    if ($val != null) {
                        foreach ($this->contents as $field) {
                            if (trim($field['field_name'], '[]') === $key) {
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
     * Condition for "=" symbol.
     *
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
     * Condition for "!=" symbol.
     *
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
     * Condition for ">=" symbol.
     *
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
     * Condition for "<=" symbol.
     *
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
     * Condition for ">" symbol.
     *
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
     * Condition for "<" symbol.
     *
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
     * Condition for "%=" symbol.
     *
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
     * Condition for "=%" symbol.
     *
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
     * Condition for "%=%" symbol.
     *
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
     * Condition for "%json" symbol.
     *
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function like_right_json(mixed $model, $value, $key): mixed
    {
        return $model->whereRaw(
            "CONVERT(JSON_UNQUOTE(JSON_EXTRACT($key, '$')) USING utf8mb4) COLLATE utf8mb4_general_ci LIKE ?",
            ['%'.$value]
        );
    }

    /**
     * Condition for "json%" symbol.
     *
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function like_left_json(mixed $model, $value, $key): mixed
    {
        return $model->whereRaw(
            "CONVERT(JSON_UNQUOTE(JSON_EXTRACT($key, '$')) USING utf8mb4) COLLATE utf8mb4_general_ci LIKE ?",
            [$value.'%']
        );
    }

    /**
     * Condition for "%json%" symbol.
     *
     * @param  mixed  $model
     * @param $key
     * @param $value
     * @return mixed
     */
    protected function like_any_json(mixed $model, $value, $key): mixed
    {
        return $model->whereRaw(
            "CONVERT(JSON_UNQUOTE(JSON_EXTRACT($key, '$')) USING utf8mb4) COLLATE utf8mb4_general_ci LIKE ?",
            ['%'.$value.'%']
        );
    }

    /**
     * Condition for "null" symbol.
     *
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
     * Condition for "not_null" symbol.
     *
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
     * Condition for "in" symbol.
     *
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
     * Condition for "not_in" symbol.
     *
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
     * Condition for "between" symbol.
     *
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
     * Condition for "not_between" symbol.
     *
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
