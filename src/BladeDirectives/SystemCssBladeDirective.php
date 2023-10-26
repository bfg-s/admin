<?php

namespace Admin\BladeDirectives;

use Admin\Facades\AdminFacade;

class SystemCssBladeDirective
{
    /**
     * @var array
     */
    protected static array $componentCss = [];

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

        $html = [];

        foreach ($extensions as $extension) {

            $html[] = $extension->config()->css();
        }

        foreach (static::$componentCss as $componentCs) {

            $html[] = $componentCs;
        }

        if ($themeCss = AdminFacade::getTheme()->css()) {

            $html[] = $themeCss;
        }

        return "<style>" . implode("</style>\n<style>", $html) . '</style>';
    }

    /**
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
