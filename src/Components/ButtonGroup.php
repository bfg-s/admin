<?php

namespace Lar\LteAdmin\Components;

use Illuminate\Database\Eloquent\Model;
use Lar\Layout\Tags\BUTTON;
use Lar\Layout\Tags\DIV;

/**
 * Class ButtonGroup
 *
 * @package Lar\LteAdmin\Components\UI
 */
class ButtonGroup extends DIV {

    /**
     * @var array|null
     */
    protected $menu;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var string
     */
    protected $action;

    /**
     * ButtonGroup constructor.
     * @param  array  $params
     */
    public function __construct(...$params)
    {
        parent::__construct();

        $this->addClass('btn-group btn-group-sm');

        $this->when($params);
        
        $this->menu = gets()->lte->menu->now;

        $this->model = gets()->lte->menu->model;

        $this->action = \Str::before(\Route::currentRouteAction(), '@');
    }

    /**
     * @param $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|BUTTON
     */
    public function info($ico, array $when = [])
    {
        return $this->btn('info', $ico, $when);
    }

    /**
     * @param $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|BUTTON
     */
    public function warning($ico, array $when = [])
    {
        return $this->btn('warning', $ico, $when);
    }

    /**
     * @param $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|BUTTON
     */
    public function danger($ico, array $when = [])
    {
        return $this->btn('danger', $ico, $when);
    }

    /**
     * @param $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|BUTTON
     */
    public function success($ico, array $when = [])
    {
        return $this->btn('success', $ico, $when);
    }

    /**
     * @param $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|BUTTON
     */
    public function secondary($ico, array $when = [])
    {
        return $this->btn('secondary', $ico, $when);
    }

    /**
     * @param $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|BUTTON
     */
    public function default($ico, array $when = [])
    {
        return $this->btn('default', $ico, $when);
    }

    /**
     * @param $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|BUTTON
     */
    public function primary($ico, array $when = [])
    {
        return $this->btn('primary', $ico, $when);
    }

    /**
     * @param  string  $type
     * @param  string|array  $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|BUTTON
     */
    public function btn($type, $ico, array $when = [])
    {
        return $this->button(['btn btn-xs btn-' . $type])->when(function (BUTTON $button) use ($ico) {
            $button->setType('button');
            if (is_array($ico)) {
                $ico = array_values($ico);
                if (isset($ico[0]) && $ico[0]) { $button->i([$ico[0]]); }
                if (isset($ico[0]) && $ico[0] && isset($ico[1]) && $ico[1]) { $button->text(":space"); }
                if (isset($ico[1]) && $ico[1]) { $button->text("<span class='d-none d-sm-inline'>{$ico[1]}</span>"); }
            }
            else if ($ico) {

                $button->i([$ico]);
            }
        })->attr($when);
    }

    /**
     * Reload button
     * @param  string|null  $link
     * @param  string|null  $title
     */
    public function reload(string $link = null, string $title = null)
    {
        $this->secondary(['fas fa-redo-alt', $title ?? __('lte::admin.refresh')])->dataClick()->location($link ?? \Request::getRequestUri());
    }

    /**
     * Nestable group
     */

    /**
     * @return $this
     */
    public function nestable()
    {
        $this->info(['far fa-minus-square', __('lte::admin.collapse_all')])->setDatas(['click' => 'nestable::collapse']);
        $this->primary(['far fa-plus-square', __('lte::admin.expand_all')])->setDatas(['click' => 'nestable::expand']);

        return $this;
    }
    
    /**
     * Resource group
     */


    /**
     * Resource list button
     * @param  string  $link
     * @param  string|null  $title
     */
    public function resourceList(string $link = null, string $title = null)
    {
        if ($link || isset($this->menu['link'])) {

            $this->primary(['fas fa-list-alt', $title ?? __('lte::admin.list')])->dataClick()->location($link ?? $this->menu['link']);
        }
    }

    /**
     * Resource edit button
     * @param  string  $link
     * @param  string|null  $title
     */
    public function resourceEdit(string $link = null, string $title = null)
    {
        if (!$link && $this->model && $this->model->exists) {

            $rk_name = $this->model->getRouteKeyName();

            $key = $this->model->getOriginal($rk_name);

            if ($key && isset($this->menu['link.edit']) && (method_exists($this->action, 'edit') || method_exists($this->action, 'edit_default'))) {

                $link = $this->menu['link.edit']($key);
            }
        }

        if ($link) {

            $this->success('fas fa-edit')->text(':space', $title ?? __('lte::admin.edit'))->dataClick()->location($link);
        }
    }

    /**
     * Resource info button
     * @param  string  $link
     * @param  string|null  $title
     */
    public function resourceInfo(string $link = null, string $title = null)
    {
        if (!$link && $this->model && $this->model->exists) {

            $rk_name = $this->model->getRouteKeyName();

            $key = $this->model->getOriginal($rk_name);

            if ($key && isset($this->menu['link.show']) && (method_exists($this->action, 'show') || method_exists($this->action, 'show_default'))) {

                $link = $this->menu['link.show']($key);
            }
        }

        if ($link) {

            $this->info(['fas fa-info-circle', $title ?? __('lte::admin.information')])->dataClick()->location($link);
        }
    }

    /**
     * Resource add button
     * @param  string  $link
     * @param  string|null  $title
     */
    public function resourceDestroy(string $link = null, string $title = null)
    {
        if (!$link && $this->model && $this->model->exists) {

            $rk_name = $this->model->getRouteKeyName();

            $key = $this->model->getOriginal($rk_name);

            if ($key && isset($this->menu['link.destroy']) && (method_exists($this->action, 'destroy') || method_exists($this->action, 'destroy_default'))) {

                $link = $this->menu['link.destroy']($key);
            }
        }

        if ($link) {

            $this->danger(['fas fa-trash-alt', $title ?? __('lte::admin.delete')])->setDatas([
                'click' => 'alert::confirm',
                'params' => [
                    __('lte::admin.delete_subject', ['subject' => strtoupper($rk_name).":{$key}?"]),
                    $link . " >> \$jax.del"
                ]
            ]);
        }
    }

    /**
     * @param  array  $btn
     * @param  string|null  $form
     * @return \Lar\Layout\Abstracts\Component
     */
    public function submit($btn = null, string $form = null)
    {
        if (!$btn) {

            $btn = ['fas fa-save', __('lte::admin.submit')];
        }

        $datas = [
            'click' => 'submit'
        ];

        if ($form) {

            $datas['form'] = $form;
        }

        return $this->success($btn)->setDatas($datas);
    }

    /**
     * Resource add button
     * @param  string  $link
     * @param  string  $title
     */
    public function resourceAdd(string $link = null, string $title = null)
    {
        if (!$link && isset($this->menu['link.create']) && (method_exists($this->action, 'create') || method_exists($this->action, 'create_default'))) {

            $link = $this->menu['link.create'];
        }

        if ($link) {

            $this->success(['fas fa-plus', $title ?? __('lte::admin.add')])->dataClick()->location($link);
        }
    }
}
