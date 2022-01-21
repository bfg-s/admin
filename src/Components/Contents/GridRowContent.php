<?php

namespace Lar\LteAdmin\Components\Contents;

use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Components\Traits\BuildHelperTrait;
use Lar\LteAdmin\Components\Traits\FieldMassControlTrait;
use Lar\LteAdmin\Core\Traits\Delegable;
use Lar\LteAdmin\Core\Traits\Macroable;
use Lar\LteAdmin\Explanation;
use Lar\LteAdmin\Interfaces\ControllerContainerInterface;
use Lar\LteAdmin\Interfaces\ControllerContentInterface;
use Lar\LteAdmin\Page;
use Lar\Tagable\Events\onRender;

/**
 * @methods Lar\LteAdmin\Components\FieldComponent::$form_components (string $name, string $label = null, ...$params)
 * @mixin GridRowContentMacroList
 * @mixin GridRowContentMethods
 */
class GridRowContent extends DIV implements onRender, ControllerContainerInterface, ControllerContentInterface
{
    use FieldMassControlTrait, Macroable, BuildHelperTrait, Delegable;

    /**
     * @var string[]
     */
    protected $props = [
        'row',
    ];

    /**
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->explainForce(Explanation::new($delegates));

        $this->callConstructEvents();
    }

    /**
     * @param $name
     * @param $arguments
     * @return bool|GridRowContent|\Lar\LteAdmin\Components\FormGroupComponent|\Lar\Tagable\Tag|mixed|string
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if ($call = $this->call_group($name, $arguments)) {
            return $call;
        }

        return parent::__call($name, $arguments);
    }

    /**
     * @return mixed|void
     */
    public function onRender()
    {
        $this->callRenderEvents();
    }

    public static function registrationInToContainer(Page $page, array $delegates = [])
    {
        if ($page->getContent() instanceof CardContent) {
            $page->registerClass(
                $page->getClass(CardContent::class)->fullBody()->row($delegates)
            );
        } else {
            $page->registerClass(
                $page->getContent()->row($delegates)
            );
        }
    }
}
