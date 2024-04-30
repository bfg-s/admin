<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Controllers\Controller;
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
        /** @var Controller $controller */
        $controller = Route::current()->controller;
        if (property_exists($controller, 'crypt_fields')) {
            $controller::addCryptField($this->name);
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
