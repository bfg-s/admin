<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Delegates\Buttons;
use Illuminate\Support\Facades\Request;

/**
 * The button group component is responsible for all button groups that are in the admin panel.
 */
class ButtonsComponent extends Component
{
    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'buttons';

    /**
     * Class control_relation for the component.
     *
     * @var bool
     */
    protected bool $controlRelation = false;

    /**
     * Class return_relation for the component.
     *
     * @var bool
     */
    protected bool $returnRelation = false;

    /**
     * ButtonsComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->delegatesNow(...$delegates);
    }

    /**
     * Add a button to a group with a default type.
     *
     * @param $ico
     * @return ButtonComponent
     */
    public function default($ico = null): ButtonComponent
    {
        return $this->btn('default', $ico);
    }

    /**
     * Add a button to a group with the specified type and icon.
     *
     * @param  string  $type
     * @param  null  $ico
     * @return ButtonComponent
     */
    public function btn(string $type, $ico = null): ButtonComponent
    {
        $this->model();

        $btn = $this->createComponent(ButtonComponent::class)
            ->visibleType($type)->model($this->model);

        if ($ico && is_string($ico)) {
            $btn->icon($ico);
        } else {
            if ($ico && is_array($ico)) {
                $btn->icon($ico[0] ?? '');
                $btn->title($ico[1] ?? '');
            }
        }

        $this->appEnd($btn);

        return $btn;
    }

    /**
     * Add a page reload button.
     *
     * @param  string|null  $link
     * @param  string|null  $title
     * @return ButtonComponent
     */
    public function reload(string $link = null, string $title = null): ButtonComponent
    {
        $return = $this->secondary(['fas fa-redo-alt', $title ?? __('admin.refresh')]);
        $return->dataClick()->location($link ?? Request::getRequestUri());
        $return->setTitleIf($title === '', __('admin.refresh'));

        return $return;
    }

    /**
     * Add a button with type secondary to the group.
     *
     * @param  mixed|null  $ico
     * @return ButtonComponent
     */
    public function secondary(mixed $ico = null): ButtonComponent
    {
        return $this->btn('secondary', $ico);
    }

    /**
     * Add buttons to control order and nesting in a group.
     *
     * @return $this
     */
    public function nestable(): static
    {
        $this->info(['far fa-minus-square', __('admin.collapse_all')])->setDatas(['click' => 'nestable::collapse']);
        $this->primary(['far fa-plus-square', __('admin.expand_all')])->setDatas(['click' => 'nestable::expand']);

        return $this;
    }

    /**
     * Add a button with type info to the group.
     *
     * @param  mixed  $name
     * @return ButtonComponent
     */
    public function info($name = null): ButtonComponent
    {
        return $this->btn('info', $name);
    }

    /**
     * Add a button with type primary to the group.
     *
     * @param  mixed|null  $ico
     * @return ButtonComponent
     */
    public function primary(mixed $ico = null): ButtonComponent
    {
        return $this->btn('primary', $ico);
    }

    /**
     * Add a button with type dark to the group.
     *
     * @param  mixed|null  $ico
     * @return ButtonComponent
     */
    public function dark(mixed $ico = null): ButtonComponent
    {
        return $this->btn('dark', $ico);
    }

    /**
     * Add a resource list button to a group.
     *
     * @param  string|null  $link
     * @param  string|null  $title
     * @return $this|ButtonComponent
     */
    public function resourceList(string $link = null, string $title = null): ButtonComponent|static
    {
        if ($link || $this->menu->isResource()) {
            $return = $this->primary(['fas fa-list-alt', $title ?? __('admin.list')]);
            $return->dataClick()->location($link ?? $this->menu->getLinkIndex());
            $return->setTitleIf($title === '', __('admin.list'));

            return $return;
        }

        return $this;
    }

    /**
     * Add a resource edit button to the group.
     *
     * @param  string|null  $link
     * @param  string|null  $title
     * @return ButtonComponent|ButtonsComponent|Buttons
     */
    public function resourceEdit(string $link = null, string $title = null): ButtonComponent|Buttons|static
    {
        if (!$link && $this->model) {
            $key = $this->realModel()->getRouteKey();
            if (
                $key && $this->menu->isResource()
            ) {
                $link = $this->menu->getLinkEdit($key);
            }
        }

        if ($link) {
            if (!$this->menu) {
                return new Buttons();
            }
            $return = $this->success(['fas fa-edit', $title ?? __('admin.edit')]);
            $return->dataClick()->location($link);
            $return->setTitleIf($title === '', __('admin.edit'));

            return $return;
        }

        return $this;
    }

    /**
     * Add a button with type success to the group.
     *
     * @param  mixed|null  $ico
     * @return ButtonComponent
     */
    public function success(mixed $ico = null): ButtonComponent
    {
        return $this->btn('success', $ico);
    }

    /**
     * Add a resource information button to a group.
     *
     * @param  string|null  $link
     * @param  string|null  $title
     * @return ButtonComponent|ButtonsComponent|Buttons
     */
    public function resourceInfo(string $link = null, string $title = null): Buttons|ButtonComponent|static
    {
        if (!$link && $this->model) {
            $key = $this->realModel()->getRouteKey();

            if (
                $key && $this->menu->isResource()
            ) {
                $link = $this->menu->getLinkShow($key);
            }
        }

        if ($link) {
            if (!$this->menu) {
                return new Buttons();
            }
            $return = $this->info(['fas fa-info-circle', $title ?? __('admin.information')]);
            $return->dataClick()->location($link);
            $return->setTitleIf($title === '', __('admin.information'));

            return $return;
        }

        return $this;
    }

