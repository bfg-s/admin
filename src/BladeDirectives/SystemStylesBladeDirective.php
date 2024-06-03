<?php

declare(strict_types=1);

namespace Admin\BladeDirectives;

use Admin\Components\Component;
use Admin\Facades\Admin;

/**
 * The class is responsible for the blade directive @adminSystemStyles.
 */
class SystemStylesBladeDirective
{
    /**
     * A function is a directive that is processed by the Blade template engine.
     *
     * @return string
     */
    public static function directive(): string
    {
        return "<?php echo ".static::class."::buildStyles(); ?>";
    }

    /**
     * A function that is responsible for generating styles.
     *
     * @return string
     */
    public static function buildStyles(): string
    {
        $extensions = Admin::extensions();

        $styles = [];

        foreach ($extensions as $extension) {
            $styles = array_merge($styles, $extension->config()->getStyles());
        }

        foreach (Component::$components as $component) {
            if (method_exists($component, 'getStyles')) {
                $styles = array_merge($styles, $component::getStyles());
            }
        }

        $theme = Admin::getTheme();

        $styles = array_merge($styles, $theme->getStyles());

        return implode("\n", array_map(
            fn(string $style) => "<link rel=\"stylesheet\" href=\""
                .(str_starts_with($style, 'https://') || str_starts_with($style, 'http://') ? $style : asset($style))
                ."\">",
            $styles
        ));
    }
}
