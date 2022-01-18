<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\LteAdmin\Core\Traits\Delegable;

/**
 * Class ModelLive.
 * @package Lar\LteAdmin\Segments\Tagable
 */
class ModelLive extends Live
{
    use Delegable;

    const NOT_NULL = 'NOT_NULL';
    const NULL = 'NULL';
    const TRUE = 'TRUE';
    const FALSE = 'FALSE';

    /**
     * @var mixed|null
     */
    public $value = null;

    /**
     * Live constructor.
     * @param ...$params
     */
    public function __construct(string $path, $need_value, ...$params)
    {
        parent::__construct(true);

        if ($need_value instanceof \Closure) {
            $params[] = $need_value;
            $need_value = null;
        }

        $model = Form::$current_model;

        $request_value = multi_dot_call(request()->all(), $path);

        $this->value = old($path, $request_value ?: ($model ? multi_dot_call($model, $path, false) : null));

        $this->conditions($need_value, $params);
    }

    /**
     * @param $need_value
     * @param $params
     */
    protected function conditions($need_value, $params)
    {
        if ($need_value === static::NOT_NULL) {
            $can = $this->value != null;
        } elseif ($need_value === static::NULL) {
            $can = $this->value == null;
        } elseif ($need_value === static::TRUE) {
            $can = (bool) $this->value === true;
        } elseif ($need_value === static::FALSE) {
            $can = (bool) $this->value === false;
        } elseif (is_array($need_value) && isset($need_value['IN'])) {
            $can = in_array($this->value, (array) $need_value['IN']);
        } elseif (is_array($need_value) && isset($need_value['NOT_IN'])) {
            $can = ! in_array($this->value, (array) $need_value['NOT_IN']);
        } else {
            $can = $need_value == $this->value;
        }

        if ($can) {
            $this->when($params);
        }
    }

    /**
     * @param ...$select
     * @return array
     */
    public static function IN(...$select)
    {
        return ['IN' => $select];
    }

    /**
     * @param ...$select
     * @return array
     */
    public static function NOT_IN(...$select)
    {
        return ['NOT_IN' => $select];
    }
}
