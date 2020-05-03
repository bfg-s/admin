<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\UL;
use Lar\Tagable\Events\onRender;

class Tabs extends UL implements onRender
{
    /**
     * @var array
     */
    protected $props = [
        'nav nav-tabs',
        'role' => 'tablist'
    ];

    /**
     * @var array|null
     */
    protected $_tmp_ = null;

    /**
     * @var array
     */
    protected $tabs = [];

    /**
     * Col constructor.
     * @param  null  $num
     * @param  mixed  ...$params
     */
    public function __construct(...$params)
    {
        parent::__construct();

        $this->when($params);
    }

    /**
     * @param  string  $name
     * @param  string|array|null  $icon
     * @param  array  $attrs
     * @return $this
     */
    public function addTab(string $name, $icon = null, array $attrs = [])
    {
        if(is_array($icon)) {
            $attrs = $icon;
            $icon = null;
        }

        $this->_tmp_ = [
            'title' => $name,
            'id' => 'tab-' . md5($name),
            'attrs' => $attrs,
            'icon' => $icon,
            'active' => !count($this->tabs),
            'number' => count($this->tabs)
        ];

        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function addData($content)
    {
        if ($this->_tmp_) {

            $this->_tmp_['content'] = $content;

            $this->tabs[$this->_tmp_['id'] . "-label"] = $this->_tmp_;

            $this->_tmp_ = null;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function onRender()
    {
        foreach ($this->tabs as $key => $tab) {

            $a = $this->li(['nav-item'])
                ->a([
                    'nav-link',
                    'id' => $key,
                    'data-toggle' => 'pill',
                    'role' => 'tab',
                    'aria-controls' => $tab['id'],
                    'aria-selected' => $tab['active'] ? 'true' : 'false'
                ])->attr($tab['attrs'])
                ->addClassIf($tab['active'], 'active')
                ->setHref("#{$tab['id']}");

            if ($tab['icon']) $a->i([$tab['icon'], 'mr-1']);

            $a->text($tab['title']);
        }

        $this->after = DIV::create(['tab-content']);

        foreach ($this->tabs as $key => $tab) {

            $this->after->div([[
                'tab-pane pt-3',
                'id' => $tab['id'],
                'role' => 'tabpanel',
                'aria-labelledby' => $key
            ]])->addClassIf($tab['active'], 'active show')->text($tab['content']);
        }
    }
}