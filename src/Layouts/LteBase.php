<?php

namespace Lar\LteAdmin\Layouts;

use Lar\Layout\Abstracts\LayoutComponent;
use Lar\LteAdmin\LteAdmin;

/**
 * Landing Class.
 *
 * @package App\Layouts
 */
class LteBase extends LayoutComponent
{
    /**
     * Protected variable Name.
     *
     * @var string
     */
    protected $name = 'lte_layout';

    /**
     * @var string
     */
    protected $default_title = 'LteAdmin';

    /**
     * @var array
     */
    protected $head_styles = [
        'ljs' => [
            'fancy', 'select2',
        ],

        'lte-asset/plugins/fontawesome-free/css/all.min.css',
        'lte-asset/css/adminlte.min.css',

        'lte-asset/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css',
        'lte-asset/plugins/bootstrap-slider/css/bootstrap-slider.min.css',
        'lte-asset/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css',
        'lte-asset/plugins/ekko-lightbox/ekko-lightbox.css',
        'lte-asset/plugins/flag-icon-css/css/flag-icon.min.css',
        'lte-asset/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
        'lte-asset/plugins/ion-rangeslider/css/ion.rangeSlider.min.css',
        'lte-asset/plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
        'lte-asset/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',
        'lte-asset/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css',
        'lte-asset/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
        'lte-asset/plugins/daterangepicker/daterangepicker.css',
        'lte-asset/plugins/chart.js/Chart.min.css',

        'lte-admin/plugins/bootstrap-fileinput/css/fileinput.min.css',
        'lte-admin/plugins/bootstrap-fileinput/themes/explorer-fas/theme.css',
        'lte-admin/plugins/bootstrap-iconpicker-1.10.0/dist/css/bootstrap-iconpicker.min.css',
        'lte-admin/plugins/nestable/jquery.nestable.css',
        'lte-admin/plugins/editor.md-master/css/editormd.min.css',
        'lte-admin/plugins/bootstrap4-editable/css/bootstrap-editable.css',
        'lte-admin/plugins/codemirror/lib/codemirror.css',
        'lte-admin/plugins/star-rating/star-rating.min.css',
        'lte-admin/plugins/star-rating/krajee-fas/theme.min.css',

        'lte-admin/css/app.css',
        'https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700',
    ];

    /**
     * @var array
     */
    protected $body_scripts = [
        'lte-asset/plugins/jquery/jquery.min.js',
        'lte-asset/plugins/bootstrap/js/bootstrap.bundle.min.js',
        'lte-asset/js/adminlte.min.js',

        'lte-asset/plugins/chart.js/Chart.min.js',
        'lte-asset/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js',
        'lte-asset/plugins/bootstrap-slider/bootstrap-slider.min.js',
        'lte-asset/plugins/bootstrap-switch/js/bootstrap-switch.min.js',
        'lte-asset/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js',
        'lte-asset/plugins/bs-custom-file-input/bs-custom-file-input.min.js',
        'lte-asset/plugins/ekko-lightbox/ekko-lightbox.min.js',
        'lte-asset/plugins/fastclick/fastclick.js',
        'lte-asset/plugins/ion-rangeslider/js/ion.rangeSlider.min.js',
        'lte-asset/plugins/jquery-knob/jquery.knob.min.js',
        'lte-asset/plugins/jquery-mousewheel/jquery.mousewheel.js',
        'lte-asset/plugins/moment/moment.min.js',
        'lte-asset/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
        'lte-asset/plugins/popper/umd/popper.min.js',
        'lte-asset/plugins/popper/umd/popper-utils.min.js',
        'lte-asset/plugins/sparklines/sparkline.js',
        'lte-asset/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js',
        'lte-asset/plugins/daterangepicker/daterangepicker.js',
        'lte-asset/plugins/sweetalert2/sweetalert2.min.js',

        'lte-admin/plugins/jquery-validation/jquery.validate.min.js',
        'lte-admin/plugins/jquery-validation/localization/messages_ru.min.js',
        'lte-admin/plugins/bootstrap-fileinput/js/plugins/piexif.min.js',
        'lte-admin/plugins/bootstrap-fileinput/js/plugins/sortable.min.js',
        'lte-admin/plugins/ckeditor5-build-classic/ckeditor.js',
        'lte-admin/plugins/bootstrap-fileinput/js/fileinput.min.js',
        'lte-admin/plugins/bootstrap-fileinput/themes/explorer-fas/theme.js',
        'lte-admin/plugins/bootstrap-fileinput/themes/fas/theme.js',
        'lte-admin/plugins/bootstrap-fileinput/js/locales/ru.js',
        'lte-admin/plugins/bootstrap-iconpicker-1.10.0/dist/js/bootstrap-iconpicker.bundle.min.js',
        'lte-admin/plugins/bootstrap-number-input.js',
        'lte-admin/plugins/nestable/jquery.nestable.js',
        'lte-admin/plugins/editor.md-master/editormd.min.js',
        'lte-admin/plugins/editor.md-master/languages/en.js',

        'lte-admin/plugins/bootstrap4-editable/js/bootstrap-editable.min.js',
        'lte-admin/plugins/codemirror/lib/codemirror.js',
        'lte-admin/plugins/codemirror/addon/selection/selection-pointer.js',
        'lte-admin/plugins/codemirror/addon/edit/matchbrackets.js',
        'lte-admin/plugins/codemirror/addon/comment/continuecomment.js',
        'lte-admin/plugins/codemirror/addon/comment/comment.js',
        'lte-admin/plugins/codemirror/mode/xml/xml.js',
        'lte-admin/plugins/codemirror/mode/javascript/javascript.js',
        'lte-admin/plugins/codemirror/mode/css/css.js',
        'lte-admin/plugins/codemirror/mode/vbscript/vbscript.js',
        'lte-admin/plugins/codemirror/mode/htmlmixed/htmlmixed.js',
        'lte-admin/plugins/codemirror/mode/markdown/markdown.js',
        'lte-admin/plugins/star-rating/star-rating.min.js',
        'lte-admin/plugins/star-rating/locales/ru.js',
        'lte-admin/plugins/star-rating/krajee-fas/theme.min.js',

        'lte-admin/plugins/ptty.jquery.js',

        'ljs' => [
            'jq', 'alert', 'nav', 'mask', 'select2', 'fancy',
        ],

        'lte-admin/js/app.js',
    ];

    /**
     * @var array
     */
    protected $metas = [
        ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1'],
    ];

    /**
     * LteBase constructor.
     */
    public function __construct()
    {
        if (LteAdmin::$echo) {
            $this->body_scripts['ljs'][] = 'echo';
        }

        $this->body_scripts['ljs'][] = 'vue';

        foreach (\LteAdmin::extensions() as $extension) {
            $this->body_scripts = array_merge_recursive($this->body_scripts, $extension->config()->getScripts());

            $this->head_styles = array_merge_recursive($this->head_styles, $extension->config()->getStyles());
        }

        $this->body_scripts = array_merge($this->body_scripts, config('lte.body_scripts', []));

        $this->js()->state('admin', \LteAdmin::user());

        parent::__construct();

        $this->head->link(['rel' => 'icon', 'type' => 'image/png', 'href' => asset('lte-admin/img/favicon.png')]);
        $this->head->link(['rel' => 'apple-touch-icon', 'href' => asset('lte-admin/img/favicon.png')]);
    }
}
