<?php

namespace Admin\Layouts;

/**
 * Class LteLayout
 * @package Admin\Layouts
 */
class LteLayout extends DefaultLayout {

    /**
     * Inject theme assets. Injected before extensions.
     */
    protected function assets(): void
    {
        $this->styles[] = "theme/lte/theme.css";
        $this->bscripts[] = "theme/lte/theme.js";
    }

    /**
     * Configs before injection
     */
    protected function configs(): void
    {
        /** Theme body classes */
        $this->body_params['class'] = 'hold-transition sidebar-mini layout-fixed';

        if (\Admin::guest()) {

            $this->body_params['class'] = 'hold-transition login-page';
        }
    }
}