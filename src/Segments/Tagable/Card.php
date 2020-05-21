<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\ButtonGroup;
use Lar\LteAdmin\Components\Card as CardComponent;
use Lar\Tagable\Events\onRender;

/**
 * Class Col
 * @package Lar\LteAdmin\Segments\Tagable
 */
class Card extends CardComponent implements onRender {

    /**
     * @var bool
     */
    public $opened_mode = false;

    /**
     * @var bool
     */
    protected $auto_tools = false;

    /**
     * @var Form
     */
    protected $form;

    /**
     * Card constructor.
     * @param  mixed  ...$params
     */
    public function __construct(...$params)
    {
        $closures = [];

        foreach ($params as $key => $param) {

            if ($param instanceof \Closure) {

                $closures[] = $param;

                unset($params[$key]);
            }
        }

        $params = array_values($params);

        if (!isset($params[0]) || !$params[0]) $params[0] = '';

        parent::__construct(...$params);

        $this->group = new ButtonGroup();

        $this->tools = $this->head_obj->div(['card-tools']);

        foreach ($closures as $closure) {

            $closure($this);
        }
    }

    /**
     * @param  mixed  ...$params
     * @return DIV
     */
    public function body(...$params)
    {
        return $this->div(['card-body'], ...$params);
    }

    /**
     * @param  mixed  ...$params
     * @return Card
     */
    public function bodyForm(...$params)
    {
        $this->form = $this->body()->form(...$params);

        return $this;
    }

    /**
     * @param  mixed  ...$params
     * @return DIV
     */
    public function footer(...$params)
    {
        return $this->div(['card-footer'], ...$params);
    }

    /**
     * @param  mixed  ...$params
     * @return $this
     */
    public function footerForm(...$params)
    {
        $this->form_footer(...$params);

        return $this;
    }

    /**
     * @return $this
     */
    public function defaultTools()
    {
        $this->makeDefaultTools();

        return $this;
    }

    /**
     * @param  mixed  ...$params
     * @return \Lar\LteAdmin\Components\ButtonGroup
     */
    public function tools(...$params)
    {
        return $this->group;
    }

    /**
     * @param  mixed  ...$params
     * @return \Lar\LteAdmin\Components\ButtonGroup
     */
    public function group(...$params)
    {
        $group = ButtonGroup::create(...$params);

        $this->tools->appEnd($group);

        return $group;
    }

    /**
     * @return mixed|void
     */
    public function onRender()
    {
        $this->tools->appEnd($this->group);
    }
}