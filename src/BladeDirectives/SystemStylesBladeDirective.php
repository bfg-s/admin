<?php

namespace Admin\BladeDirectives;

use Admin\Components\Component;
use Admin\Facades\AdminFacade;

class SystemStylesBladeDirective
{
    /**
     * @return string
     */
    public static function directive(): string
    {
        return "<?php echo " . static::class . "::buildStyles(); ?>";
    }

    /**
     * @return string
     */
    public static function buildStyles(): string
    {
        $extensions = AdminFacade::extensions();

        $styles = [];

        foreach ($extensions as $extension) {

            $styles = array_merge($styles, $extension->config()->getStyles());
        }

        foreach (Component::$components as $component) {

            if (method_exists($component, 'getStyles')) {

                $styles = array_merge($styles, $component::getStyles());
            }
        }

        $theme = AdminFacade::getTheme();

        $styles = array_merge($styles, $theme->getStyles());

        return implode("\n", array_map(
            fn (string $style) => "<link rel=\"stylesheet\" href=\""
                . (str_starts_with($style, 'https://') || str_starts_with($style, 'http://') ? $style : asset($style))
                . "\">",
            $styles
        ));
    }
}
