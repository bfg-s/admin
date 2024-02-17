<?php

namespace Admin\Core;

use DB;
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
use Illuminate\Support\Collection;
use Admin\Models\AdminFileStorage;
use Intervention\Image\ImageManager;

class ModelSaver
{
    public const DELETE_FIELD = '__DELETE__';

    /**
     * @var callable[]
     */
    protected static array $on_save = [];

    /**
     * @var callable[]
     */
    protected static array $on_saved = [];

    /**
     * @var callable[]
     */
    protected static array $on_finish = [];

    /**
     * @var callable[]
     */
    protected static array $on_create = [];

    /**
     * @var callable[]
     */
    protected static array $on_created = [];

    /**
     * @var callable[]
     */
    protected static array $on_update = [];

    /**
     * @var callable[]
     */
    protected static array $on_updated = [];

    /**
     * @var callable[]
     */
    protected static array $on_delete = [];

    /**
     * @var callable[]
     */
    protected static array $on_deleted = [];

    /**
     * @var bool
     */
    protected bool $has_delete = false;

    /**
     * @var mixed
     */
    protected mixed $src = null;

    /**
     * @param  string|Model  $model
     * @param  array  $data
     * @param  object|null  $eventsObject
     * @param  array  $imageModifiers
     */
    public function __construct(
        protected mixed $model,
        protected array $data,
        protected ?object $eventsObject = null,
        protected array $imageModifiers = [],
    ) {
        if (is_string($model)) {
            $this->model = new $model();
        }
    }

    /**
     * @template SaveModel
     * @param  SaveModel  $model
     * @param  array  $data
     * @param  object|null  $eventsObject
     * @param  array  $imageModifiers
     * @return SaveModel|bool|Model|mixed|string
     */
    public static function do($model, array $data, object $eventsObject = null, array $imageModifiers = [])
    {
        return (new static($model, $data, $eventsObject, $imageModifiers))->save();
    }

    /**
     * Save method.
     *
     * @return bool|Model
     */
    public function save(): Model|bool
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
     * @return array[]
     */
    protected function getDatas(): array
    {
        $data = [];
        foreach ($this->data as $key => $datum) {
            if ($datum instanceof UploadedFile) {
                $data[$key] = AdminFileStorage::makeFile($datum);
                if (isset($this->imageModifiers[$key])) {
                    $image = ImageManager::imagick()->read(public_path($data[$key]));
                    foreach ($this->imageModifiers[$key] as $imageModifier) {
                        $image->{$imageModifier[0]}(...$imageModifier[1]);
                    }
                    $image->save(public_path($data[$key]));
                }
            } else if (is_array($datum) && isset($datum[0]) && $datum[0] instanceof UploadedFile) {
                foreach ($datum as $keyData => $item) {
                    $data[$key][$keyData] = AdminFileStorage::makeFile($item);
                    if (isset($this->imageModifiers[$key])) {
                        $image = ImageManager::imagick()->read(public_path($data[$key][$keyData]));
                        foreach ($this->imageModifiers[$key] as $imageModifier) {
                            $image->{$imageModifier[0]}(...$imageModifier[1]);
                        }
                        $image->save(public_path($data[$key][$keyData]));
                    }
                }
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
     * @return string|null
     */
    public function getModelKeyName(): ?string
    {
        $model = $this->getModel();
        return $model?->getKeyName();
    }

    /**
     * @return Model|null
     */
    public function getModel(): ?Model
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
     * @return array
     */
    protected function getNullableFields(): array
    {
        $table = $this->getModelTable();

        if (!$table) {
            return [];
        }

        $fields = DB::select(
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
    public function getModelTable(): ?string
    {
        $model = $this->getModel();
        return $model?->getTable();
    }

    /**
     * @return array
     */
    protected function getFields(): array
    {
        $table = $this->getModelTable();

        if (!$table) {
            return [];
        }

        return $this->model->getConnection()->getSchemaBuilder()->getColumnListing($table);
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
                            $param = collect($param)->filter(static function ($i) {
                                return !isset($i[static::DELETE_FIELD]);
                            })->map(static function ($i) {
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
     * @param  string  $name
     * @param  mixed  ...$params
     * @return array
     */
    protected function call_on(string $name, ...$params): array
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

        if (
            $this->eventsObject
            && $model
            && method_exists($this->eventsObject, $name)
            && property_exists($this->eventsObject, 'model')
        ) {
            $controllerModel = $this->eventsObject::$model;
            if ($controllerModel == $class) {
                $r = call_user_func_array([$this->eventsObject, $name], $params);
                if (is_array($r) && count($r)) {
                    $result = array_merge_recursive($result, $r);
                }
            }
        }

        return $result;
    }

    /**
     * @param $src
     * @return $this
     */
    public function setSrc($src): static
    {
        $this->src = $src;

        return $this;
    }

    /**
     * Create model.
     * @param $data
     * @param $add
     * @return Model|string|null
     */
    protected function create_model($data, $add): Model|string|null
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
                            $param = collect($param)->filter(static function ($i) {
                                return !isset($i[static::DELETE_FIELD]);
                            })->map(static function ($i) {
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
     * @param $model
     * @param  array|Arrayable  $data
     * @return Collection
     */
    public static function doMany($model, array|Arrayable $data): Collection
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
     * @param  callable|string  $model
     * @param  callable|null  $call
     */
    public static function on_save(callable|string $model, callable $call = null): void
    {
        static::on('save', $model, $call);
    }

    /**
     * @param  string  $event
     * @param $model
     * @param  callable|null  $call
     */
    public static function on(string $event, $model, callable $call = null): void
    {
        if (!$call && is_callable($model)) {
            $call = $model;

            $model = admin_controller_model();
        }

        $event = "on_$event";

        if ($model && property_exists(static::class, $event) && is_callable($call)) {
            $events = static::$$event;
            $events[$model][] = $call;
            static::$$event = $events;
        }
    }

    /**
     * @param  callable|string  $model
     * @param  callable|null  $call
     */
    public static function on_saved(callable|string $model, callable $call = null): void
    {
        static::on('saved', $model, $call);
    }

    /**
     * @param  callable|string  $model
     * @param  callable|null  $call
     */
    public static function on_finish(callable|string $model, callable $call = null): void
    {
        static::on('finish', $model, $call);
    }

    /**
     * @param  callable|string  $model
     * @param  callable|null  $call
     */
    public static function on_create(callable|string $model, callable $call = null): void
    {
        static::on('create', $model, $call);
    }

    /**
     * @param  callable|string  $model
     * @param  callable|null  $call
     */
    public static function on_created(callable|string $model, callable $call = null): void
    {
        static::on('created', $model, $call);
    }

    /**
     * @param  callable|string  $model
     * @param  callable|null  $call
     */
    public static function on_update(callable|string $model, callable $call = null): void
    {
        static::on('update', $model, $call);
    }

    /**
     * @param  callable|string  $model
     * @param  callable|null  $call
     */
    public static function on_updated(callable|string $model, callable $call = null): void
    {
        static::on('updated', $model, $call);
    }

    /**
     * @param  callable|string  $model
     * @param  callable|null  $call
     */
    public static function on_delete(callable|string $model, callable $call = null): void
    {
        static::on('delete', $model, $call);
    }

    /**
     * @param  callable|string  $model
     * @param  callable|null  $call
     */
    public static function on_deleted(callable|string $model, callable $call = null): void
    {
        static::on('deleted', $model, $call);
    }
}
