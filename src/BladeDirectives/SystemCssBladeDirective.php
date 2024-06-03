<?php

declare(strict_types=1);

namespace Admin\BladeDirectives;

use Admin\Facades\Admin;

/**
 * The class responsible for the blade directive @adminSystemCss
 */
class SystemCssBladeDirective
{
    /**
     * Property for storing custom admin panel styles.
     *
     * @var array
     */
    protected static array $componentCss = [];

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

        $html = [];

        foreach ($extensions as $extension) {
            $html[] = $extension->config()->css();
        }

        foreach (static::$componentCss as $componentCs) {
            $html[] = $componentCs;
        }

        if ($themeCss = Admin::getTheme()->css()) {
            $html[] = $themeCss;
        }

        return "<style>".implode("</style>\n<style>", $html).'</style>';
    }

    /**
     * Add a custom style to the admin panel.
     *
     * @param  string  $css
     * @return void
     */
    public static function addComponentCss(string $css): void
    {
        if ($css) {
            static::$componentCss[] = $css;
        }
    }
}
