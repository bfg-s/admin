<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Explanation;

class FormFooterComponent extends Component
{
    /**
     * @var string|null
     */
    protected ?string $form_id = null;

    /**
     * @var string|null
     */
    protected ?string $btn_text = null;

    /**
     * @var string|null
     */
    protected ?string $btn_icon = null;

    /**
     * @var string|null
     */
    protected ?string $type = null;

    /**
     * @var ButtonsComponent|null
     */
    protected ?ButtonsComponent $group = null;
    /**
     * @var string
     */
    protected string $view = 'form-footer';
    /**
     * @var bool
     */
    protected bool $row = false;
    /**
     * @var bool
     */
    private bool $nav_redirect = true;

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
    }

    /**
     * @param  string  $id
     * @return $this
     */
    public function setFormId(string $id): static
    {
        $this->form_id = $id;

        return $this;
    }

    /**
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
     * @return $this
     */
    public function withOutRedirectRadios(): static
    {
        $this->nav_redirect = false;

        return $this;
    }

    /**
     * @param  string  $type
     * @return $this
     */
    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param  bool  $row
     * @return $this
     */
    public function setRow(bool $row): static
    {
        $this->row = $row;

        return $this;
    }

    /**
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
     * @return void
     */
    protected function mount(): void
    {
        //
    }
}
