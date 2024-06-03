<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Core\ModelSaver;
use Admin\Explanation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Throwable;

/**
 * Component for describing the relationship form of the admin panel model.
 */
class ModelRelationComponent extends Component
{
    /**
     * Template mode is enabled for the model relation component.
     *
     * @var bool
     */
    protected static bool $tplMode = false;

    /**
     * The current relation of the model.
     *
     * @var Relation|null
     */
    protected ?Relation $relation = null;

    /**
     * The current name of the model relationship.
     *
     * @var string|null
     */
    protected ?string $relation_name = null;

    /**
     * Latest relationship content.
     *
     * @var ModelRelationContentComponent
     */
    protected ModelRelationContentComponent $last_content;

    /**
     * Event if the relation is empty.
     *
     * @var callable
     */
    protected mixed $on_empty = null;

    /**
     * Internal delegation relations.
     *
     * @var array
     */
    protected array $innerDelegates = [];

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'model-relation';

    /**
     * The title of the model relationship window.
     *
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * Group of model relationship buttons.
     *
     * @var mixed|null
     */
    protected mixed $buttons = null;

    /**
     * Sortable model relations.
     *
     * @var string|null
     */
    protected ?string $ordered = null;

    /**
     * ModelRelationComponent constructor.
     *
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
     * Add an event if the model relationship is empty.
     *
     * @param  callable  $call
     * @return $this
     */
    public function onEmpty(callable $call): static
    {
        $this->on_empty = $call;

        return $this;
    }

    /**
     * Set template mode.
     *
     * @param  bool  $state
     * @return void
     */
    public static function templateMode(bool $state): void
    {
        static::$tplMode = $state;
    }

    /**
     * Check if it is currently in template mode.
     *
     * @return bool
     */
    public static function isTemplateMode(): bool
    {
        return static::$tplMode;
    }

    /**
     * Get the name of the relationship associated with the model.
     *
     * @return string|null
     */
    public function getRelationName(): ?string
    {
        return $this->relation_name;
    }

    /**
     * The deep name function is present in the tree of all components and generates the name nested.
     *
     * @param  array  $names
     * @return string|null
     */
    public function deepName(array $names): string|null
    {
        if (static::$tplMode) {
            if (count($names) <= 1) {
                $return = $this->relation_name.'[{__id__}]';
            } else {
                if (!$this->model->exists) {
                    $return = $this->relation_name.'[{__id__}]';
                } else {
                    $return = $this->relation_name
                        .($this->model->{$this->model->getKeyName()} ? "[{$this->model->{$this->model->getKeyName()}}]" : '');
                }
            }
        } else {
            $return = $this->relation_name
                .($this->model->{$this->model->getKeyName()} ? "[{$this->model->{$this->model->getKeyName()}}]" : '');
        }

        return $return;
    }

    /**
     * Generate part of the path for a nested pass.
     *
     * @param  array  $paths
     * @return string|null
     */
    public function deepPath(array $paths): string|null
    {
        return $this->relation_name.'.*';
    }

    /**
     * Make the model connection sortable.
     *
     * @param  string  $field
     * @return $this
     */
    public function ordered(string $field = 'order'): static
    {
        $this->ordered = $field;

        return $this;
    }

    /**
     * A method for describing a template of delegations into a model relation.
     *
     * @param ...$delegates
     * @return $this
     */
    public function template(...$delegates): static
    {
        $this->innerDelegates = array_merge($this->innerDelegates, $delegates);

        return $this;
    }

    /**
     * Build model relation rows.
     *
     * @throws Throwable
     */
    protected function buildNestedTemplate(): void
    {
        if (!$this->ordered) {
            $datas = $this->relation->get();
        } else {
            $datas = $this->relation->orderBy($this->ordered)->get();
        }

        $i = 0;

        $preventModel = $this->page->getModel();
        $preventModelThis = $this->model;

        /** @var Model $item */
        foreach ($datas as $item) {
            $this->page->model($item);
            $this->model($item);

            $container = $this->createComponent(
                ModelRelationContainerComponent::class,
                $this->relation_name,
                $item->{$item->getKeyName()}
            )->setOrdered($this->ordered);

            $container->model($item);

            $deepNames = $this->deepNames();

            $nameStart = $this->namesToString($deepNames);

            $container->view('components.inputs.hidden', [
                'name' => "{$nameStart}[{$item->getKeyName()}]",
                'value' => $item->{$item->getKeyName()}
            ]);

            if ($this->ordered) {
                $container->view('components.inputs.hidden', [
                    'name' => "{$nameStart}[{$this->ordered}]",
                    'value' => $item->{$this->ordered} ?: $i,
                    'classes' => ['ordered-field']
                ]);
            }

            $this->last_content = $this->createComponent(
                ModelRelationContentComponent::class,
                $this->relation_name,
                'template_content',
                'template_content'
            );

            $this->applyTemplate();

            $container->appEnd($this->last_content);

            if ($this->last_content->get_test_var('control_group', [$item])) {
                $del = $this->last_content->get_test_var('control_delete', [$item]);

                if ($del || $this->last_content->hasControls()) {
                    if ($del) {
                        $buttonsDel = $this->createComponent(ButtonsComponent::class)
                            ->addCLass('control_relation');
                        $buttonsDel->danger(['fas fa-trash', __('admin.delete')])
                            ->on_click('admin::drop_relation', [
                                admin_view('components.inputs.hidden', [
                                    'classes' => ['delete_field'],
                                    'name' => "{$nameStart}[".ModelSaver::DELETE_FIELD.']',
                                    'value' => $item->{$item->getKeyName()}
                                ])->render(),
                            ]);
                        $container->setButtons($buttonsDel);
                    }

                    if ($this->last_content->get_test_var('control_restore') && $del) {
                        $buttonsRestore = $this->createComponent(ButtonsComponent::class)
                            ->addCLass('return_relation')->hide();
                        $text_d = $this->last_content->get_test_var('control_restore_text');
                        $s = $text_d ?: (strtoupper($item->getKeyName()).': '.$item->{$item->getKeyName()});
                        $text = __('admin.restore_subject', ['subject' => $s]);
                        $buttonsRestore->secondary([
                            'fas fa-redo',
                            tag_replace($text, $item),
                        ])->on_click('admin::return_relation');
                        $container->setButtons($buttonsRestore);
                    }
                }
            }

            $this->appEnd($container->render());

            $i++;
        }

        $this->page->model($preventModel);
        $this->model($preventModelThis);

        if (!$datas->count() && $this->on_empty) {
            $container = $this->createComponent(
                ModelRelationContainerComponent::class,
                $this->relation_name,
                'empty',
                'template_empty_container'
            );

            $this->last_content = $this->createComponent(
                ModelRelationContentComponent::class,
                $this->relation_name,
                'template_empty_content',
                'template_empty_content'
            );

            $this->callEmptyTemplate($this->last_content, $this->relation->getQuery()->getModel(), $this);
            $container->appEnd($this->last_content);
            $this->appEnd($container);
        }

        $this->generateButton();
    }

