<?php

declare(strict_types=1);

namespace Admin\BladeDirectives;

use Admin\Facades\Admin;

/**
 * The class is responsible for the @adminSystemJs blade directive.
 */
class SystemJsBladeDirective
{
    /**
     * Property for storing custom admin panel scripts.
     *
     * @var array
     */
    protected static array $componentJs = [];

    /**
     * A function is a directive that is processed by the Blade template engine.
     *
     * @return string
     */
    public static function directive(): string
    {
        return "<?php echo ".static::class."::buildScripts(); ?>";
    }

    /**
     * A function that is responsible for generating scripts.
     *
     * @param  bool  $tag
     * @return string
     */
    public static function buildScripts(bool $tag = true): string
    {
        $extensions = Admin::extensions();

        $realHtml = [];
        $html = [];

        if ($respond = session('respond')) {
            $html[] = "exec($respond)";
        }

        foreach ($extensions as $extension) {
            $html[] = $extension->config()->js();
            $realHtml = array_merge($realHtml, $extension->config()->getScriptLines());
        }

        foreach (static::$componentJs as $componentJ) {
            $html[] = $componentJ;
        }

        if ($themeJs = Admin::getTheme()->js()) {
            $html[] = $themeJs;
        }

        $html[] = 'document.querySelector(\'[name="csrf-token"]\').setAttribute("content", "'.csrf_token().'")';

        $separator = ($tag ? "</script>" : "")
            ."\n"
            .($tag ? "<script>" : "");

        return ($tag ? "<script>" : "")
            .implode($separator, $html)
            .($tag ? "</script>" : "") . implode('', $realHtml);
    }

    /**
     * Add a custom script to the admin panel.
     *
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
