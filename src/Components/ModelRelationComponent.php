<?php

namespace Admin\Components;

use Illuminate\Database\Eloquent\Relations\Relation;
use Admin\Traits\ModelRelation\ModelRelationBuilderTrait;
use Admin\Traits\ModelRelation\ModelRelationHelpersTrait;

class ModelRelationComponent extends Component
{
    use ModelRelationHelpersTrait;
    use ModelRelationBuilderTrait;

    /**
     * @var mixed
     */
    protected static mixed $fm = null;

    /**
     * @var Relation|null
     */
    protected ?Relation $relation = null;

    /**
     * @var string|null
     */
    protected ?string $relation_name = null;

    /**
     * @var string|null
     */
    protected ?string $path_name = null;

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
    protected mixed $fm_old;

    /**
     * @var array
     */
    protected array $innerDelegates = [];

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

    /**
     * @param ...$delegates
     * @return $this
     */
    public function template(...$delegates): static
    {
        $this->innerDelegates = array_merge($this->innerDelegates, $delegates);

        return $this;
    }

    /**
     * @return void
     */
    protected function mount(): void
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
