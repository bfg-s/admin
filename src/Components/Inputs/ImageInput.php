<?php

declare(strict_types=1);

namespace Admin\Components\Inputs;

use Admin\Controllers\Controller;
use Intervention\Image\Interfaces\FontInterface;

/**
 * Input the admin panel to select pictures.
 */
class ImageInput extends FileInput
{
    /**
     * Add the specified modifier for the selected image.
     *
     * @param  string  $name
     * @param  mixed  ...$attributes
     * @return $this
     */
    public function addImageModifier(string $name, ...$attributes): static
    {
        if ($this->admin_controller) {
            /** @var Controller $controller */
            $controller = $this->controller;
            $controller::addImageModifier($this->path, $name, $attributes);
        }

        return $this;
    }

    /**
     * Add a "resize" modifier for the selected image.
     *
     * @param  int|null  $width
     * @param  int|null  $height
     * @return $this
     */
    public function resize(?int $width, ?int $height): static
    {
        $this->addImageModifier(__FUNCTION__, $width, $height);

        return $this;
    }

    /**
     * Add a "resizeDown" modifier for the selected image.
     *
     * @param  int|null  $width
     * @param  int|null  $height
     * @return $this
     */
    public function resizeDown(?int $width, ?int $height): static
    {
        $this->addImageModifier(__FUNCTION__, $width, $height);

        return $this;
    }

    /**
     * Add a "scale" modifier for the selected image.
     *
     * @param  int|null  $width
     * @param  int|null  $height
     * @return $this
     */
    public function scale(?int $width, ?int $height): static
    {
        $this->addImageModifier(__FUNCTION__, $width, $height);

        return $this;
    }

    /**
     * Add a "scaleDown" modifier for the selected image.
     *
     * @param  int|null  $width
     * @param  int|null  $height
     * @return $this
     */
    public function scaleDown(?int $width, ?int $height): static
    {
        $this->addImageModifier(__FUNCTION__, $width, $height);

        return $this;
    }

    /**
     * Add a "cover" modifier for the selected image.
     *
     * @param  int  $width
     * @param  int  $height
     * @param  string  $position
     * @return $this
     */
    public function cover(int $width, int $height, string $position = 'center'): static
    {
        $this->addImageModifier(__FUNCTION__, $width, $height, $position);

        return $this;
    }

    /**
     * Add a "coverDown" modifier for the selected image.
     *
     * @param  int  $width
     * @param  int  $height
     * @param  string  $position
     * @return $this
     */
    public function coverDown(int $width, int $height, string $position = 'center'): static
    {
        $this->addImageModifier(__FUNCTION__, $width, $height, $position);

        return $this;
    }

    /**
     * Add a "pad" modifier for the selected image.
     *
     * @param  int  $width
     * @param  int  $height
     * @param  string  $background
     * @param  string  $position
     * @return $this
     */
    public function pad(int $width, int $height, string $background = 'ffffff', string $position = 'center'): static
    {
        $this->addImageModifier(__FUNCTION__, $width, $height, $background, $position);

        return $this;
    }

    /**
     * Add a "contain" modifier for the selected image.
     *
     * @param  int  $width
     * @param  int  $height
     * @param  string  $background
     * @param  string  $position
     * @return $this
     */
    public function contain(int $width, int $height, string $background = 'ffffff', string $position = 'center'): static
    {
        $this->addImageModifier(__FUNCTION__, $width, $height, $background, $position);

        return $this;
    }

    /**
     * Add a "crop" modifier for the selected image.
     *
     * @param  int  $width
     * @param  int  $height
     * @param  int  $offset_x
     * @param  int  $offset_y
     * @param  string  $background
     * @param  string  $position
     * @return $this
     */
    public function crop(
        int $width,
        int $height,
        int $offset_x = 0,
        int $offset_y = 0,
        mixed $background = 'ffffff',
        string $position = 'top-left'
    ): static {
        $this->addImageModifier(__FUNCTION__, $width, $height, $offset_x, $offset_y, $background, $position);

        return $this;
    }

