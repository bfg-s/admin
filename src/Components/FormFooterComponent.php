<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Explanation;

/**
 * Footer component of the admin panel form.
 */
class FormFooterComponent extends Component
{
    /**
     * Admin panel form ID.
     *
     * @var string|null
     */
    protected string|null $form_id = null;

    /**
     * Custom button text.
     *
     * @var string|null
     */
    protected string|null $btn_text = null;

    /**
     * Custom button icon.
     *
     * @var string|null
     */
    protected string|null $btn_icon = null;

    /**
     * The current operation type, edit or create.
     *
     * @var string|null
     */
    protected string|null $type = null;

    /**
     * Default button group.
     *
     * @var ButtonsComponent|null
     */
    protected ButtonsComponent|null $group = null;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'form-footer';

    /**
     * Determines whether to add the "row" class to the CSS.
     *
     * @var bool
     */
    protected bool $row = false;

    /**
     * Determines whether redirect form navigation is enabled.
     *
     * @var bool
     */
    private bool $nav_redirect = true;

    /**
     * FormFooterComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->explainForce(Explanation::new($delegates));

        if (FormComponent::$last_id) {
            $this->setFormId(FormComponent::$last_id);
        }
    }

    /**
     * Set the form ID.
     *
     * @param  string  $id
     * @return $this
     */
    public function setFormId(string $id): static
    {
        $this->form_id = $id;

        return $this;
    }

    /**
     * Describe the default button.
     *
     * @param  string  $text
     * @param  string|null  $icon
     * @return $this
     */
    public function defaultBtn(string $text, string $icon = null): static
    {
        $this->btn_text = $text;

        if ($icon) {
            $this->btn_icon = $icon;
        }

        return $this;
    }

    /**
     * Disable redirect form navigation.
     *
     * @return $this
     */
    public function withOutRedirectRadios(): static
    {
        $this->nav_redirect = false;

        return $this;
    }

    /**
     * Set the current operation type.
     *
     * @param  string  $type
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Enable or disable the CSS class "row".
     *
     * @param  bool  $row
     * @return $this
     */
    public function setRow(bool $row): static
    {
        $this->row = $row;

        return $this;
    }

    /**
     * Create standard buttons for the form.
     *
     * @return $this
     */
    public function createDefaultCRUDFooter(): static
    {
        $this->group = new ButtonsComponent();

        $type = $this->type ?? $this->page->resource_type;
        $menu = $this->menu;

        if ($type === 'edit' || isset($menu['post'])) {
            $this->group->success([$this->btn_icon ?? 'fas fa-save', __($this->btn_text ?? 'admin.save')])->setDatas([
                'click' => 'submit',
                'form' => $this->form_id,
            ]);
        } elseif ($type === 'create') {
            $this->group->success([$this->btn_icon ?? 'fas fa-plus', __($this->btn_text ?? 'admin.add')])->setDatas([
                'click' => 'submit',
                'form' => $this->form_id,
            ]);
        } else {
            $this->group->submit(null, $this->form_id);
        }

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
            'group' => $this->group,
            'row' => $this->row,
            'type' => $this->type ?? $this->page->resource_type,
            'nav_redirect' => $this->nav_redirect,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        //
    }
}
