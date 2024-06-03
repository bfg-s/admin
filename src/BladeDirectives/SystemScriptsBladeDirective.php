<?php

declare(strict_types=1);

namespace Admin\BladeDirectives;

use Admin\Components\Component;
use Admin\Facades\Admin;

/**
 * The class is responsible for the blade directive @adminSystemScripts.
 */
class SystemScriptsBladeDirective
{
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
     * @return string
     */
    public static function buildScripts(): string
    {
        $extensions = Admin::extensions();

        $theme = Admin::getTheme();

        $scripts = $theme->getFirstScripts();

        foreach ($extensions as $extension) {
            $scripts = array_merge($scripts, $extension->config()->getScripts());
        }

        foreach (Component::$components as $component) {
            if (method_exists($component, 'getScripts')) {
                $scripts = array_merge($scripts, $component::getScripts());
            }
        }

        $scripts = array_merge($scripts, $theme->getScripts());

        $scripts[] = 'admin/js/app.js';

        return implode("\n", array_map(
            fn(string $script) => "<script type=\"text/javascript\" src=\""
                .(str_starts_with($script, 'https://') || str_starts_with($script,
                    'http://') ? $script : asset($script))
                ."\"></script>",
            $scripts
        ));
    }
}
