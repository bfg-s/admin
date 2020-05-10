<?php

namespace Lar\LteAdmin\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * @param Model|string $model
     * @param array $data
     */
    public function __construct($model, array $data)
    {
        if (is_string($model)) {

            $model = new $model;
        }

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
     * @param $model
     * @param  array  $data
     * @return \Illuminate\Support\Collection
     */
    public static function doMany($model, array $data)
    {
        $results = collect();

        foreach ($data as $datum) {

            $results->push((new static($model, $datum))->save());
        }

        return $results;
    }

    /**
     * Save method
     *
     * @return bool|void|mixed
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

                    $builder = $this->model->{$key}();

                    if ($builder instanceof BelongsToMany) {

                        $builder->sync($param);
                    }

                    else if ($builder instanceof HasMany) {
                        if (isset($param[0]) && is_array($param[0])) {
                            $param = collect($param);
                            $params_with_id = $param->where('id');
                            $ids = $params_with_id->pluck('id')->toArray();
                            $has = $builder->whereIn('id', $ids)->get();
                            foreach ($params_with_id as $with_id_key => $with_id) {
                                if ($model = $has->where('id', $with_id['id'])->first()) {
                                    (new static($model, $with_id))->save();
                                } else {
                                    unset($ids[$with_id_key]);
                                }
                            }
                            foreach ($param->whereNotIn('id', $ids) as $item) {
                                (new static($this->model->{$key}(), $item))->save();
                            }
                        }
                        else {

                            (new static($this->model->{$key}(), $param))->save();
                        }
                    }

                    else {

                        (new static($this->model->{$key} ?? $builder, $param))->save();
                    }
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

                    $builder = $this->model->{$key}();

                    if ($builder instanceof BelongsToMany) {

                        $builder->sync($param);
                    }

                    else if ($builder instanceof HasMany) {
                        if (isset($param[0]) && is_array($param[0])) {
                            $param = collect($param);
                            $params_with_id = $param->where('id');
                            $ids = $params_with_id->pluck('id')->toArray();
                            $has = $builder->whereIn('id', $ids)->get();
                            foreach ($params_with_id as $with_id_key => $with_id) {
                                if ($model = $has->where('id', $with_id['id'])->first()) {
                                    (new static($model, $with_id))->save();
                                } else {
                                    unset($ids[$with_id_key]);
                                }
                            }
                            foreach ($param->whereNotIn('id', $ids) as $item) {
                                (new static($this->model->{$key}(), $item))->save();
                            }
                        }
                        else {

                            (new static($this->model->{$key}(), $param))->save();
                        }
                    }

                    else {

                        (new static($this->model->{$key} ?? $builder, $param))->save();
                    }
                }
            }

            return $this->model;
        }

        else {

            return $this->model;
        }
    }
}