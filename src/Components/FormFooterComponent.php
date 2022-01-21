<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\Vue\FormActionAfterSave;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\Tagable\Events\onRender;

/**
 * @mixin FormFooterContentMacroList
 */
class FormFooterComponent extends DIV implements onRender
{
    use Macroable, Delegable;

    /**
     * @var string
     */
    protected $form_id;

    /**
     * @var array
     */
    protected $props = [
        'row',
    ];

    /**
     * @var string|null
     */
    protected $btn_text;

    /**
     * @var string|null
     */
    protected $btn_icon;

    /**
     * @var string|null
     */
    protected $type;

    /**
     * @var bool
     */
    private $nav_redirect = true;

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->explainForce(Explanation::new($delegates));

        if (FormComponent::$last_id) {
            $this->setFormId(FormComponent::$last_id);
        }

        $this->callConstructEvents();
    }

    /**
     * @param  string  $text
     * @param  string|null  $icon
     * @return $this
     */
    public function defaultBtn(string $text, string $icon = null)
    {
        $this->btn_text = $text;

        if ($icon) {
            $this->btn_icon = $icon;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function noRedirect()
    {
        $this->nav_redirect = false;

        return $this;
    }

    /**
     * @param  string  $id
     * @return $this
     */
    public function setFormId(string $id)
    {
        $this->form_id = $id;

        return $this;
    }

    /**
     * @param  string  $type
     * @return $this|\Lar\Layout\Abstracts\Component|\Lar\Layout\LarDoc|FormFooterComponent
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return $this
     */
    public function createDefaultCRUDFooter()
    {
        $group = new ButtonsComponent();

        $type = $this->type ?? gets()->lte->menu->type;
        $menu = gets()->lte->menu->now;

        if ($type === 'edit' || isset($menu['post'])) {
            $group->success([$this->btn_icon ?? 'fas fa-save', __($this->btn_text ?? 'lte.save')])->setDatas([
                'click' => 'submit',
                'form' => $this->form_id,
            ]);
        } elseif ($type === 'create') {
            $group->success([$this->btn_icon ?? 'fas fa-plus', __($this->btn_text ?? 'lte.add')])->setDatas([
                'click' => 'submit',
                'form' => $this->form_id,
            ]);
        } else {
            $group->submit(null, $this->form_id);
        }

        if (($type === 'create' || $type === 'edit') && $this->nav_redirect) {
            $this->appEnd(FormActionAfterSave::create([
                'select' => session('_after', 'index'),
                'type' => $type,
                'lang' => [
                    'to_the_list' => __('lte.to_the_list'),
                    'add_more' => __('lte.add_more'),
                    'edit_further' => __('lte.edit_further'),
                ],
            ]));
        }

        $this->div(['col text-right'])
            ->appEnd($group);

        return $this;
    }

    /**
     * @return mixed|void
     */
    public function onRender()
    {
        $this->callRenderEvents();
    }
}
