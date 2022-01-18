<?php

namespace Lar\LteAdmin\Core;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\UploadedFile;
use Lar\Developer\Core\Traits\Eventable;
use Lar\LteAdmin\Models\LteFileStorage;

/**
 * Class ModelSaver.
 * @package Lar\Developer\Core
 */
class ModelSaver
{
    use Eventable;

    const DELETE_FIELD = '__DELETE__';

    /**
     * Save model.
     *
     * @var Model
     */
    protected $model;

    /**
     * Save data.
     *
     * @var array
     */
    protected $data;

    /**
     * @var bool
     */
    protected $has_delete = false;

    /**
     * @var mixed
     */
    protected $src = null;

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
    protected static $on_finish = [];

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
     * @template T
     * @param T $model
     * @param  array  $data
     * @return T|Model|bool|mixed
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
     * Save method.
     *
     * @return Model|bool|mixed
     */
    public function save()
    {
        list($data, $add) = $this->getDatas();

        if ($this->model instanceof Model) {
            if ($this->model->exists) {
                return $this->update_model($data, $add);
            } elseif (isset($data['id']) && $m = $this->model->find($data['id'])) {
                $this->model = $m;

                return $this->update_model($data, $add);
            } else {
                return $this->create_model($data, $add);
            }
        } else {
            return $this->create_model($data, $add);
        }
    }

    /**
     * @param $src
     * @return $this
     */
    public function setSrc($src)
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Update model.
     *
     * @param $data
     * @param $add
     * @return bool|void
     */
    protected function update_model($data, $add)
    {
        if ($this->has_delete) {
            $this->call_on('on_delete', $this->data, $this->model);
            $return = $this->model->delete();
            $this->call_on('on_deleted', $this->data, $this->model);

            return $return;
        }

        $r1 = $this->call_on('on_save', $this->data, $this->model);
        $r2 = $this->call_on('on_update', $this->data, $this->model);

        $event_data = array_merge($r1, $r2);

        if ($event_data) {
            $this->data = array_merge($this->data, $event_data);

            list($data, $add) = $this->getDatas();
        }

        $result = $this->model->update(array_merge($data, $event_data));

        if ($result) {
            $this->call_on('on_saved', $this->data, $result);
            $this->call_on('on_updated', $this->data, $result);

            foreach ($add as $key => $param) {
                if (is_array($param) && method_exists($this->model, $key)) {
                    if (isset($param[static::DELETE_FIELD])) {
                        unset($param[static::DELETE_FIELD]);
                    }

                    $builder = $this->model->{$key}();

                    if ($builder instanceof BelongsToMany) {
                        $fk = array_key_first($param);
                        $fv = $param[$fk];

                        if (is_array($fv)) {
                            $param = collect($param)->filter(function ($i) {
                                return ! isset($i[static::DELETE_FIELD]);
                            })->map(function ($i) {
                                $lk = array_key_last($i);

                                return $i[$lk];
                            })->values()->toArray();
                        }

                        $builder->sync($param);
                    } elseif ($builder instanceof HasMany || $builder instanceof MorphMany || $builder instanceof MorphToMany) {
                        if (is_array($param) && isset($param[array_key_first($param)]) && is_array($param[array_key_first($param)])) {
                            $param = collect($param);
                            $params_with_id = $param->where('id');
                            $ids = $params_with_id->pluck('id')->toArray();
                            $has = $builder->whereIn('id', $ids)->get();
                            foreach ($params_with_id as $with_id_key => $with_id) {
                                if ($model = $has->where('id', $with_id['id'])->first()) {
                                    (new static($model, $with_id))->setSrc($builder)->save();
                                } else {
                                    unset($ids[$with_id_key]);
                                }
                            }
                            foreach ($param->whereNotIn('id', $ids) as $item) {
                                (new static($this->model->{$key}(), $item))->setSrc($builder)->save();
                            }
                        } else {
                            (new static($this->model->{$key}(), $param))->setSrc($builder)->save();
                        }
                    } else {
                        (new static($this->model->{$key} ?? $builder, $param))->setSrc($builder)->save();
                    }
                }
            }

            $this->call_on('on_finish', $this->data, $this->model);
        }

        return $result;
    }

