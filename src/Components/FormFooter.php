<?php

namespace Lar\LteAdmin\Components;

use Lar\Layout\Tags\DIV;

/**
 * Class Form
 * @package Lar\LteAdmin\Components
 */
class FormFooter extends DIV
{
    /**
     * @var string
     */
    protected $form_id;

    /**
     * @var array
     */
    protected $props = [
        'card-footer'
    ];

    /**
     * @var bool
     */
    private $nav_redirect = true;

    /**
     * Col constructor.
     * @param  bool  $nav_redirect
     * @param  mixed  ...$params
     */
    public function __construct($nav_redirect = true, ...$params)
    {
        parent::__construct();

        if (is_bool($nav_redirect)) {

            $this->nav_redirect = $nav_redirect;
        }

        else {

            $this->when($nav_redirect);
        }

        $this->when($params);
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
     * @return $this
     */
    public function createFooter()
    {
        $group = new ButtonGroup(['group-sm']);

        $type = gets()->lte->menu->type;
        $menu = gets()->lte->menu->now;

        if ($type === 'edit' || isset($menu['post'])) {

            $group->success(['fas fa-save', __('lte.save')])->setDatas([
                'click' => 'submit',
                'form' => $this->form_id
            ]);
        }

        else if ($type === 'create') {

            $group->success(['fas fa-plus', __('lte.add')])->setDatas([
                'click' => 'submit',
                'form' => $this->form_id
            ]);
        }

        else {

            $group->submit(null, $this->form_id);
        }

        $row = $this->div(['row']);

        if ($type && $this->nav_redirect) {

            $row->div(['col'])->div(['mb-0 clearfix'])->when(function (DIV $div) use ($type) {

                $_after = session('_after', 'index');

                $div->div(['icheck-primary float-left mr-2'])
                    ->input(['name' => '_after', 'type' => 'radio', 'id' => '_after_select_index', 'value' => 'index'])
                    ->setDatas(['state' => ''])->setCheckedIf($_after === 'index', 'checked')
                    ->label(['for' => '_after_select_index'])->text(__('lte.to_the_list'));

                if ($type === 'create') {

                    $div->div(['icheck-primary float-left mr-2'])
                        ->input(['name' => '_after', 'type' => 'radio', 'id' => '_after_select_stay', 'value' => 'stay'])
                        ->setDatas(['state' => ''])->setCheckedIf($_after === 'stay', 'checked')
                        ->label(['for' => '_after_select_stay'])->text(__('lte.add_more'));
                }

                if ($type === 'edit') {

                    $div->div(['icheck-primary float-left mr-2'])
                        ->input(['name' => '_after', 'type' => 'radio', 'id' => '_after_select_stay', 'value' => 'stay'])
                        ->setDatas(['state' => ''])->setCheckedIf($_after === 'stay', 'checked')
                        ->label(['for' => '_after_select_stay'])->text(__('lte.edit_further'));
                }
            });
        }

        $row->div(['col text-right'])
            ->appEnd($group);


        return $this;
    }
}