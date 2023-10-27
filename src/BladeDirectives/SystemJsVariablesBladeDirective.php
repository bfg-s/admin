<?php

namespace Admin\BladeDirectives;

use Admin\Facades\AdminFacade;

class SystemJsVariablesBladeDirective
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
    public static function buildScripts(): string
    {
        $html = [
            static::html()
        ];

        $separator = "</script>"
            . "\n"
            . "<script>";

        return "<script>"
            . implode($separator, $html)
            . "</script>";
    }

    protected static function html()
    {
        $dark = json_encode(admin_repo()->isDarkMode);
        if (AdminFacade::guest()) {
            return <<<JS
window.darkMode = $dark;
JS;
        } else {
            $admin = json_encode([
                'id' => admin()->id,
                'login' => admin()->login,
                'email' => admin()->email,
                'name' => admin()->name,
                'roles' => admin()->roles,
            ]);
            $home = route('admin.home');
            $uploader = route('admin.uploader');
            $export_excel = route('admin.export_excel');
            $export_csv = route('admin.export_csv');
            $custom_save = route('admin.custom_save');
            $call_callback = route('admin.call_callback');
            $load_lives = route('admin.load_lives');
            return <<<JS
window.darkMode = $dark;
window.admin = $admin;
window.home = "$home";
window.uploader = "$uploader";
window.export_excel = "$export_excel";
window.export_csv = "$export_csv";
window.custom_save = "$custom_save";
window.call_callback = "$call_callback";
window.load_lives = "$load_lives";
JS;
        }
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
