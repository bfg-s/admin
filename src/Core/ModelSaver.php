<?php

namespace Lar\LteAdmin\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Lar\LteAdmin\Models\LteFileStorage;

/**
 * Class ModelSaver
 * @package Lar\Developer\Core
 */
class ModelSaver
{
    /**
     * Save model
     *
     * @var Model
     */
    protected $model;

    /**
     * Save data
     *
     * @var array
     */
    protected $data;

    /**
     * ModelSaver constructor.
     *
     * @param Model $model
     * @param array $data
     */
    public function __construct($model, array $data)
    {
        $this->model = $model;

        $this->data = $data;
    }

    /**
     * @param $model
     * @param  array  $data
     * @return bool|void
     */
    public static function do($model, array $data)
    {
        return (new static($model, $data))->save();
    }

    /**
     * Save method
     *
     * @return bool|void
     */
    public function save()
    {
        foreach ($this->data as $key => $datum) {

            if (is_object($datum) && $datum instanceof UploadedFile) {

                $this->data[$key] = LteFileStorage::makeFile($datum);
            }
        }

        if ($this->model instanceof Model && $this->model->exists) {

            return $this->update_model();
        }

        else {

            return $this->create_model();
        }
    }

    /**
     * Update model
     *
     * @return bool|void
     */
    protected function update_model()
    {
        if ($result = $this->model->update($this->data)) {

            foreach ($this->data as $key => $param) {

                if (is_array($param) && method_exists($this->model, $key)) {

                    (new static($this->model->{$key} ?? $this->model->{$key}(), $param))->save();
                }
            }
        }

        return $result;
    }

    /**
     * Create model
     */
    protected function create_model()
    {
        if ($this->model = $this->model->create($this->data)) {

            foreach ($this->data as $key => $param) {

                if (is_array($param) && method_exists($this->model, $key)) {

                    (new static($this->model->{$key} ?? $this->model->{$key}(), $param))->save();
                }
            }

            return $this->model;
        }

        else {

            return $this->model;
        }
    }
}