    /**
     * Add a "resizeCanvas" modifier for the selected image.
     *
     * @param  int|null  $width
     * @param  int|null  $height
     * @param  string  $background
     * @param  string  $position
     * @return $this
     */
    public function resizeCanvas(
        ?int $width,
        ?int $height,
        mixed $background = 'ffffff',
        string $position = 'center'
    ): static {
        $this->addImageModifier(__FUNCTION__, $width, $height, $background, $position);

        return $this;
    }

    /**
     * Add a "resizeCanvasRelative" modifier for the selected image.
     *
     * @param  int|null  $width
     * @param  int|null  $height
     * @param  string  $background
     * @param  string  $position
     * @return $this
     */
    public function resizeCanvasRelative(
        ?int $width,
        ?int $height,
        mixed $background = 'ffffff',
        string $position = 'center'
    ): static {
        $this->addImageModifier(__FUNCTION__, $width, $height, $background, $position);

        return $this;
    }

    /**
     * Add a "place" modifier for the selected image.
     *
     * @param  mixed  $element
     * @param  string  $position
     * @param  int  $offset_x
     * @param  int  $offset_y
     * @param  int  $opacity
     * @return $this
     */
    public function place(
        mixed $element,
        string $position = 'top-left',
        int $offset_x = 0,
        int $offset_y = 0,
        int $opacity = 100
    ): static {
        $this->addImageModifier(__FUNCTION__, $element, $position, $offset_x, $offset_y, $opacity);

        return $this;
    }

    /**
     * Add a "brightness" modifier for the selected image.
     *
     * @param  int  $level
     * @return $this
     */
    public function brightness(int $level): static
    {
        $this->addImageModifier(__FUNCTION__, $level);

        return $this;
    }

    /**
     * Add a "contrast" modifier for the selected image.
     *
     * @param  int  $level
     * @return $this
     */
    public function contrast(int $level): static
    {
        $this->addImageModifier(__FUNCTION__, $level);

        return $this;
    }

    /**
     * Add a "gamma" modifier for the selected image.
     *
     * @param  float  $gamma
     * @return $this
     */
    public function gamma(float $gamma): static
    {
        $this->addImageModifier(__FUNCTION__, $gamma);

        return $this;
    }

    /**
     * Add a "colorize" modifier for the selected image.
     *
     * @param  int  $red
     * @param  int  $green
     * @param  int  $blue
     * @return $this
     */
    public function colorize(int $red = 0, int $green = 0, int $blue = 0): static
    {
        $this->addImageModifier(__FUNCTION__, $red, $green, $blue);

        return $this;
    }

    /**
     * Add a "flop" modifier for the selected image.
     *
     * @return $this
     */
    public function flop(): static
    {
        $this->addImageModifier(__FUNCTION__);

        return $this;
    }

    /**
     * Add a "flip" modifier for the selected image.
     *
     * @return $this
     */
    public function flip(): static
    {
        $this->addImageModifier(__FUNCTION__);

        return $this;
    }

    /**
     * Add a "rotate" modifier for the selected image.
     *
     * @param  float  $angle
     * @param  mixed  $background
     * @return $this
     */
    public function rotate(float $angle, mixed $background = 'ffffff'): static
    {
        $this->addImageModifier(__FUNCTION__, $angle, $background);

        return $this;
    }

    /**
     * Add a "blur" modifier for the selected image.
     *
     * @param  int  $amount
     * @return $this
     */
    public function blur(int $amount = 5): static
    {
        $this->addImageModifier(__FUNCTION__, $amount);

        return $this;
    }

    /**
     * Add a "sharpen" modifier for the selected image.
     *
     * @param  int  $amount
     * @return $this
     */
    public function sharpen(int $amount = 10): static
    {
        $this->addImageModifier(__FUNCTION__, $amount);

        return $this;
    }

    /**
     * Add a "invert" modifier for the selected image.
     *
     * @return $this
     */
    public function invert(): static
    {
        $this->addImageModifier(__FUNCTION__);

        return $this;
    }

    /**
     * Add a "pixelate" modifier for the selected image.
     *
     * @param  int  $size
     * @return $this
     */
    public function pixelate(int $size): static
    {
        $this->addImageModifier(__FUNCTION__, $size);

        return $this;
    }

