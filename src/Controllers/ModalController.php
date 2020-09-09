<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Lar\Layout\Core\HTMLCustomCommand;
use Lar\LteAdmin\Segments\Modal;

/**
 * Class ModalController
 * @package Lar\LteAdmin\Controllers
 */
class ModalController extends HTMLCustomCommand
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var array
     */
    protected $size;

    /**
     * @var string
     */
    protected $backdrop = 'static';

    /**
     * @var bool
     */
    protected $focus = true;

    /**
     * @var bool
     */
    protected $escape = false;

    /**
     * @var string
     */
    protected $method = "index";

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $handle;

    /**
     * @var array
     */
    protected $create;

    /**
     * ModalController constructor.
     * @param  array  $params
     */
    public function __construct(array $params = [])
    {
        $this->class = static::class;
        $this->params = $params;
    }

    /**
     * @param  string|null  $method
     * @return $this
     */
    public function setMethod(string $method = null)
    {
        if ($method) {

            $this->method = $method;
        }

        else {

            $this->method = "index";
        }

        return $this;
    }

    /**
     * @param  string|null  $handle
     * @return $this
     */
    public function setClass(string $handle = null)
    {
        if ($handle) {

            $this->class = $handle;
        }

        else {

            $this->class = static::class;
        }

        return $this;
    }

    /**
     * @param  string|null  $handle
     * @return $this
     */
    public function setHandle(string $handle = null)
    {
        if ($handle) {

            $this->handle = $handle;
        }

        return $this;
    }

    /**
     * @param  array|null  $create
     * @return $this
     */
    public function setCreate(array $create = null)
    {
        if ($create) {

            $this->create = $create;
        }

        return $this;
    }

    /**
     * @param  array  $create
     * @return $this
     */
    public function createWith(array $create)
    {
        $this->create = $create;

        return $this;
    }

    /**
     * @return Modal
     */
    public function index() {

        $link = [$this, 'create'];

        if ($this->create) {

            $link = $this->create;
        }

        return Modal::create($link);
    }

    /**
     * @param $value
     * @return $this
     */
    public function backdrop($value)
    {
        $this->backdrop = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function escape()
    {
        $this->escape = true;

        return $this;
    }

    /**
     * Extra big size
     * @return $this
     */
    public function extra()
    {
        $this->size = 'extra';

        return $this;
    }

    /**
     * Big size
     * @return $this
     */
    public function big()
    {
        $this->size = 'big';

        return $this;
    }

    /**
     * Small size
     * @return $this
     */
    public function small()
    {
        $this->size = 'small';

        return $this;
    }

    /**
     * @return array[]
     */
    public function toArray()
    {
        return [
            'modal:put' => [
                $this->handle ? $this->handle : "{$this->class}@{$this->method}",
                $this->params,
                [
                    'backdrop' => $this->backdrop,
                    'focus' => $this->focus,
                    'keyboard' => $this->escape,
                    'size' => $this->size
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function render()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}