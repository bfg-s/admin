<?php

namespace Admin\Components\Inputs;

use Illuminate\Support\Facades\Route;

class PasswordInput extends Input
{
    /**
     * @var string
     */
    protected $type = 'password';

    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-key';

    /**
     * @var string
     */
    protected string $autocomplete = 'new-password';

    /**
     * @param  string|null  $label
     * @return $this
     */
    public function confirm(string $label = null): static
    {
        $controller = Route::current()->controller;
        if (property_exists($controller, 'crypt_fields')) {

            $controller::$crypt_fields[] = $this->name;
        }

        $this->is_equal_to("#input_{$this->name}_confirmation")->confirmed()->crypt();

        $info = null;

        if (!$label && $this->title) {
            $label = $this->title;

            $info = __('admin.confirmation');
        }

        $p = $this->parent_field;

        $p = $p->password($this->name.'_confirmation', $label)
            ->icon($this->icon)->mergeDataList($this->data)->is_equal_to("#input_{$this->name}");

        if ($info) {
            $p->info($info);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    protected function create_value(): mixed
    {
        return $this->value;
    }
}
