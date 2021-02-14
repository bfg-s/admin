<?php

namespace Admin\Components;

use Bfg\Layout\View\Component;

/**
 * Class Wrapper
 * @package Admin\Components
 */
class Wrapper extends Component
{
    /**
     * The component alias name.
     *
     * @var string
     */
    public $componentName = "bfg::wrapper";

    /**
     * Wrapper constructor.
     * @param  mixed  $content
     */
    public function __construct(
        protected mixed $content
    ) {}

    /**
     * Inner append content
     */
    public function inner()
    {
        $this->text('Loading...');

        $content = (string)$this->content;

        if (!is_bfg_cr() && ! is_bfg_tr()) {

            respond('schema.content', $content);
        }

        Footer::create();
    }
}