    /**
     * Convert the input name that was generated by the deep method.
     *
     * @param $array
     * @return string
     */
    protected function namesToString($array): string
    {
        if (empty($array)) {
            return '';
        }

        $firstElement = array_shift($array);
        $formattedElements = array_map(function ($item) {
            return sprintf('[%s]', $item);
        }, $array);

        return $firstElement.implode('', $formattedElements);
    }

    /**
     * Apply the internal delegations pattern on the last content of the model relationship.
     *
     * @return void
     */
    protected function applyTemplate(): void
    {
        /**
         * Required Force.
         */
        $this->last_content?->explainForce(Explanation::new($this->innerDelegates));
    }

    /**
     * Raise event if relation is empty.
     *
     * @param  mixed  ...$params
     * @return mixed
     */
    protected function callEmptyTemplate(...$params): mixed
    {
        if ($this->on_empty) {

            return call_user_func($this->on_empty, ...$params);
        }

        return null;
    }

    /**
     * Build relation template maker button.
     *
     * @return void
     * @throws Throwable
     */
    protected function generateButton(): void
    {
        ModelRelationComponent::templateMode(true);
        LangComponent::templateMode(true);

        $emptyModel = new ($this->relation->getRelated());
        $preventModel = $this->page->getModel();
        $preventModelThis = $this->model;

        $this->page->model($emptyModel);
        $this->model($emptyModel);

        $container = $this->createComponent(
            ModelRelationContainerComponent::class,
            $this->relation_name,
            'template_container'
        )->setOrdered($this->ordered);

        $container->model($emptyModel);

        if ($this->ordered) {
            $deepNames = $this->deepNames();

            $nameStart = $this->namesToString($deepNames);

            $container->view('components.inputs.hidden', [
                'name' => "{$nameStart}[{$this->ordered}]",
                'value' => '{__val__}',
                'classes' => ['ordered-field']
            ]);
        }

        $this->last_content = $this->createComponent(
            ModelRelationContentComponent::class,
            $this->relation_name,
            'template_content',
            'template_content'
        );

        $this->last_content->model($emptyModel);

        $this->applyTemplate();

        $container->appEnd($this->last_content);

        if (!$this->last_content->get_test_var('control_create')) {
            return;
        }
        $buttons = $this->createComponent(ButtonsComponent::class);
        $buttons->warning(['fas fa-minus', __('admin.remove')])->on_click('admin::drop_relation_tpl');
        $container->setButtons($buttons);

        $this->buttons = $this->createComponent(ButtonsComponent::class)
            ->success(['fas fa-plus', __('admin.add')])
            ->on_click(
                'admin::add_relation_tpl',
                $this->relation_name
            );

        $row = $this->row();

        $row->template("relation_{$this->relation_name}_template")
            ->appEnd($container->render());

        $this->page->model($preventModel);
        $this->model($preventModelThis);

        ModelRelationComponent::templateMode(false);
        LangComponent::templateMode(false);
    }

    /**
     * Set the title for the model link group.
     *
     * @param  string|null  $title
     * @return static
     */
    public function title(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Additional data to be sent to the template.
     *
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

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     * @throws Throwable
     */
    protected function mount(): void
    {
        if (!($this->relation instanceof Relation)) {
            $this->alert()->title('Danger!')->body("Relation [$this->relation_name] not found!")->dangerType();
        } else {
            $this->buildNestedTemplate();
            $this->setDatas(['relation' => $this->relation_name, 'relation-path' => $this->relation_name]);
        }
    }
}
