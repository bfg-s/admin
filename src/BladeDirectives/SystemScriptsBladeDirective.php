<?php

namespace Admin\BladeDirectives;

use Admin\Components\Component;
use Admin\Facades\AdminFacade;

class SystemScriptsBladeDirective
{
    /**
     * @return string
     */
    public static function directive(): string
    {
        return "<?php echo " . static::class . "::buildScripts(); ?>";
    }

    /**
     * @return string
     */
    public static function buildScripts(): string
    {
        $extensions = AdminFacade::extensions();

        $scripts = [];

        foreach ($extensions as $extension) {

            $scripts = array_merge($scripts, $extension->config()->getScripts());
        }

        foreach (Component::$components as $component) {

            if (method_exists($component, 'getScripts')) {

                $scripts = array_merge($scripts, $component::getScripts());
            }
        }

        $theme = AdminFacade::getTheme();

        $scripts = array_merge($scripts, $theme->getScripts());

        return implode("\n", array_map(
            fn (string $script) => "<script type=\"text/javascript\" src=\""
                . (str_starts_with($script, 'https://') || str_starts_with($script, 'http://') ? $script : asset($script))
                . "\"></script>",
            $scripts
        ));
    }
}