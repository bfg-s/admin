<?php

namespace LteAdmin\Components;

use Illuminate\Database\Eloquent\Relations\Relation;
use LteAdmin\Traits\ModelRelation\ModelRelationBuilderTrait;
use LteAdmin\Traits\ModelRelation\ModelRelationHelpersTrait;

class ModelRelationComponent extends Component
{
    use ModelRelationHelpersTrait;
    use ModelRelationBuilderTrait;

    /**
     * @var mixed
     */
    protected static $fm;
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
     * @var ModelRelationContentComponent
     */
    protected $last_content;
    /**
     * @var callable
     */
    protected $on_empty;
    protected $fm_old;
    protected $innerDelegates = [];

    /**
     * @param  array|string  $relationName
     * @param  mixed  ...$delegates
     */
    public function __construct($relationName, ...$delegates)
    {
        parent::__construct();

        if (is_array($relationName)) {
            $this->relation_name = $relationName[0];
            $this->relation = $this->model->{$relationName[1]}();
        } else {
            $this->relation_name = $relationName;
            $this->relation = $this->model->{$relationName}();
        }

        $this->innerDelegates = array_merge($this->innerDelegates, $delegates);
    }

    public function template(...$delegates)
    {
        $this->innerDelegates = array_merge($this->innerDelegates, $delegates);

        return $this;
    }

    protected function mount()
    {
        if (!($this->relation instanceof Relation)) {
            $this->alert()->title('Danger!')->body("Relation [$this->relation_name] not found!")->dangerType();
        } else {
            $this->fm_old = self::$fm;
            self::$fm = $this->relation;
            $this->_build();
            $this->setDatas(['relation' => $this->relation_name, 'relation-path' => $this->relation_name]);
        }
    }
}
