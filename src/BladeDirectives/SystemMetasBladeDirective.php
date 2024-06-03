<?php

declare(strict_types=1);

namespace Admin\BladeDirectives;

use Admin\Facades\Admin;

/**
 * The class is responsible for the blade directive @adminSystemMetas.
 */
class SystemMetasBladeDirective
{
    /**
     * A function is a directive that is processed by the Blade template engine.
     *
     * @return string
     */
    public static function directive(): string
    {
        return "<?php echo ".static::class."::buildMetas(); ?>";
    }

    /**
     * A function that is responsible for generating meta tags.
     *
     * @return string
     */
    public static function buildMetas(): string
    {
        $extensions = Admin::extensions();

        $metas = [];

        foreach ($extensions as $extension) {
            $metas = array_merge($metas, $extension->config()->metas());
        }

        $theme = Admin::getTheme();

        $metas = array_merge($metas, $theme->metas());

        return implode("\n", $metas);
    }
}
