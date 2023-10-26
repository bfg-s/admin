<?php

namespace Admin\Components;

use Lar\Tagable\Core\Extension\Content;

class LangComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'content-only';

    /**
     * @var array|null
     */
    protected ?array $lang_list = null;

    /**
     * Lang constructor.
     * @param  array|null  $lang_list
     */
    public function __construct(array $lang_list = null)
    {
        $this->lang_list = $lang_list;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        //$inner = [];

        foreach ($this->contents as $key => $inner_input) {
            //$inner_input = $inner_input->getOriginalValue();

            if ($inner_input instanceof FormGroupComponent) {

                foreach ($this->lang_list ?: config('layout.languages', []) as $lang) {
                    $input = clone $inner_input;
                    $input->set_name($input->get_name()."[{$lang}]");
                    $input->set_path($input->get_path().".{$lang}");
                    $input->set_id($input->get_id()."_{$lang}");
                    $input->set_title($input->get_title().' ['.strtoupper($lang).']');
                    $this->appEnd(
                        $input->render()
                    );
                }

                unset($this->contents[$key]);

                //$inner[] = new Content($inn[array_key_last($inn)], $this);
                //$this->appEnd($inn[array_key_last($inn)]);
            } else {
                //$inner[] = new Content($inner_input, $this);
                $this->appEnd($inner_input);
            }
        }

        //$this->content->setItems($inner);
    }
}
