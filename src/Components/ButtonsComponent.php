<?php

namespace LteAdmin\Components;

use LteAdmin\Delegates\Buttons;
use Request;

class ButtonsComponent extends Component
{
    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->delegatesNow(...$delegates);

        $this->addClass('btn-group btn-group-sm ml-1');
    }

    /**
     * @param  mixed  $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function default($ico = null, array $when = [])
    {
        return $this->btn('default', $ico, $when);
    }

    /**
     * @param  string  $type
     * @param  string|array|null  $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function btn($type, $ico = null, array $when = [])
    {
        $this->model();

        $btn = ButtonComponent::create()
            ->wisibleType($type)->model($this->model);

        if ($ico && is_string($ico)) {
            $btn->icon($ico);
        } else {
            if ($ico && is_array($ico)) {
                $btn->iconTitle($ico);
            }
        }

        $btn->attr($when);

        $this->appEnd($btn);

        return $btn;
    }

    /**
     * Reload button.
     * @param  string|null  $link
     * @param  string|null  $title
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function reload(string $link = null, string $title = null)
    {
        $return = $this->secondary(['fas fa-redo-alt', $title ?? __('lte.refresh')]);
        $return->dataClick()->location($link ?? Request::getRequestUri());
        $return->setTitleIf($title === '', __('lte.refresh'));

        return $return;
    }

    /**
     * @param  mixed  $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function secondary($ico = null, array $when = [])
    {
        return $this->btn('secondary', $ico, $when);
    }

    /**
     * @return $this
     */
    public function nestable()
    {
        $this->info(['far fa-minus-square', __('lte.collapse_all')])->setDatas(['click' => 'nestable::collapse']);
        $this->primary(['far fa-plus-square', __('lte.expand_all')])->setDatas(['click' => 'nestable::expand']);

        return $this;
    }

    /**
     * @param  mixed  $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function info($ico = null, array $when = [])
    {
        return $this->btn('info', $ico, $when);
    }

    /**
     * @param  mixed  $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function primary($ico = null, array $when = [])
    {
        return $this->btn('primary', $ico, $when);
    }

    /**
     * @param  mixed  $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function dark($ico = null, array $when = [])
    {
        return $this->btn('dark', $ico, $when);
    }

    /**
     * Resource list button.
     * @param  string|null  $link
     * @param  string|null  $title
     * @return $this|\Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function resourceList(string $link = null, string $title = null)
    {
        if ($link || isset($this->menu['link'])) {
            $return = $this->primary(['fas fa-list-alt', $title ?? __('lte.list')]);
            $return->dataClick()->location($link ?? $this->menu['link.index']());
            $return->setTitleIf($title === '', __('lte.list'));

            return $return;
        }

        return $this;
    }

    /**
     * Resource edit button.
     * @param  string|null  $link
     * @param  string|null  $title
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent|ButtonsComponent|Buttons
     */
    public function resourceEdit(string $link = null, string $title = null)
    {
        if (!$link && $this->model) {
            $key = $this->realModel()->getRouteKey();
            if (
                $key &&
                isset($this->menu['link.edit'])
            ) {
                $link = $this->menu['link.edit']($key);
            }
        }

        if ($link) {
            if (!$this->menu) {
                return new Buttons();
            }
            $return = $this->success(['fas fa-edit', $title ?? __('lte.edit')]);
            $return->dataClick()->location($link);
            $return->setTitleIf($title === '', __('lte.edit'));

            return $return;
        }

        return $this;
    }

    /**
     * Nestable group.
     */

    /**
     * @param  mixed  $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function success($ico = null, array $when = [])
    {
        return $this->btn('success', $ico, $when);
    }

    /**
     * Resource group.
     */

    /**
     * Resource info button.
     * @param  string|null  $link
     * @param  string|null  $title
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent|ButtonsComponent|Buttons
     */
    public function resourceInfo(string $link = null, string $title = null)
    {
        if (!$link && $this->model) {
            $key = $this->realModel()->getRouteKey();

            if (
                $key &&
                isset($this->menu['link.show'])
            ) {
                $link = $this->menu['link.show']($key);
            }
        }

        if ($link) {
            if (!$this->menu) {
                return new Buttons();
            }
            $return = $this->info(['fas fa-info-circle', $title ?? __('lte.information')]);
            $return->dataClick()->location($link);
            $return->setTitleIf($title === '', __('lte.information'));

            return $return;
        }

        return $this;
    }

    /**
     * Resource add button.
     * @param  string|null  $link
     * @param  string|null  $title
     * @param  string|null  $message
     * @param  null  $key
     * @return \Lar\Layout\Abstracts\Component|ButtonsComponent|Buttons
     */
    public function resourceDestroy(string $link = null, string $title = null, string $message = null, $key = null)
    {
        if (!$link && $this->model) {
            $key = $this->realModel()->getRouteKey();

            if (
                $key &&
                isset($this->menu['link.destroy'])
            ) {
                $link = $this->menu['link.destroy']($key);
            }
        }

        if ($link) {
            if (!$this->menu) {
                return new Buttons();
            }

            $stay = !$this->menu['current'] ? (str_contains($link, '?') ? '&' : '?').'_after=stay' : '';

            return $this->danger(['fas fa-trash-alt', $title ?? __('lte.delete')])->setDatas([
                'click' => 'alert::confirm',
                'params' => [
                    __(
                        'lte.delete_subject',
                        ['subject' => strtoupper($message ?? $this->model->getRouteKeyName()).($key ? ":{$key}?" : '')]
                    ),
                    $link.$stay.' >> $jax.del',
                ],
            ])->setTitleIf($title === '', __('lte.delete'));
        }

        return $this;
    }

    /**
     * @param  mixed  $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function danger($ico = null, array $when = [])
    {
        return $this->btn('danger', $ico, $when);
    }

    /**
     * Resource add button.
     * @param  string|null  $link
     * @param  string|null  $title
     * @param  string|null  $message
     * @param  null  $key
     * @return \Lar\Layout\Abstracts\Component|static|self||ButtonComponent
     */
    public function resourceForceDestroy(string $link = null, string $title = null, string $message = null, $key = null)
    {
        if (!$link && $this->model) {
            $key = $this->realModel()->getRouteKey();

            if (
                $key &&
                isset($this->menu['link.destroy'])
            ) {
                $link = $this->menu['link.destroy']($key);
            }
        }

        if ($link) {
            return $this->danger(['fas fa-trash-alt', $title ?? __('lte.delete_forever')])->setDatas([
                'click' => 'alert::confirm',
                'params' => [
                    __(
                        'lte.delete_forever_subject',
                        ['subject' => strtoupper($message ?? $this->model->getRouteKeyName()).($key ? ":{$key}?" : '')]
                    ),
                    $link.'?force=1 >> $jax.del',
                ],
            ])->setTitleIf($title === '', __('lte.delete_forever'));
        }

        return $this;
    }

    /**
     * Resource add button.
     * @param  string|null  $link
     * @param  string|null  $title
     * @param  string|null  $message
     * @param  null  $key
     * @return \Lar\Layout\Abstracts\Component|static|self||ButtonComponent
     */
    public function resourceRestore(string $link = null, string $title = null, string $message = null, $key = null)
    {
        if (!$link && $this->model) {
            $key = $this->realModel()->getRouteKey();

            if (
                $key &&
                isset($this->menu['link.destroy'])
            ) {
                $link = $this->menu['link.destroy']($key);
            }
        }

        if ($link) {
            return $this->warning(['fas fa-trash-restore-alt', $title ?? __('lte.restore')])->setDatas([
                'click' => 'alert::confirm',
                'params' => [
                    __(
                        'lte.restore_subject',
                        ['subject' => strtoupper($message ?? $this->model->getRouteKeyName()).($key ? ":{$key}?" : '')]
                    ),
                    $link.'?restore=1 >> $jax.del',
                ],
            ])->setTitleIf($title === '', __('lte.restore'));
        }

        return $this;
    }

    /**
     * @param  mixed  $ico
     * @param  array  $when
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function warning($ico = null, array $when = [])
    {
        return $this->btn('warning', $ico, $when);
    }

    /**
     * @param  string|array|null  $icon
     * @param  string|null  $form
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent
     */
    public function submit($icon = null, string $form = null)
    {
        if (!$icon) {
            $icon = ['fas fa-save', __('lte.submit')];
        }

        $datas = [
            'click' => 'submit',
        ];

        if ($form) {
            $datas['form'] = $form;
        }

        return $this->success($icon)->setDatas($datas);
    }

    /**
     * Resource add button.
     * @param  string|null  $link
     * @param  string|null  $title
     * @return \Lar\Layout\Abstracts\Component|ButtonComponent|ButtonsComponent
     */
    public function resourceAdd(string $link = null, string $title = null)
    {
        if (!$link && isset($this->menu['link.create'])) {
            $link = $this->menu['link.create']();
        }

        if ($link) {
            $return = $this->success(['fas fa-plus', $title ?? __('lte.add')]);
            $return->setTitleIf($title === '', __('lte.add'));
            $return->dataClick()->location($link);

            return $return;
        }

        return $this;
    }

    protected function mount()
    {
        // TODO: Implement mount() method.
    }
}
