<?php

namespace Lar\LteAdmin\Segments;

use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Interfaces\SegmentContainerInterface;

/**
 * Class Container
 * @package App\LteAdmin\Segments
 */
class Container implements SegmentContainerInterface {

    /**
     * @var string
     */
    protected $layout;

    /**
     * @var string
     */
    protected $content_yield = "content";

    /**
     * @var Component
     */
    protected $component;

    /**
     * Container constructor.
     * @param  \Closure  $warp
     */
    public function __construct(\Closure $warp)
    {
        $this->layout = config('lte.paths.view', 'admin').'.page';
        $this->component = DIV::create()->only_content();
        $warp($this->component);
    }

    /**
     * @return string|void
     */
    public function render()
    {
        return view('lte::wrapper.container', [
            'layout' => $this->layout,
            'yield' => $this->content_yield,
            'component' => $this->component
        ]);
    }

    /**
     * @param  mixed  ...$params
     * @return static
     */
    public static function create(...$params)
    {
        return new static(...$params);
    }
}