    /**
     * Add a button to delete a resource to a group.
     *
     * @param  string|null  $link
     * @param  string|null  $title
     * @param  string|null  $message
     * @param  null  $key
     * @param  array  $add
     * @return ButtonsComponent|Buttons
     */
    public function resourceDestroy(
        string $link = null,
        string $title = null,
        string $message = null,
        $key = null,
        array $add = []
    ): Buttons|ButtonComponent {
        if (!$link && $this->model) {
            $key = $this->realModel()->getRouteKey();

            if (
                $key && $this->menu->isResource()
            ) {
                $link = $this->menu->getLinkDestroy($key);
            }
        }

        if ($link) {
            if (!$this->menu) {
                return new Buttons();
            }

            $stay = $this->menu->isNotCurrent() ? (str_contains($link,
                    '?') ? '&' : '?').'_after=stay&'.http_build_query($add) : '?'.http_build_query($add);

            return $this->danger(['fas fa-trash-alt', $title ?? __('admin.delete')])->on_click('admin::delete_item', [
                __(
                    'admin.delete_subject',
                    ['subject' => strtoupper($message ?? $this->model->getRouteKeyName()).($key ? ":{$key}?" : '')]
                ),
                $link.$stay
            ])->setTitleIf($title === '', __('admin.delete'));
        }

        return $this;
    }

    /**
     * Add a button with the danger type to the group.
     *
     * @param  mixed|null  $ico
     * @return ButtonComponent|static
     */
    public function danger(mixed $ico = null): ButtonComponent|static
    {
        return $this->btn('danger', $ico);
    }

    /**
     * Add a button to force removal of a resource to a group.
     *
     * @param  string|null  $link
     * @param  string|null  $title
     * @param  string|null  $message
     * @param  null  $key
     * @return static|ButtonComponent
     */
    public function resourceForceDestroy(
        string $link = null,
        string $title = null,
        string $message = null,
        $key = null
    ): ButtonComponent|static {
        if (!$link && $this->model) {
            $key = $this->realModel()->getRouteKey();

            if (
                $key && $this->menu->isResource()
            ) {
                $link = $this->menu->getLinkDestroy($key);
            }
        }

        if ($link) {
            return $this->danger([
                'fas fa-trash-alt', $title ?? __('admin.delete_forever')
            ])->on_click('admin::delete_item', [
                __(
                    'admin.delete_forever_subject',
                    ['subject' => strtoupper($message ?? $this->model->getRouteKeyName()).($key ? ":{$key}?" : '')]
                ),
                $link.'?force=1'
            ])->setTitleIf($title === '', __('admin.delete_forever'));
        }

        return $this;
    }

    /**
     * Add a resource recovery button to the group.
     *
     * @param  string|null  $link
     * @param  string|null  $title
     * @param  string|null  $message
     * @param  null  $key
     * @return ButtonComponent|static
     */
    public function resourceRestore(
        string $link = null,
        string $title = null,
        string $message = null,
        $key = null
    ): ButtonComponent|static {
        if (!$link && $this->model) {
            $key = $this->realModel()->getRouteKey();

            if (
                $key && $this->menu->isResource()
            ) {
                $link = $this->menu->getLinkDestroy($key);
            }
        }

        if ($link) {
            return $this->success([
                'fas fa-trash-restore-alt', $title ?? __('admin.restore')
            ])->on_click('admin::delete_item', [
                __(
                    'admin.restore_subject',
                    ['subject' => strtoupper($message ?? $this->model->getRouteKeyName()).($key ? ":{$key}?" : '')]
                ),
                $link.'?restore=1'
            ])->setTitleIf($title === '', __('admin.restore'));
        }

        return $this;
    }

    /**
     * Add a button with the warning type to the group.
     *
     * @param  mixed|null  $ico
     * @return ButtonComponent
     */
    public function warning(mixed $ico = null): ButtonComponent
    {
        return $this->btn('warning', $ico);
    }

    /**
     * Add a submit form button to the group.
     *
     * @param  array|string|null  $icon
     * @param  string|null  $form
     * @return ButtonComponent
     */
    public function submit(array|string $icon = null, string $form = null): ButtonComponent
    {
        if (!$icon) {
            $icon = ['fas fa-save', __('admin.submit')];
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
     * Add a button to add a resource to a group.
     *
     * @param  string|null  $link
     * @param  string|null  $title
     * @return ButtonComponent|static
     */
    public function resourceAdd(string $link = null, string $title = null): ButtonComponent|static
    {
        if (!$link && $this->menu->isResource()) {
            $link = $this->menu->getLinkCreate();
        }

        if ($link) {
            $return = $this->success(['fas fa-plus', $title ?? __('admin.add')]);
            $return->setTitleIf($title === '', __('admin.add'));
            $return->dataClick()->location($link);

            return $return;
        }

        return $this;
    }

    /**
     * Set the control_relation property to true.
     *
     * @return $this
     */
    public function controlRelation(): static
    {
        $this->controlRelation = true;

        return $this;
    }

    /**
     * Set the return_relation property to true.
     *
     * @return $this
     */
    public function returnRelation(): static
    {
        $this->returnRelation = true;

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
            'controlRelation' => $this->controlRelation,
            'returnRelation' => $this->returnRelation,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
    }
}
