<?php

namespace Lar\LteAdmin\Core;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Lar\Developer\Core\Traits\Eventable;
use Lar\LteAdmin\Models\LteFileStorage;

/**
 * Class ModelSaver
 * @package Lar\Developer\Core
 */
class ModelSaver
{
    use Eventable;

    const DELETE_FIELD = "__DELETE__";

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
     * @var bool
     */
    protected $has_delete = false;

    /**
     * @var callable[]
     */
    protected static $on_save = [];

    /**
     * @var callable[]
     */
    protected static $on_saved = [];

    /**
     * @var callable[]
     */
    protected static $on_create = [];

    /**
     * @var callable[]
     */
    protected static $on_created = [];

    /**
     * @var callable[]
     */
    protected static $on_update = [];

    /**
     * @var callable[]
     */
    protected static $on_updated = [];

    /**
     * @var callable[]
     */
    protected static $on_delete = [];

    /**
     * @var callable[]
     */
    protected static $on_deleted = [];

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
     * @param  array|Arrayable  $data
     * @return \Illuminate\Support\Collection
     */
    public static function doMany($model, $data)
    {
        $results = collect();

        foreach ($data as $datum) {

            if ($datum instanceof Arrayable) {

                $datum = $datum->toArray();
            }

            if (is_array($datum) && count($datum)) {

                $results->push((new static($model, $datum))->save());
            }
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

        list($data, $add) = $this->getDatas();

        if ($this->model instanceof Model) {

            if ($this->model->exists) {

                return $this->update_model($data, $add);
            }

            else if (isset($data['id']) && $m = $this->model->find($data['id'])) {

                $this->model = $m;

                return $this->update_model($data, $add);
            }

            else {

                return $this->create_model($data, $add);
            }
        }

        else {

            return $this->create_model($data, $add);
        }
    }

    /**
     * Update model
     *
     * @param $data
     * @param $add
     * @return bool|void
     */
    protected function update_model($data, $add)
    {
        if ($this->has_delete) {
            $this->call_on('on_delete', $data);
            $return = $this->model->delete();
            $this->call_on('on_deleted', $data);
            return $return;
        }

        $this->call_on('on_save', $data);
        $this->call_on('on_update', $data);

        $result = $this->model->update($data);

        $this->call_on('on_saved', $result);
        $this->call_on('on_updated', $result);

        if ($result) {

            foreach ($add as $key => $param) {

                if (is_array($param) && method_exists($this->model, $key)) {

                    $builder = $this->model->{$key}();

                    if ($builder instanceof BelongsToMany) {

                        $builder->sync($param);
                    }

                    else if ($builder instanceof HasMany) {
                        if (is_array($param) && isset($param[array_key_first($param)]) && is_array($param[array_key_first($param)])) {
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
     * @param $data
     * @param $add
     * @return Model
     */
    protected function create_model($data, $add)
    {
        $this->call_on('on_save', $data);
        $this->call_on('on_create', $data);

        $this->model = $this->model->create($data);

        if ($this->model) {

            $this->call_on('on_saved', $this->model);
            $this->call_on('on_created', $this->model);

            foreach ($add as $key => $param) {

                if (is_array($param) && method_exists($this->model, $key)) {

                    $builder = $this->model->{$key}();

                    if ($builder instanceof BelongsToMany) {

                        $builder->sync($param);
                    }

                    else if ($builder instanceof HasMany) {
                        if (is_array($param) && isset($param[array_key_first($param)]) && is_array($param[array_key_first($param)])) {
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

    /**
     * @return array
     */
    protected function getFields()
    {
        $table = $this->getModelTable();

        if (!$table) {

            return [];
        }

        $fields = $this->model->getConnection()->getSchemaBuilder()->getColumnListing($table);

        return $fields;
    }

    /**
     * @return array[]
     */
    protected function getDatas()
    {
        $data = $this->data;
        $key = $this->getModelKeyName();
        if (
            isset($data[static::DELETE_FIELD]) &&
            isset($data[$key]) &&
            $data[static::DELETE_FIELD] == $data[$key]
        ) {
            $this->has_delete = true;
            return [[], []];
        }
        $nullable = $this->getNullableFields();
        $result = [[]];
        foreach ($this->getFields() as $field) {
            if (isset($data[$field])) {
                if ($data[$field] !== '') {
                    $result[0][$field] = $data[$field];
                } else if (isset($nullable[$field]) && $nullable[$field]) {
                    $result[0][$field] = null;
                } else {
                    $result[0][$field] = $data[$field];
                }
                unset($data[$field]);
            }
        }
        $result[1] = $data;
        return $result;
    }

    /**
     * @return array
     */
    protected function getNullableFields()
    {
        $table = $this->getModelTable();

        if (!$table) {

            return [];
        }

        $fields = \DB::select(
            "SELECT COL.COLUMN_NAME, COL.IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS COL WHERE COL.TABLE_NAME = '{$table}'"
        );

        $clear_fields = [];

        foreach ($fields as $field) {

            $clear_fields[$field->COLUMN_NAME] = $field->IS_NULLABLE === 'YES';
        }

        return $clear_fields;
    }

    /**
     * @return string|null
     */
    public function getModelKeyName()
    {
        $key = null;
        $model = $this->getModel();
        if ($model) $key = $model->getKeyName();

        return $key;
    }

    /**
     * @return string|null
     */
    public function getModelTable()
    {
        $table = null;
        $model = $this->getModel();
        if ($model) $table = $model->getTable();

        return $table;
    }

    /**
     * @return Model|null
     */
    public function getModel()
    {
        $model = null;

        if ($this->model instanceof Relation) {

            $model = $this->model->getModel();

        } else if ($this->model instanceof Model) {

            $model = $this->model;

        } else if ($this->model instanceof Builder) {

            $model = $this->model->getModel();
        }

        return $model;
    }

    /**
     * @param  string  $model
     * @param  callable  $call
     */
    public static function on_save(string $model, callable $call)
    {
        static::$on_save[$model][] = $call;
    }

    /**
     * @param  string  $model
     * @param  callable  $call
     */
    public static function on_saved(string $model, callable $call)
    {
        static::$on_saved[$model][] = $call;
    }

    /**
     * @param  string  $model
     * @param  callable  $call
     */
    public static function on_create(string $model, callable $call)
    {
        static::$on_create[$model][] = $call;
    }

    /**
     * @param  string  $model
     * @param  callable  $call
     */
    public static function on_created(string $model, callable $call)
    {
        static::$on_created[$model][] = $call;
    }

    /**
     * @param  string  $model
     * @param  callable  $call
     */
    public static function on_update(string $model, callable $call)
    {
        static::$on_update[$model][] = $call;
    }

    /**
     * @param  string  $model
     * @param  callable  $call
     */
    public static function on_updated(string $model, callable $call)
    {
        static::$on_updated[$model][] = $call;
    }

    /**
     * @param  string  $model
     * @param  callable  $call
     */
    public static function on_delete(string $model, callable $call)
    {
        static::$on_delete[$model][] = $call;
    }

    /**
     * @param  string  $model
     * @param  callable  $call
     */
    public static function on_deleted(string $model, callable $call)
    {
        static::$on_deleted[$model][] = $call;
    }

    /**
     * @param  string  $name
     * @param  mixed  ...$params
     */
    protected function call_on(string $name, ...$params)
    {
        $events = static::$$name;
        $model = $this->getModel();
        $class = $model ? get_class($model) : false;

        if ($class && isset($events[$class])) {
            foreach ($events[$class] as $item) {
                call_user_func_array($item, $params);
            }
        }
    }
}