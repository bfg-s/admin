<?php

namespace Lar\LteAdmin\Components;

use Illuminate\Database\Eloquent\Relations\Relation;
use Lar\LteAdmin\Components\Traits\ModelRelation\ModelRelationBuilderTrait;
use Lar\LteAdmin\Components\Traits\ModelRelation\ModelRelationHelpersTrait;

class ModelRelationComponent extends Component
{
    use ModelRelationHelpersTrait,
        ModelRelationBuilderTrait;

    /**
     * @var Relation
     */
    protected $relation;

    /**
     * @var string
     */
    protected $relation_name;

    /**
     * @var string
     */
    protected $path_name;

    /**
     * @var \Closure|array
     */
    protected $create_content;

    /**
     * @var ModelRelationContentComponent
     */
    protected $last_content;

    /**
     * @var callable
     */
    protected $on_empty;

    /**
     * @var mixed
     */
    protected static $fm;
    protected $fm_old;
    protected $innerDelegates;

    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->innerDelegates = $delegates;
    }

    public function relation(array|string $relation)
    {
        if (is_array($relation)) {
            $this->relation_name = $relation[0];
            $this->relation = $this->model->{$relation[1]}();
        } else {
            $this->relation_name = $relation;
            $this->relation = $relation;
        }

        return $this;
    }

    protected function mount()
    {
        if (! ($this->relation instanceof Relation)) {
            $this->alert('Danger!', 'Relation not found!')->danger();
        } else {
            $this->fm_old = self::$fm;
            self::$fm = $this->relation;
            $this->create_content = $this->innerDelegates;
            $this->toExecute('_build');
            $this->setDatas(['relation' => $this->relation_name, 'relation-path' => $this->relation_name]);
        }
    }
}
