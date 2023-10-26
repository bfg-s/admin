<?php

namespace Admin;

use Exception;

use function config;

class ApplicationServiceProvider extends ExtendProvider
{
    /**
     * Extension call slug.
     * @var string
     */
    public static $slug = 'application';

    /**
     * @var array
     */
    protected array $defaultScripts = [
        'admin-asset/plugins/jquery/jquery.min.js',
        'admin-asset/plugins/bootstrap/js/bootstrap.bundle.min.js',

        'admin-asset/plugins/chart.js/Chart.min.js',
        'admin-asset/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js',
        'admin-asset/plugins/bootstrap-slider/bootstrap-slider.min.js',
        'admin-asset/plugins/bootstrap-switch/js/bootstrap-switch.min.js',
        'admin-asset/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js',
        'admin-asset/plugins/bs-custom-file-input/bs-custom-file-input.min.js',
        'admin-asset/plugins/ekko-lightbox/ekko-lightbox.min.js',
        'admin-asset/plugins/fastclick/fastclick.js',
        'admin-asset/plugins/ion-rangeslider/js/ion.rangeSlider.min.js',
        'admin-asset/plugins/jquery-knob/jquery.knob.min.js',
        'admin-asset/plugins/jquery-mousewheel/jquery.mousewheel.js',
        'admin-asset/plugins/moment/moment.min.js',
        'admin-asset/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
        'admin-asset/plugins/popper/umd/popper.min.js',
        'admin-asset/plugins/popper/umd/popper-utils.min.js',
        'admin-asset/plugins/sparklines/sparkline.js',
        'admin-asset/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js',
        'admin-asset/plugins/daterangepicker/daterangepicker.js',
        'admin-asset/plugins/sweetalert2/sweetalert2.min.js',
        'admin-asset/plugins/select2/js/select2.full.min.js',

        'admin/plugins/jquery-validation/jquery.validate.min.js',

        'admin/plugins/bootstrap-fileinput/js/plugins/piexif.min.js',
        'admin/plugins/bootstrap-fileinput/js/plugins/sortable.min.js',
        'admin/plugins/ckeditor5-build-classic/ckeditor.js',
        'admin/plugins/bootstrap-fileinput/js/fileinput.min.js',
        'admin/plugins/bootstrap-fileinput/themes/explorer-fas/theme.js',
        'admin/plugins/bootstrap-fileinput/themes/fas/theme.js',
        'admin/plugins/bootstrap-fileinput/js/locales/ru.js',
        'admin/plugins/bootstrap-iconpicker-1.10.0/dist/js/bootstrap-iconpicker.bundle.min.js',
        'admin/plugins/bootstrap-number-input.js',
        'admin/plugins/nestable/jquery.nestable.js',
        'admin/plugins/editor.md-master/editormd.min.js',
        'admin/plugins/editor.md-master/languages/en.js',

        'admin/plugins/bootstrap4-editable/js/bootstrap-editable.min.js',
        'admin/plugins/codemirror/lib/codemirror.js',
        'admin/plugins/codemirror/addon/selection/selection-pointer.js',
        'admin/plugins/codemirror/addon/edit/matchbrackets.js',
        'admin/plugins/codemirror/addon/comment/continuecomment.js',
        'admin/plugins/codemirror/addon/comment/comment.js',
        'admin/plugins/codemirror/mode/xml/xml.js',
        'admin/plugins/codemirror/mode/javascript/javascript.js',
        'admin/plugins/codemirror/mode/css/css.js',
        'admin/plugins/codemirror/mode/vbscript/vbscript.js',
        'admin/plugins/codemirror/mode/htmlmixed/htmlmixed.js',
        'admin/plugins/codemirror/mode/markdown/markdown.js',
        'admin/plugins/star-rating/star-rating.min.js',
        'admin/plugins/star-rating/locales/ru.js',
        'admin/plugins/star-rating/krajee-fas/theme.min.js',

        'admin/plugins/ptty.jquery.js',
        'admin/plugins/jquery.lar.pjax.js',

        'admin/js/app.js',
    ];

    /**
     * @var array
     */
    protected array $defaultStyles = [
        'admin-asset/plugins/fontawesome-free/css/all.min.css',

        'admin-asset/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css',
        'admin-asset/plugins/bootstrap-slider/css/bootstrap-slider.min.css',
        'admin-asset/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css',
        'admin-asset/plugins/ekko-lightbox/ekko-lightbox.css',
        'admin-asset/plugins/flag-icon-css/css/flag-icon.min.css',
        'admin-asset/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
        'admin-asset/plugins/ion-rangeslider/css/ion.rangeSlider.min.css',
        'admin-asset/plugins/overlayScrollbars/css/OverlayScrollbars.min.css',

        'admin-asset/plugins/select2/css/select2.min.css',
        'admin-asset/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css',

        'admin-asset/plugins/sweetalert2/sweetalert2.css',
        'admin-asset/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css',
        'admin-asset/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
        'admin-asset/plugins/daterangepicker/daterangepicker.css',
        'admin-asset/plugins/chart.js/Chart.min.css',

        'admin/plugins/bootstrap-fileinput/css/fileinput.min.css',
        'admin/plugins/bootstrap-fileinput/themes/explorer-fas/theme.css',
        'admin/plugins/bootstrap-iconpicker-1.10.0/dist/css/bootstrap-iconpicker.min.css',
        'admin/plugins/nestable/jquery.nestable.css',
        'admin/plugins/editor.md-master/css/editormd.min.css',
        'admin/plugins/bootstrap4-editable/css/bootstrap-editable.css',
        'admin/plugins/codemirror/lib/codemirror.css',
        'admin/plugins/star-rating/star-rating.min.css',
        'admin/plugins/star-rating/krajee-fas/theme.min.css',

        'admin/css/app.css',
    ];

    /**
     * @throws Exception
     */
    public function register()
    {
        static::$name = config('app.name');

        parent::register();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function boot()
    {
        parent::boot();

        $this->config()
            ->mergeScripts($this->defaultScripts);

        $this->config()
            ->mergeStyles($this->defaultStyles);
    }

    /**
     * @return bool
     */
    public function included()
    {
        return true;
    }
}
