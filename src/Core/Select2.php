<?php

namespace Admin\Core;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

class Select2 extends Collection
{
    /**
     * @var bool
     */
    protected $disable_pagination = false;

    /**
     * @var bool
     */
    protected $pagination = true;

    /**
     * @var int|null
     */
    protected $paginate_peg_page = 15;

    /**
     * @var string[]
     */
    protected $columns = [];

    /**
     * @var array
     */
    protected $search_columns = [];

    /**
     * @var bool|string
     */
    protected $group_by = false;

    /**
     * @var Arrayable|Model|Builder|Relation|Collection|LengthAwarePaginator|array
     */
    protected $data;

    /**
     * @var int
     */
    private $_paginate_total = 0;

    /**
     * @var int
     */
    private $_paginate_current = 0;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string|null
     */
    private $no_select;

    /**
     * @var string|null
     */
    private $prefix;

    /**
     * @var string
     */
    private $name = 'select2_page';

    /**
     * @var Collection
     */
    private $value_data;

    /**
     * @var Closure|null
     */
    private $where;

    /**
     * @var string
     */
    protected string $format = "{id}) {name}";

    /**
     * @var array
     */
    protected array $relations = [];

    /**
     * Select2 constructor.
     *
     * @param  null  $data
     * @param  string|null  $format
     * @param  null  $value
     * @param  string|null  $no_select
     * @param  string|null  $prefix
     * @param  null  $where
     * @param  string  $separator
     * @throws ReflectionException
     */
    public function __construct(
        $data = null,
        string $format = null,
        $value = null,
        string $no_select = null,
        string $prefix = null,
        $where = null,
    ) {
        parent::__construct([]);

        if (is_embedded_call($where)) {
            $this->where = $where;
        }

        if ($prefix) {
            $this->prefix = preg_replace('/[^a-zA-Z0-9]/', '_', $prefix);
        }

        if ($value) {
            $this->val($value);
        }

        if ($format) {
            $this->format($format);
        }

        if ($no_select) {
            $this->no_select($no_select);
        }

        if ($data) {
            $this->data($data);
        }
    }

