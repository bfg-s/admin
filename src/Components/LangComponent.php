<?php

namespace LteAdmin\Components;

use Lar\Tagable\Core\Extension\Content;
use LteAdmin\Traits\BuildHelperTrait;
use LteAdmin\Traits\Delegable;
use LteAdmin\Traits\FieldMassControlTrait;
use LteAdmin\Traits\Macroable;

/**
 * @methods LteAdmin\Components\FieldComponent::$inputs (string $name, string $label = null, ...$params)
 * @mixin LangComponentMethods
 */
class LangComponent extends Component
{
    use FieldMassControlTrait;
    use Macroable;
    use BuildHelperTrait;
    use Delegable;

    /**
     * @var array|null
     */
    protected $lang_list = null;

    /**
     * Lang constructor.
     * @param  array|null  $lang_list
     */
    public function __construct(array $lang_list = null)
    {
        $this->lang_list = $lang_list;

        parent::__construct();
    }

    protected function mount()
    {
        $inner = [];

        foreach ($this->content as $inner_input) {
            $inner_input = $inner_input->getOriginalValue();

            if (is_object($inner_input) && $inner_input instanceof FormGroupComponent) {
                $inn = [];
                $inner_input->unregister();
                foreach (array_values($this->lang_list ?: config('layout.languages', [])) as $lang) {
                    $input = clone $inner_input;
                    $input->set_name($input->get_name()."[{$lang}]");
                    $input->set_path($input->get_path().".{$lang}");
                    $input->set_id($input->get_id()."_{$lang}");
                    $input->set_title($input->get_title().' ['.strtoupper($lang).']');
                    $inn[] = $input->render();
                }

                $inner[] = new Content($inn[array_key_last($inn)], $this);
            } else {
                $inner[] = new Content($inner_input, $this);
            }
        }

        $this->content->setItems($inner);
    }
}
