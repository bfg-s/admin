<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;

class Card extends DIV
{
    /**
     * @var bool
     */
    public $opened_mode = true;

    /**
     * @var array
     */
    protected $props = [
        'card', 'card-outline'
    ];

    /**
     * @var array
     */
    protected $types = [
        'default', 'primary', 'success', 'warning', 'danger'
    ];

    /**
     * @var array|\Lar\LteAdmin\Getters\Menu|null
     */
    protected $now;

    /**
     * @var DIV
     */
    protected $head_obj;

    /**
     * @var \Lar\Layout\Tags\H3
     */
    protected $title_obj;

    /**
     * @var bool
     */
    protected $auto_tools = true;

    /**
     * @var ButtonGroup
     */
    protected $group;

    /**
     * @var DIV
     */
    protected $tools;

    /**
     * Col constructor.
     * @param  string  $title
     * @param  string|null  $icon
     * @param  string  $type
     * @param  mixed  ...$params
     */
    public function __construct(string $title = null, string $icon = null, string $type = 'primary', ...$params)
    {
        parent::__construct();

        if ($title && array_search($title, $this->types) !== false) {

            $type = $title;
            $title = null;
        }

        if ($icon && array_search($icon, $this->types) !== false) {

            $type = $icon;
            $icon = null;
        }

        $this->now = gets()->lte->menu->now;

        $type = $this->typed($type);

        $this->addClass("card-{$type}");

        if ($title !== null) {

            $title = __($title);

            $this->head_obj = $this->div(['card-header']);
            $this->title_obj = $this->head_obj->h3(['card-title']);

            if ($icon) {

                $this->title_obj->text("<i class=\"{$icon} mr-1\"></i>");
            }

            $model = gets()->lte->menu->model;

            if ($model) {

                $attrs = $model->getAttributes();

                foreach ($attrs as $key => $attr) {

                    if (is_string($attr) || is_numeric($attr)) {
                        $title = str_replace(":{$key}", $attr, $title);
                    }
                }
            }

            $this->title_obj->text($title);

            if ($this->now && $this->auto_tools) {
                $this->makeToolsArea()
                    ->makeDefaultTools();
            }
        }

        $this->when($params);
    }

    /**
     * @return $this
     */
    protected function makeToolsArea()
    {
        $this->group = new ButtonGroup();

        $this->tools = $this->head_obj->div(['card-tools']);

        $this->tools->appEnd($this->group);

        return $this;
    }

    /**
     * @param $type
     * @return string
     */
    protected function typed($type)
    {
        if ($this->now && $this->now['current.type']) {

            if ($this->now['current.type'] === 'create') return 'success';
            else if ($this->now['current.type'] === 'edit') return 'success';
            else if ($this->now['current.type'] === 'show') return 'info';
        }

        return $type;
    }

    /**
     * Default tools
     */
    protected function makeDefaultTools()
    {
        if ($this->now['current.type']) {

            $type = $this->now['current.type'];

            $this->group->reload();

            if ($type === 'create') {

                $this->group->resourceList();
            }

            else if ($type === 'edit' || $type === 'show') {

                $this->group->resourceList();

                if ($type === 'show') {

                    $this->group->resourceEdit();
                }

                if ($type === 'edit') {

                    $this->group->resourceInfo();
                }

                $this->group->resourceDestroy();
            }

            if ($type !== 'create') {

                $this->group->resourceAdd();
            }
        }
    }
}