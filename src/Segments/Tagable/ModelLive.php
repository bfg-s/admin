<?php

namespace Lar\LteAdmin\Segments\Tagable;

/**
 * Class ModelLive
 * @package Lar\LteAdmin\Segments\Tagable
 */
class ModelLive extends Live {

    /**
     * Live constructor.
     * @param ...$params
     */
    public function __construct(string $path, $need_value, ...$params)
    {
        parent::__construct();

        if ($need_value instanceof \Closure) {

            $params[] = $need_value;
            $need_value = null;
        }

        $model = Form::$current_model;

        $request_value = request($path);

        $value = old($path, ($model ? (multi_dot_call($model, $path, false) ?? $request_value): $request_value));

        if ($need_value == $value) {

            $this->when($params);
        }
    }
}
