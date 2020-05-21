<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\LteAdmin\Segments\Tagable\Traits\FieldMassControl;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 * @mixin \Lar\LteAdmin\Core\FormGroupComponents
 */
class Form extends \Lar\LteAdmin\Components\Form {

    use FieldMassControl;

    /**
     * Form constructor.
     * @param  mixed  ...$params
     */
    public function __construct(...$params)
    {
        $closures = [];

        foreach ($params as $key => $param) {

            if ($param instanceof \Closure) {

                $closures[] = $param;

                unset($params[$key]);
            }
        }

        $params = array_values($params);

        parent::__construct(...$params);

        foreach ($closures as $closure) {

            $closure($this);
        }
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|Form|\Lar\Tagable\Tag|mixed|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if ($call = $this->call_group($name, $arguments)) {

            $call->setModel($this->model);

            return $call;
        }

        return parent::__call($name, $arguments);
    }
}