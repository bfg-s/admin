<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Controllers\SystemController;
use Admin\Facades\Admin;

/**
 * A special observer component for live parts of the admin panel template.
 */
class LoadContentComponent extends Component
{
    /**
     * Component list for loading.
     *
     * @var array
     */
    public static array $componentsForLoad = [];

    /**
     * Set the component like invisible for API. return only content.
     *
     * @var bool
     */
    protected bool $invisibleForApi = true;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = "load-content";

    /**
     * WatchComponent constructor.
     *
     * @param  mixed  $callback
     */
    public function __construct(protected mixed $callback)
    {
        parent::__construct();
    }

    /**
     * Set the component like invisible for API. return only content.
     *
     * @return $this
     */
    public function contentOnly(): static
    {
        $this->view = "content-only";

        return $this;
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     * @throws \Throwable
     */
    protected function mount(): void
    {
        $this->dataLoad('load::content', [$this->model_name]);

        if (Admin::isApiMode() || request('_realtime') || SystemController::$isReferer) {
            $this->useCallBack();
        } else {
            static::$componentsForLoad[$this->model_name] = $this;
        }
    }

    /**
     * Reset component content.
     *
     * @return $this
     */
    public function resetContent(): static
    {
        $this->contents = [];

        return $this;
    }

    /**
     * Use the callback function.
     *
     * @return void
     * @throws \Throwable
     */
    public function useCallBack(): void
    {
        $this->forceDelegates(embedded_call($this->callback, [
            static::class => $this,
        ]));
    }

    public function contents()
    {
        return $this->contents;
    }

    /**
     * Component view data.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [

        ];
    }
}