    /**
     * Add a "reduceColors" modifier for the selected image.
     *
     * @param  int  $limit
     * @param  mixed  $background
     * @return $this
     */
    public function reduceColors(int $limit, mixed $background = 'transparent'): static
    {
        $this->addImageModifier(__FUNCTION__, $limit, $background);

        return $this;
    }

    /**
     * Add a "writingText" modifier for the selected image.
     *
     * @param  string  $text
     * @param  int  $x
     * @param  int  $y
     * @param  callable|FontInterface  $font
     * @return $this
     */
    public function writingText(string $text, int $x, int $y, callable|FontInterface $font): static
    {
        $this->addImageModifier('text', $text, $x, $y, $font);

        return $this;
    }

    /**
     * Add a "fill" modifier for the selected image.
     *
     * @param  mixed  $color
     * @param  int|null  $x
     * @param  int|null  $y
     * @return $this
     */
    public function fill(mixed $color, ?int $x = null, ?int $y = null): static
    {
        $this->addImageModifier(__FUNCTION__, $color, $x, $y);

        return $this;
    }

    /**
     * Add a "drawPixel" modifier for the selected image.
     *
     * @param  int  $x
     * @param  int  $y
     * @param  mixed|null  $color
     * @return $this
     */
    public function drawPixel(int $x, int $y, mixed $color = null): static
    {
        $this->addImageModifier(__FUNCTION__, $x, $y, $color);

        return $this;
    }

    /**
     * Add a "drawRectangle" modifier for the selected image.
     *
     * @param  int  $x
     * @param  int  $y
     * @param  callable|null  $init
     * @return $this
     */
    public function drawRectangle(int $x, int $y, ?callable $init = null): static
    {
        $this->addImageModifier(__FUNCTION__, $x, $y, $init);

        return $this;
    }

    /**
     * Add a "drawEllipse" modifier for the selected image.
     *
     * @param  int  $x
     * @param  int  $y
     * @param  callable|null  $init
     * @return $this
     */
    public function drawEllipse(int $x, int $y, ?callable $init = null): static
    {
        $this->addImageModifier(__FUNCTION__, $x, $y, $init);

        return $this;
    }

    /**
     * Add a "drawCircle" modifier for the selected image.
     *
     * @param  int  $x
     * @param  int  $y
     * @param  callable|null  $init
     * @return $this
     */
    public function drawCircle(int $x, int $y, ?callable $init = null): static
    {
        $this->addImageModifier(__FUNCTION__, $x, $y, $init);

        return $this;
    }

    /**
     * Add a "drawLine" modifier for the selected image.
     *
     * @param  callable|null  $init
     * @return $this
     */
    public function drawLine(?callable $init = null): static
    {
        $this->addImageModifier(__FUNCTION__, $init);

        return $this;
    }

    /**
     * Add a "drawPolygon" modifier for the selected image.
     *
     * @param  callable  $init
     * @return $this
     */
    public function drawPolygon(callable $init): static
    {
        $this->addImageModifier(__FUNCTION__, $init);

        return $this;
    }

    /**
     * Add a "sliceAnimation" modifier for the selected image.
     *
     * @param  int  $offset
     * @param  int|null  $length
     * @return $this
     */
    public function sliceAnimation(int $offset, null|int $length = null): static
    {
        $this->addImageModifier(__FUNCTION__, $offset, $length);

        return $this;
    }

    /**
     * Add a "setLoops" modifier for the selected image.
     *
     * @param  int  $count
     * @return $this
     */
    public function setLoops(int $count): static
    {
        $this->addImageModifier(__FUNCTION__, $count);

        return $this;
    }

    /**
     * Add a "removeAnimation" modifier for the selected image.
     *
     * @param  int|string  $position
     * @return $this
     */
    public function removeAnimation(int|string $position = 0): static
    {
        $this->addImageModifier(__FUNCTION__, $position);

        return $this;
    }

    /**
     * After construct event.
     *
     * @return void
     */
    protected function after_construct(): void
    {
        parent::after_construct();
        $this->exts('jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg', 'webp');
        $this->image();
    }
}
