<?php

declare(strict_types=1);

namespace Admin\BladeDirectives;

use Admin\Facades\AdminFacade;

class SystemMetasBladeDirective
{
    /**
     * @return string
     */
    public static function directive(): string
    {
        return "<?php echo ".static::class."::buildMetas(); ?>";
    }

    /**
     * @return string
     */
    public static function buildMetas(): string
    {
        $extensions = AdminFacade::extensions();

        $metas = [];

        foreach ($extensions as $extension) {
            $metas = array_merge($metas, $extension->config()->metas());
        }

        $theme = AdminFacade::getTheme();

        $metas = array_merge($metas, $theme->metas());

        return implode("\n", $metas);
    }
}
