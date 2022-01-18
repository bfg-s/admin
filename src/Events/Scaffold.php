<?php

namespace Lar\LteAdmin\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class Scaffold.
 * @package Lar\LteAdmin\Events
 */
class Scaffold
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var []
     */
    public $data;

    /**
     * @var string
     */
    public $table_name;

    /**
     * @var string
     */
    public $model;

    /**
     * @var string
     */
    public $controller;

    /**
     * @var array
     */
    public $create;

    /**
     * @var array
     */
    public $fields;

    /**
     * @var string
     */
    public $primary;

    /**
     * @var bool
     */
    public $created_at = false;

    /**
     * @var bool
     */
    public $updated_at = false;

    /**
     * @var bool
     */
    public $soft_delete = false;

    /**
     * Create a new event instance.
     *
     * @param  array  $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $this->table_name = $this->data['table_name'];
        $this->model = implode('\\', $this->data['model']);
        $this->controller = implode('\\', $this->data['controller']);
        $this->create = $this->data['create'];
        $this->fields = $this->data['fields'];
        $this->primary = $this->data['primary'];
        $this->created_at = $this->data['created_at'];
        $this->updated_at = $this->data['updated_at'];
        $this->soft_delete = $this->data['soft_delete'];
    }
}
