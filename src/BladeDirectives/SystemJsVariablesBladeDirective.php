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
                'avatar' => admin()->avatar,
                'roles' => admin()->roles,
            ]);
            $home = route('admin.home');
            $uploader = route('admin.uploader');
            $uploader_drop = route('admin.uploader_drop');
            $export_excel = route('admin.export_excel');
            $export_csv = route('admin.export_csv');
            $custom_save = route('admin.custom_save');
            $call_callback = route('admin.call_callback');
            $load_lives = route('admin.load_lives');
            $save_image_order = route('admin.save_image_order');
            $translate = route('admin.translate');
            $load_chart_js = route('admin.load_chart_js');
            $load_select2 = route('admin.load_select2');
            $delete_ordered_image = route('admin.delete_ordered_image');
            $langs = json_encode(__('admin'), JSON_UNESCAPED_UNICODE);
            return <<<JS
window.darkMode = $dark;
window.admin = $admin;
window.langs = $langs;
window.home = "$home";
window.uploader = "$uploader";
window.uploader_drop = "$uploader_drop";
window.export_excel = "$export_excel";
window.export_csv = "$export_csv";
window.custom_save = "$custom_save";
window.call_callback = "$call_callback";
window.load_lives = "$load_lives";
window.save_image_order = "$save_image_order";
window.translate = "$translate";
window.load_chart_js = "$load_chart_js";
window.load_select2 = "$load_select2";
window.delete_ordered_image = "$delete_ordered_image";
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
