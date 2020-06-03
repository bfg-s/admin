<?php

namespace Lar\LteAdmin\Core;

use Lar\Layout\Abstracts\Component;
use Lar\LteAdmin\Segments\Tagable\Alert;
use Lar\LteAdmin\Segments\Tagable\ButtonGroup;
use Lar\LteAdmin\Segments\Tagable\Card;
use Lar\LteAdmin\Segments\Tagable\Col;
use Lar\LteAdmin\Segments\Tagable\Field;
use Lar\LteAdmin\Segments\Tagable\Form;
use Lar\LteAdmin\Segments\Tagable\FormFooter;
use Lar\LteAdmin\Segments\Tagable\InfoBox;
use Lar\LteAdmin\Segments\Tagable\Row;
use Lar\LteAdmin\Segments\Tagable\SmallBox;
use Lar\LteAdmin\Segments\Tagable\Table;

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
        'table' => Table::class,
        'button_group' => ButtonGroup::class,
        'alert' => Alert::class,
        'small_box' => SmallBox::class,
        'info_box' => InfoBox::class,
    ];

    /**
     * TagableComponent constructor.
     */
    public function __construct()
    {
        static::injectCollection($this->collection);
    }
}