    /**
     * Create model.
     * @param $data
     * @param $add
     * @return Model
     */
    protected function create_model($data, $add)
    {
        $r1 = $this->call_on('on_save', $this->data, $this->model);
        $r2 = $this->call_on('on_create', $this->data, $this->model);

        $event_data = array_merge($r1, $r2);

        if ($event_data) {
            $this->data = array_merge($this->data, $event_data);

            list($data, $add) = $this->getDatas();
        }

        $data_for_create = array_merge($data, $event_data);

        $this->model = $this->model->create($data_for_create);

        if ($this->src instanceof HasOne) {
            $local_parent_relation = $this->src->getLocalKeyName();

            $parent = $this->src->getParent();

            if (
                $local_parent_relation != $parent->getKeyName()
            ) {
                $parent->update([
                    $local_parent_relation => $this->model->{$this->model->getKeyName()},
                ]);
            }
        }

        if ($this->model) {
            $this->call_on('on_saved', $this->data, $this->model);
            $this->call_on('on_created', $this->data, $this->model);

            foreach ($add as $key => $param) {
                if (is_array($param) && method_exists($this->model, $key)) {
                    if (isset($param[static::DELETE_FIELD])) {
                        unset($param[static::DELETE_FIELD]);
                    }

                    $builder = $this->model->{$key}();

                    if ($builder instanceof BelongsToMany) {
                        $fk = array_key_first($param);
                        $fv = $param[$fk];

                        if (is_array($fv)) {
                            $param = collect($param)->filter(function ($i) {
                                return ! isset($i[static::DELETE_FIELD]);
                            })->map(function ($i) {
                                $lk = array_key_last($i);

                                return $i[$lk];
                            })->values()->toArray();
                        }

                        $builder->sync($param);
                    } elseif (
                        $builder instanceof HasMany ||
                        $builder instanceof MorphMany ||
                        $builder instanceof MorphToMany ||
                        $builder instanceof HasManyThrough
                    ) {
                        if (is_array($param) && isset($param[array_key_first($param)]) && is_array($param[array_key_first($param)])) {
                            $param = collect($param);
                            $params_with_id = $param->where('id');
                            $ids = $params_with_id->pluck('id')->toArray();
                            $has = $builder->whereIn('id', $ids)->get();
                            foreach ($params_with_id as $with_id_key => $with_id) {
                                if ($model = $has->where('id', $with_id['id'])->first()) {
                                    (new static($model, $with_id))->setSrc($builder)->save();
                                } else {
                                    unset($ids[$with_id_key]);
                                }
                            }
                            foreach ($param->whereNotIn('id', $ids) as $item) {
                                (new static($this->model->{$key}(), $item))->setSrc($builder)->save();
                            }
                        } else {
                            (new static($this->model->{$key}(), $param))->setSrc($builder)->save();
                        }
                    } else {
                        (new static($this->model->{$key} ?? $builder, $param))->setSrc($builder)->save();
                    }
                }
            }

            $this->call_on('on_finish', $this->data, $this->model);

            return $this->model;
        } else {
            return $this->model;
        }
    }

    /**
     * Insert relations when model creating.
     * @param  array  $data
     * @return array
     */
    protected function insert_relations(array $data)
    {

//        foreach ($data as $key => $datum) {
//
//        }

        return $data;
    }

    /**
     * @return array
     */
    protected function getFields()
    {
        $table = $this->getModelTable();

        if (! $table) {
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
        $data = [];
        foreach ($this->data as $key => $datum) {
            if (is_object($datum) && $datum instanceof UploadedFile) {
                $data[$key] = LteFileStorage::makeFile($datum);
            } else {
                $data[$key] = $datum;
            }
        }
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
            if (array_key_exists($field, $data)) {
                if ($data[$field] !== '') {
                    $result[0][$field] = $data[$field];
                } elseif (isset($nullable[$field]) && $nullable[$field]) {
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

        if (! $table) {
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
        if ($model) {
            $key = $model->getKeyName();
        }

        return $key;
    }

    /**
     * @return string|null
     */
    public function getModelTable()
    {
        $table = null;
        $model = $this->getModel();
        if ($model) {
            $table = $model->getTable();
        }

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
        } elseif ($this->model instanceof Model) {
            $model = $this->model;
        } elseif ($this->model instanceof Builder) {
            $model = $this->model->getModel();
        }

        return $model;
    }

    /**
     * @param  string|callable  $model
     * @param  callable|null  $call
     */
    public static function on_save($model, callable $call = null)
    {
        static::on('save', $model, $call);
    }

    /**
     * @param  string|callable  $model
     * @param  callable|null  $call
     */
    public static function on_saved($model, callable $call = null)
    {
        static::on('saved', $model, $call);
    }

    /**
     * @param  string|callable  $model
     * @param  callable|null  $call
     */
    public static function on_finish($model, callable $call = null)
    {
        static::on('finish', $model, $call);
    }

    /**
     * @param  string|callable  $model
     * @param  callable|null  $call
     */
    public static function on_create($model, callable $call = null)
    {
        static::on('create', $model, $call);
    }

    /**
     * @param  string|callable  $model
     * @param  callable|null  $call
     */
    public static function on_created($model, callable $call = null)
    {
        static::on('created', $model, $call);
    }

    /**
     * @param  string|callable  $model
     * @param  callable|null  $call
     */
    public static function on_update($model, callable $call = null)
    {
        static::on('update', $model, $call);
    }

    /**
     * @param  string|callable  $model
     * @param  callable|null  $call
     */
    public static function on_updated($model, callable $call = null)
    {
        static::on('updated', $model, $call);
    }

    /**
     * @param  string|callable  $model
     * @param  callable|null  $call
     */
    public static function on_delete(string $model, callable $call = null)
    {
        static::on('delete', $model, $call);
    }

    /**
     * @param  string|callable  $model
     * @param  callable|null  $call
     */
    public static function on_deleted(string $model, callable $call = null)
    {
        static::on('deleted', $model, $call);
    }

    /**
     * @param  string  $event
     * @param $model
     * @param  callable|null  $call
     */
    public static function on(string $event, $model, callable $call = null)
    {
        if (! $call && is_callable($model)) {
            $call = $model;

            $model = lte_controller_model();
        }

        $event = "on_$event";

        if ($model && property_exists(static::class, $event) && is_callable($call)) {
            $events = static::$$event;

            $events[$model][] = $call;

            static::$$event = $events;
        }
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

        $result = [];

        if ($class && isset($events[$class])) {
            foreach ($events[$class] as $item) {
                $r = call_user_func_array($item, $params);
                if (is_array($r) && count($r)) {
                    $result = array_merge_recursive($result, $r);
                }
            }
        }

        return $result;
    }
}
