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

        $now = gets()->lte->menu->now;

        $type = $this->typed($type, $now);

        $this->addClass("card-{$type}");

        if ($title) {

            $head_obj = $this->div(['card-header']);
            $title_obj = $head_obj->h3(['card-title']);

            if ($icon) {

                $title_obj->text("<i class=\"{$icon} mr-1\"></i>");
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

            $title_obj->text($title);

            if ($now) {

                $this->tools($head_obj, $now);
            }
        }

        $this->when($params);
    }

    protected function typed($type, $menu)
    {
        if ($menu && $menu['current.type']) {

            if ($menu['current.type'] === 'create') return 'success';
            else if ($menu['current.type'] === 'edit') return 'success';
            else if ($menu['current.type'] === 'show') return 'info';
        }

        return $type;
    }

    /**
     * @param  DIV  $obj
     * @param  array  $menu
     */
    protected function tools(DIV $obj, $menu)
    {
        if ($menu['current.type']) {

            $type = $menu['current.type'];

            $tools = $obj->div(['card-tools']);

            $group = new ButtonGroup();

            $group->reload();

            if ($type === 'create') {

                $group->resourceList();
            }

            else if ($type === 'edit' || $type === 'show') {

                $group->resourceList();

                if ($type === 'show') {

                    $group->resourceEdit();
                }

                if ($type === 'edit') {

                    $group->resourceInfo();
                }

                $group->resourceDestroy();
            }

            if ($type !== 'create') {

                $group->resourceAdd();
            }

            $tools->appEnd($group);
        }
    }
}