    /**
     * @param $value
     * @return $this
     */
    public function val($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param  string  $format
     * @return $this
     */
    public function format(string $format = '{id}) {name}')
    {
        $this->format = $format;

        preg_match_all('/{[A-z\.]+}/m', $format, $m);
        $matches = $m[0];

        foreach ($matches as $input) {
            $input = trim($input, '{}');
            if (str_contains($input, '.')) {
                $this->relations[$input] = explode('.', $input);
            } else {
                $this->columns[] = $input;
            }
        }

        return $this;
    }

    /**
     * @param  string  $text
     * @return $this
     */
    public function no_select(string $text)
    {
        $this->no_select = $text;

        return $this;
    }

    /**
     * @param $data
     * @return $this
     * @throws ReflectionException
     */
    public function data($data)
    {
        if (is_string($data)) {
            $data = new $data;
        }

        if ($data instanceof \Illuminate\Database\Eloquent\Collection) {
            $data = collect($data->all());
        }

        $this->data = $data;

        $this->createData();

        return $this;
    }

    /**
     * Create selection data.
     * @throws ReflectionException
     */
    private function createData()
    {
        //Arrayable|array|Model|Builder|Relation|Collection|LengthAwarePaginator

        if ($this->data instanceof Model) {
            $this->createModel();
        } elseif ($this->data instanceof Arrayable && !$this->data instanceof Collection) {
            $this->createArrayable();
        } elseif (is_array($this->data)) {
            $this->createArray();
        } elseif ($this->data instanceof Builder) {
            $this->createBuilder();
        } elseif ($this->data instanceof Relation) {
            $this->createRelation();
        } elseif ($this->data instanceof Collection) {
            $this->createCollection();
        } elseif ($this->data instanceof LengthAwarePaginator) {
            $this->createLengthAwarePaginator();
        }
    }

    /**
     * Create data for Model.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createModel(): void
    {
        $class = get_class($this->data);

        $this->name = strtolower(class_basename($class));

        $this->makePaginator();
    }

    /**
     * @return Select2
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function makePaginator(): Select2
    {
        $this->makeSearch()->makeGroupBy();

        if (!$this->disable_pagination) {
            $this->makeValue($this->data);

            if (!request()->has($this->getName())) {
                return $this;
            }

            if ($this->where) {
                $form = request($this->getName().'_form');

                $this->data = call_user_func($this->where, $this->data, is_json($form) ? json_decode($form, 1) : $form);
            }

            if ($this->data) {
                foreach ($this->relations as $full => $relation) {
                    $this->data = $this->data->with($relation[0]);
                }
                $this->data = $this->data->paginate($this->paginate_peg_page, ['*'], $this->getName().'_page');
            } else {
                $this->data = collect([])->paginate($this->paginate_peg_page, $this->getName().'_page');
            }

            $this->createLengthAwarePaginator();
        } else {
            $this->makeValue($this->data);

            if (!request()->has($this->getName())) {
                return $this;
            }

            if ($this->where) {
                $form = request($this->getName().'_form');

                $this->data = call_user_func($this->where, $this->data, is_json($form) ? json_decode($form, 1) : $form);
            }

            $this->data = $this->data instanceof Arrayable ? $this->data->toArray() : $this->data->get()->all();

            if (!$this->data) {
                $this->data = [];
            }

            $this->normalize();
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function makeGroupBy()
    {
        if ($this->group_by) {
            if (is_array($this->data)) {
                collect($this->data)->groupBy($this->group_by)->toArray();
            } else {
                $this->data = $this->data->groupBy($this->group_by);
            }
        }

        return $this;
    }

    /**
     * @return $this
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    private function makeSearch()
    {
        $q = request()->get($this->getName().'_q', false);

        if ($q) {
            $cacheColumns = null;
            if (str_ends_with($q, ')') || str_ends_with($q, ') ')) {
                $q = rtrim($q, "\s)");
                $cacheColumns = $this->columns;
                $this->columns = [$this->columns[0]];
            }
            $collect_filt = function (Collection $collect) use ($q) {
                if ($collect->has($this->columns[0])) {
                    return $collect->filter(function ($item) use ($q) {
                        $find = !$q;

                        if (is_string($item)) {
                            $find = (strpos($item, $q) !== false);
                        } else {
                            foreach ($this->columns as $column) {
                                $find = (strpos($item[$column], $q) !== false);
                            }
                        }

                        return $find;
                    });
                } else {
                    return $collect->map(function ($coll) use ($q) {
                        return collect($coll)->filter(function ($item) use ($q) {
                            $find = !$q;

                            if (is_string($item)) {
                                $find = strpos($item, $q) !== false;
                            } else {
                                foreach ($this->columns as $column) {
                                    $find = strpos($item[$column], $q) !== false;
                                }
                            }

                            return $find;
                        });
                    });
                }
            };

            if (is_array($this->data)) {
                $this->data = $collect_filt(collect($this->data))->toArray();
            } elseif ($this->data instanceof Collection) {
                $this->data = $collect_filt($this->data);
            } elseif ($this->data instanceof Model || $this->data instanceof Relation || $this->data instanceof Builder) {
                $this->data = $this->data->where(function ($query) use ($q) {
                    foreach (array_merge($this->columns, $this->search_columns) as $key => $column) {
                        if (strpos($column, '.') === false) {
                            $query->orWhere($column, 'like', "%{$q}%");
                        }
                    }
                    foreach ($this->relations as $relation) {
                        $query->orWhereHas(
                            $relation[0],
                            fn ($qRelation) => $qRelation->where($relation[1], 'like', "%{$q}%")
                        );
                    }
                });
            }

            if ($cacheColumns) {
                $this->columns = $cacheColumns;
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->prefix ? $this->prefix.$this->name : '';
    }

    /**
     * @param  Builder|Collection  $data
     */
    private function makeValue($dataInsert)
    {
        $data = clone $dataInsert;

        if (!request()->ajax() || request()->pjax()) {
            $key = $this->getKeyColumn();
            $text = $this->getTextColumn();

            if (!($this->value instanceof \Illuminate\Database\Eloquent\Collection)) {
                $has_where = false;

                if (!is_array($this->value) && $this->value) {
                    $data = $data->where($key, $this->value);
                    $has_where = true;
                } elseif (is_array($this->value) && count($this->value)) {
                    $data = $data->whereIn($key, $this->value);
                    $has_where = true;
                }

                if ($has_where) {
                    if (!($data instanceof Arrayable)) {
                        foreach ($this->relations as $relation) {
                            $data = $data->with($relation[0]);
                        }
                        $data = $data->get();
                    }
                    $result = [];
                    $lang = App::getLocale();
                    /** @var Model $d */
                    foreach ($data as $d) {

                        $result[$d[$key]] = preg_replace_callback('/{([A-z.]+)}/m', function ($m) use ($d) {
                            return multi_dot_call($d, $m[1]);
                        }, $this->format);
                    }
                    $this->value_data = $result;
                } else {
                    $this->value_data = collect();
                }
            } else {
                $this->value_data = $this->value->pluck($text, $key);
            }
        }
    }

    /**
     * @return string
     */
    public function getKeyColumn()
    {
        return $this->columns[0] ?? 'id';
    }

    /**
     * @return string
     */
    public function getTextColumn()
    {
        return $this->columns[1] ?? 'id';
    }

    /**
     * Create data for LengthAwarePaginator.
     *
     * @return void
     */
    private function createLengthAwarePaginator()
    {
        $this->pagination = true;

        $this->_paginate_current = $this->data->currentPage();

        $this->_paginate_total = $this->data->lastPage();

        $this->data = $this->data->all();

        $this->normalize();
    }

    /**
     * Normalize data for select2 request.
     */
    private function normalize()
    {
        $field_id = isset($this->columns[0]) ? $this->columns[0] : 'id';

        $field_text = isset($this->columns[1]) ? $this->columns[1] : $field_id;

        $groups = [];

        $has_empty_groups = false;

        foreach ($this->data as $key => $datum) {
            $has_empty_groups = !is_numeric($key);
        }

        foreach ($this->data as $key => $datum) {
            if (is_array($datum) && isset($datum['id']) && isset($datum['text'])) {
                $this->push($datum);

                continue;
            }

            if (is_numeric($key)) {
                if (is_string($datum)) {
                    $datum = [$datum];
                }

                $id = multi_dot_call($datum, $field_id);
                $id = $id === null ? (string) multi_dot_call($datum, '0') : (string) $id;

                $text = preg_replace_callback('/{([A-z.]+)}/m', function ($m) use ($datum) {
                    return multi_dot_call($datum, $m[1]);
                }, $this->format);

                $item = ['id' => $id, 'text' => $text];

                if ($id == $this->value) {
                    $item['selected'] = true;
                }

                if (!$has_empty_groups) {
                    $this->push($item);
                } else {
                    $groups['Other'][] = $item;
                }
            } else {
                $groups[ucfirst($key)] = $datum;
            }
        }

        foreach ($groups as $group_name => $group) {
            $this->push([
                'text' => $group_name,
                'children' => collect($group)->map(function ($datum) use ($field_id, $field_text) {
                    if (is_string($datum)) {
                        $datum = [$field_id => $datum, $field_text => $datum];
                    }

                    $id = (string) multi_dot_call($datum, $field_id);

                    $text = (string) multi_dot_call($datum, $field_text);

                    foreach (array_slice($this->columns, 2) as $part) {
                        $t = multi_dot_call($datum, $part);

                        if ($t) {
                            $text .= ' '.$t;
                        }
                    }

                    $item = ['id' => $id, 'text' => $text];

                    if ($id == $this->value) {
                        $item['selected'] = true;
                    }

                    return $item;
                })->toArray()
            ]);
        }
    }

    /**
     * Create data for Arrayable.
     *
     * @return void
     */
    private function createArrayable()
    {
        $this->data = $this->data->toArray();

        $this->createArray();
    }

    /**
     * Create data for Array.
     *
     * @return void
     */
    private function createArray()
    {
        $this->makeGroupBy()->makeSearch()->normalize();
    }

    /**
     * Create data for Builder.
     *
     * @return void
     * @throws ReflectionException
     */
    private function createBuilder()
    {
        $class = get_class($this->data->getModel());

        $this->name = strtolower(class_basename($class));

        $result = $this->callEvent($class, [$class => $this->data]);

        if ($result instanceof $this->data) {
            $this->data = $result;
        }

        $this->makePaginator();
    }

    /**
     * Create data for Relation.
     *
     * @return void
     * @throws ReflectionException
     */
    private function createRelation()
    {
        $this->createBuilder();
    }

    /**
     * Create data for Relation.
     *
     * @return void
     * @throws ReflectionException
     */
    private function createCollection()
    {
        $class = get_class($this->data);

        $this->name = strtolower(class_basename($class));

        $result = $this->callEvent($class, [$class => $this->data]);

        if ($result instanceof $this->data) {
            $this->data = $result;
        }

        $this->makePaginator();
    }

    /**
     * @param  string  $cols
     * @return $this
     */
    public function searchBy(string $cols)
    {
        foreach (explode(':', $cols) as $item) {
            if (array_search($item, $this->columns) === false && array_search($item, $this->search_columns) === false) {
                $this->search_columns[] = $item;
            }
        }

        return $this;
    }

    /**
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        $this->items = $this->toArray();

        return parent::toJson($options);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $return = [
            'results' => $this->no_select ? array_merge([['id' => '', 'text' => $this->no_select]],
                $this->all()) : $this->all(),
        ];

        if ($this->pagination && $this->_paginate_current !== $this->_paginate_total && count($return['results'])) {
            $return['pagination'] = ['more' => true];
        }

        return $return;
    }

    /**
     * @return Collection
     */
    public function getValueData()
    {
        return $this->value_data;
    }
}
