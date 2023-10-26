<?php

namespace Admin\Traits;

use Admin\Respond;
use Illuminate\Contracts\Support\Renderable;

trait DataTrait
{
    /**
     * Data rules.
     * @var array
     */
    protected array $data = [];

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_click(string|array $command, $value = null): static
    {
        return $this->xOnClick(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_submit(string|array $command, $value = null): static
    {
        return $this->xOnSubmit(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_dblclick(string|array $command, $value = null): static
    {
        return $this->xOnDblclick(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_change(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-change',
            Respond::create()->put($command, $value) //->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_blur(string|array $command, $value = null): static
    {
        return $this->xOnBlur(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_focus(string|array $command, $value = null): static
    {
        return $this->xOnFocus(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_formchange(string|array $command, $value = null): static
    {
        return $this->xOnFormchange(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_forminput(string|array $command, $value = null): static
    {
        return $this->xOnForminput(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_input(string|array $command, $value = null): static
    {
        return $this->xOnInput(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_keydown(string|array $command, $value = null): static
    {
        return $this->xOnKeydown(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_keypress(string|array $command, $value = null): static
    {
        return $this->xOnKeypress(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_keyup(string|array $command, $value = null): static
    {
        return $this->xOnKeyup(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mousedown(string|array $command, $value = null): static
    {
        return $this->xOnMousedown(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mousemove(string|array $command, $value = null): static
    {
        return $this->xOnMousemove(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mouseout(string|array $command, $value = null): static
    {
        return $this->xOnMouseout(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mouseover(string|array $command, $value = null): static
    {
        return $this->xOnMouseover(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mouseup(string|array $command, $value = null): static
    {
        return $this->xOnMouseup(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_mousewheel(string|array $command, $value = null): static
    {
        return $this->xOnMousewheel(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_hover(string|array $command, $value = null): static
    {
        return $this->xOnHover(
            Respond::create()->put($command, $value)->renderWithExecutor()
        );
    }

    /**
     * @param  string|array  $command
     * @param  null  $value
     * @return $this
     */
    public function on_load(string|array $command, $value = null): static
    {
        return $this->attr(
            'data-load',
            Respond::create()->put($command, $value) //->renderWithExecutor()
        );
    }
}
