<?php

namespace Admin\BladeDirectives;

use Admin\Facades\AdminFacade;

class SystemJsBladeDirective
{
    /**
     * @var array
     */
    protected static array $componentJs = [];

    /**
     * @return string
     */
    public static function directive(): string
    {
        return "<?php echo " . static::class . "::buildScripts(); ?>";
    }

    /**
     * @param  bool  $tag
     * @return string
     */
    public static function buildScripts(bool $tag = true): string
    {
        $extensions = AdminFacade::extensions();

        $html = [];

        foreach ($extensions as $extension) {

            $html[] = $extension->config()->js();
        }

        foreach (static::$componentJs as $componentJ) {

            $html[] = $componentJ;
        }

        if ($themeJs = AdminFacade::getTheme()->js()) {

            $html[] = $themeJs;
        }

        $separator = ($tag ? "</script>" : "")
            . "\n"
            . ($tag ? "<script>" : "");

        return ($tag ? "<script>" : "")
            . implode($separator, $html)
            . ($tag ? "</script>" : "");
    }

    /**
     * @param  string  $js
     * @return void
     */
    public static function addComponentJs(string $js): void
    {
        if ($js) {
            static::$componentJs[] = $js;
        }
    }
}
