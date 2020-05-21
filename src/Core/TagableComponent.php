<?php

namespace Lar\LteAdmin\Core;

use Lar\Layout\Abstracts\Component;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Col;
use Lar\LteAdmin\Segments\Tagable\Field;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\FormFooter;
use Lar\LteAdmin\Segments\Tagable\Row;

/**
 * Class TagableComponent
 * @package Lar\LteAdmin\Core
 */
class TagableComponent extends Component {

    /**
     * @var string[]
     */
    protected $collection = [
        'row' => Row::class,
        'col' => Col::class,
        'card' => Card::class,
        'form' => Form::class,
        'form_footer' => FormFooter::class,
        'field' => Field::class,
    ];

    /**
     * TagableComponent constructor.
     */
    public function __construct()
    {
        static::injectCollection($this->collection);
    }
}