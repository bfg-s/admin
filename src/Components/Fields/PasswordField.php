<?php

namespace Admin\Components\Fields;

use Route;

class PasswordField extends InputField
{
    /**
     * @var string
     */
    protected $type = 'password';

    /**
     * @var string
     */
    protected $icon = 'fas fa-key';

    /**
     * @var array
     */
    protected $params = [
        ['autocomplete' => 'new-password'],
    ];

    /**
     * @param  string|null  $label
     * @return $this
     */
    public function confirm(string $label = null)
    {
        Route::current()->controller::$crypt_fields[] = $this->name;

        $this->_front_rule_equal_to("#input_{$this->name}_confirmation")->confirmed()->crypt();

        $info = null;

        if (!$label && $this->title) {
            $label = $this->title;

            $info = __('admin.confirmation');
        }

        $p = $this->parent_field;

        if (!$p) {
            $p = $this->_();
        }

        $p = $p->password($this->name.'_confirmation', $label, ...$this->params)
            ->icon($this->icon)->mergeDataList($this->data)->_front_rule_equal_to("#input_{$this->name}");

        if ($info) {
            $p->info($info);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    protected function create_value()
    {
        return $this->value;
    }
}
