<?php

namespace Admin\Components;

use Illuminate\Database\Eloquent\Relations\Relation;
use Admin\Traits\ModelRelation\ModelRelationBuilderTrait;
use Admin\Traits\ModelRelation\ModelRelationHelpersTrait;
use Throwable;

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
     * @var string
     */
    protected string $view = 'model-relation';

    /**
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * @var mixed|null
     */
    protected mixed $buttons = null;

    /**
     * @var string|null
     */
    protected ?string $ordered = null;

    /**
     * @var bool
     */
    protected static bool $tplMode = false;

    /**
     * @param  array|string  $relationName
     * @param  mixed  ...$delegates
     */
    public function __construct($relationName, ...$delegates)
    {
        if (is_array($relationName)) {
            $this->relation_name = $relationName[0];
            $modelRelationName = $relationName[1];
        } else {
            $this->relation_name = $relationName;
            $modelRelationName = $relationName;
        }

        parent::__construct();

        $this->relation = $this->model->{$modelRelationName}();

        $this->innerDelegates = array_merge($this->innerDelegates, $delegates);
    }

    /**
     * @return string|null
     */
    public function getRelationName(): ?string
    {
        return $this->relation_name;
    }

    /**
     * @param  bool  $state
     * @return void
     */
    public static function templateMode(bool $state): void
    {
        static::$tplMode = $state;
    }

    /**
     * @return bool
     */
    public static function isTemplateMode(): bool
    {
        return static::$tplMode;
    }

    /**
     * @param  array  $names
     * @return string|null
     */
    public function deepName(array $names): string|null
    {
        if (static::$tplMode) {
            if (count($names) <= 1) {
                $return = $this->relation_name . '[{__id__}]';
            } else {
                if (! $this->model->exists) {
                    $return = $this->relation_name . '[{__id__}]';
                } else {

                    $return = $this->relation_name
                        . ($this->model->{$this->model->getKeyName()} ? "[{$this->model->{$this->model->getKeyName()}}]" : '');
                }
            }
        } else {
            $return = $this->relation_name
                . ($this->model->{$this->model->getKeyName()} ? "[{$this->model->{$this->model->getKeyName()}}]" : '');
        }

        return $return;
    }

    public function deepPath(array $paths): string|null
    {
        return $this->relation_name . '.*';
    }

    /**
     * @param  string|null  $title
     * @return static
     */
    public function title(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param  string  $field
     * @return $this
     */
    public function ordered(string $field = 'order'): static
    {
        $this->ordered = $field;

        return $this;
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
     * @throws Throwable
     */
    protected function mount(): void
    {
        if (!($this->relation instanceof Relation)) {
            $this->alert()->title('Danger!')->body("Relation [$this->relation_name] not found!")->dangerType();
        } else {
            $this->fm_old = self::$fm;
            self::$fm = $this->relation;
            $this->buildNestedTemplate();
            $this->setDatas(['relation' => $this->relation_name, 'relation-path' => $this->relation_name]);
        }
    }

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'title' => $this->title,
            'ordered' => $this->ordered,
            'buttons' => $this->buttons,
            'tpl' => "relation_{$this->relation_name}_template",
        ];
    }
}
