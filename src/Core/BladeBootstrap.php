<?php

namespace Lar\LteAdmin\Core;

use Lar\Developer\Commands\Dump\GenerateBladeHelpers;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\H3;
use Lar\Layout\Tags\INPUT;
use Lar\Layout\Tags\TEXTAREA;
use Lar\LteAdmin\Components\Alert;
use Lar\LteAdmin\Components\ButtonGroup;
use Lar\LteAdmin\Components\Card;
use Lar\LteAdmin\Components\CardHead;
use Lar\LteAdmin\Components\Col;
use Lar\LteAdmin\Components\Form;
use Lar\LteAdmin\Components\FormFooter;
use Lar\LteAdmin\Components\FormGroup;
use Lar\LteAdmin\Components\FormGroupEnd;
use Lar\LteAdmin\Components\InfoBox;
use Lar\LteAdmin\Components\Nestable;
use Lar\LteAdmin\Components\Select2;
use Lar\LteAdmin\Components\SmallBox;
use Lar\LteAdmin\Components\Switcher;use Lar\LteAdmin\Components\Table;
use Lar\LteAdmin\Components\Tabs;

/**
 * Class BladeBootstrap
 * @package Lar\LteAdmin\Core
 */
class BladeBootstrap {

    /**
     * @var array
     */
    protected $func = [
        'container', 'row', 'col', 'formgroup', 'card', 'table', 'form', 'buttongroup', 'inputs',
        'tabs', 'alerts', 'nestable', 'smalbox'
    ];

    /**
     * @var string
     */
    protected $input_link;

    /**
     * @var string
     */
    protected $div_link;

    /**
     * @var string
     */
    protected $col_link;

    public function smalbox()
    {
        \Blade::directive('infoboxprimary', function ($attr = '') {
            $class = InfoBox::class;
            return "<?php echo \\{$class}::create({$attr})->icon_bg('bg-primary'); ?>";
        });
        \Blade::directive('infoboxsecondary', function ($attr = '') {
            $class = InfoBox::class;
            return "<?php echo \\{$class}::create({$attr})->icon_bg('bg-secondary'); ?>";
        });
        \Blade::directive('infoboxsuccess', function ($attr = '') {
            $class = InfoBox::class;
            return "<?php echo \\{$class}::create({$attr})->icon_bg('bg-success'); ?>";
        });
        \Blade::directive('infoboxdanger', function ($attr = '') {
            $class = InfoBox::class;
            return "<?php echo \\{$class}::create({$attr})->icon_bg('bg-danger'); ?>";
        });
        \Blade::directive('infoboxwarning', function ($attr = '') {
            $class = InfoBox::class;
            return "<?php echo \\{$class}::create({$attr})->icon_bg('bg-warning'); ?>";
        });
        \Blade::directive('infoboxinfo', function ($attr = '') {
            $class = InfoBox::class;
            return "<?php echo \\{$class}::create({$attr})->icon_bg('bg-info'); ?>";
        });
        \Blade::directive('infoboxlight', function ($attr = '') {
            $class = InfoBox::class;
            return "<?php echo \\{$class}::create({$attr})->icon_bg('bg-light'); ?>";
        });
        \Blade::directive('infoboxdark', function ($attr = '') {
            $class = InfoBox::class;
            return "<?php echo \\{$class}::create({$attr})->icon_bg('bg-dark'); ?>";
        });
        \Blade::directive('infoboxwhite', function ($attr = '') {
            $class = InfoBox::class;
            return "<?php echo \\{$class}::create({$attr})->icon_bg('bg-white'); ?>";
        });
        \Blade::directive('infoboxtransparent', function ($attr = '') {
            $class = InfoBox::class;
            return "<?php echo \\{$class}::create({$attr})->icon_bg('bg-transparent'); ?>";
        });



        \Blade::directive('smboxprimary', function ($attr = '') {
            $class = SmallBox::class;
            return "<?php echo \\{$class}::create({$attr})->addClass('bg-primary'); ?>";
        });
        \Blade::directive('smboxsecondary', function ($attr = '') {
            $class = SmallBox::class;
            return "<?php echo \\{$class}::create({$attr})->addClass('bg-secondary'); ?>";
        });
        \Blade::directive('smboxsuccess', function ($attr = '') {
            $class = SmallBox::class;
            return "<?php echo \\{$class}::create({$attr})->addClass('bg-success'); ?>";
        });
        \Blade::directive('smboxdanger', function ($attr = '') {
            $class = SmallBox::class;
            return "<?php echo \\{$class}::create({$attr})->addClass('bg-danger'); ?>";
        });
        \Blade::directive('smboxwarning', function ($attr = '') {
            $class = SmallBox::class;
            return "<?php echo \\{$class}::create({$attr})->addClass('bg-warning'); ?>";
        });
        \Blade::directive('smboxinfo', function ($attr = '') {
            $class = SmallBox::class;
            return "<?php echo \\{$class}::create({$attr})->addClass('bg-info'); ?>";
        });
        \Blade::directive('smboxlight', function ($attr = '') {
            $class = SmallBox::class;
            return "<?php echo \\{$class}::create({$attr})->addClass('bg-light'); ?>";
        });
        \Blade::directive('smboxdark', function ($attr = '') {
            $class = SmallBox::class;
            return "<?php echo \\{$class}::create({$attr})->addClass('bg-dark'); ?>";
        });
        \Blade::directive('smboxwhite', function ($attr = '') {
            $class = SmallBox::class;
            return "<?php echo \\{$class}::create({$attr})->addClass('bg-white'); ?>";
        });
        \Blade::directive('smboxtransparent', function ($attr = '') {
            $class = SmallBox::class;
            return "<?php echo \\{$class}::create({$attr})->addClass('bg-transparent'); ?>";
        });
    }

    /**
     * Nestable
     */
    protected function nestable()
    {
        \Blade::directive('nestable', function ($attrs = '') {

            $class = Nestable::class;

            return "<?php \$__nestable = \\{$class}::create({$attrs}); ?>";
        });

        \Blade::directive('endnestable', function () {
            return '<?php echo $__nestable; ?>';
        });

        \Blade::directive('cardbodynestable', function ($attrs = '') {

            $class = Nestable::class;

            return "<?php \$__nestable = \\{$class}::create({$attrs}); echo \\{$this->div_link}::create()->addClass('card-body')->openMode(); ?>";
        });

        \Blade::directive('endcardbodynestable', function () {
            return '<?php echo $__nestable->build(); ?> </div>';
        });

        \Blade::directive('nestabledesc', function ($attrs = '') {

            return "<?php \$__nestable->orderDesc({$attrs}); ?>";
        });

        \Blade::directive('nestableorderby', function ($attrs = '') {

            return "<?php \$__nestable->orderBy({$attrs}); ?>";
        });

        \Blade::directive('nestabletitle', function ($attrs = '') {

            return "<?php \$__nestable->title_field({$attrs}); ?>";
        });

        \Blade::directive('nestabledepth', function ($attrs = '') {

            return "<?php \$__nestable->maxDepth({$attrs}); ?>";
        });

        \Blade::directive('nestabledisablecontrols', function () {

            return "<?php \$__nestable->disableControls(); ?>";
        });

        \Blade::directive('nestabledisableinfo', function () {

            return "<?php \$__nestable->disableInfo(); ?>";
        });

        \Blade::directive('nestabledisableedit', function () {

            return "<?php \$__nestable->disableEdit(); ?>";
        });

        \Blade::directive('nestabledisabledelete', function () {

            return "<?php \$__nestable->disableDelete(); ?>";
        });

        GenerateBladeHelpers::$just[] = 'endnestable';
        GenerateBladeHelpers::$just[] = 'endcardbodynestable';
        GenerateBladeHelpers::$just[] = 'nestabledisablecontrols';
        GenerateBladeHelpers::$just[] = 'nestabledisableinfo';
        GenerateBladeHelpers::$just[] = 'nestabledisableedit';
        GenerateBladeHelpers::$just[] = 'nestabledisabledelete';
    }

    /**
     * Alerts
     */
    protected function alerts()
    {
        \Blade::directive('alertprimary', function ($data = '') {

            $class = Alert::class;

            return "<?php echo \\{$class}::create({$data})->addClass('alert-primary'); ?>";
        });

        \Blade::directive('alertsecondary', function ($data = '') {

            $class = Alert::class;

            return "<?php echo \\{$class}::create({$data})->addClass('alert-secondary'); ?>";
        });

        \Blade::directive('alertsuccess', function ($data = '') {

            $class = Alert::class;

            return "<?php echo \\{$class}::create({$data})->addClass('alert-success'); ?>";
        });

        \Blade::directive('alertdanger', function ($data = '') {

            $class = Alert::class;

            return "<?php echo \\{$class}::create({$data})->addClass('alert-danger'); ?>";
        });

        \Blade::directive('alertwarning', function ($data = '') {

            $class = Alert::class;

            return "<?php echo \\{$class}::create({$data})->addClass('alert-warning'); ?>";
        });

        \Blade::directive('alertinfo', function ($data = '') {

            $class = Alert::class;

            return "<?php echo \\{$class}::create({$data})->addClass('alert-info'); ?>";
        });

        \Blade::directive('alertlight', function ($data = '') {

            $class = Alert::class;

            return "<?php echo \\{$class}::create({$data})->addClass('alert-light'); ?>";
        });

        \Blade::directive('alertdark', function ($data = '') {

            $class = Alert::class;

            return "<?php echo \\{$class}::create({$data})->addClass('alert-dark'); ?>";
        });

        \Blade::directive('endalert', function ($attrs = '') {

            return "</div>";
        });

        GenerateBladeHelpers::$just[] = 'endalert';

    }

    /**
     * Tabs
     */
    protected function tabs()
    {
        \Blade::directive('tabs', function ($attrs = '') {

            $class = Tabs::class;

            return "<?php \$__tab = new \\{$class}({$attrs}); ?>";
        });

        \Blade::directive('tab', function ($attrs = '') {

            return "<?php \$__tab->addTab({$attrs}); ob_start(); ?>";
        });

        \Blade::directive('endtab', function () {

            return "<?php \$__contents_ = ob_get_contents(); \$__tab->addData(\$__contents_); unset(\$__contents_ ); ob_end_clean(); ?>";
        });

        \Blade::directive('endtabs', function () {

            return "<?php echo \$__tab; unset(\$__tab); ?>";
        });

        \Blade::directive('tabsbottom', function ($attrs = '') {

            return "<?php echo \\{$this->div_link}::create({$attrs})->addClass('tab-custom-content')->openMode(); ?>";
        });

        \Blade::directive('endtabsbottom', function () {

            return "</div>";
        });

        GenerateBladeHelpers::$just[] = 'endtab';
        GenerateBladeHelpers::$just[] = 'endtabs';
        GenerateBladeHelpers::$just[] = 'endtabsbottom';
    }

    /**
     * Inputs
     */
    protected function inputs()
    {
        \Blade::directive('forminput', function ($attrs = '') {

            return "<?php echo \$__inp = \\{$this->input_link}::create({$attrs})->attr(['type' => 'text', 'id' => \$id ?? '', 'name' => \$name ?? '', 'placeholder' => \$title ?? ''])->addClass('form-control'); if (isset(\$value)) {\$__inp->setValueIf(\$value !== null, \$value);} ?>";
        });

        \Blade::directive('formpassword', function ($attrs = '') {

            return "<?php echo \$__inp = \\{$this->input_link}::create({$attrs})->attr(['type' => 'password', 'id' => \$id ?? '', 'name' => \$name ?? '', 'placeholder' => \$title ?? ''])->addClass('form-control'); if (isset(\$value)) {\$__inp->setValueIf(\$value !== null, \$value);} ?>";
        });

        \Blade::directive('formemail', function ($attrs = '') {

            return "<?php echo \\{$this->input_link}::create({$attrs})->attr(['type' => 'email', 'id' => \$id, 'name' => \$name, 'placeholder' => \$title])->addClass('form-control')->setValueIf(\$value !== null, \$value); ?>";
        });

        \Blade::directive('formnumber', function ($attrs = '') {

            return "<?php echo \\{$this->input_link}::create({$attrs})->attr(['type' => 'number', 'id' => \$id, 'name' => \$name, 'value' => \$value ?? 0, 'placeholder' => \$title, 'data-load' => 'number'])->addClass('form-control'); ?>";
        });

        \Blade::directive('formfile', function ($attrs = '') {

            return "<?php echo \\{$this->input_link}::create($attrs)->attr(['type' => 'file', 'name' => \$name, 'placeholder' => \$title, 'data-load' => 'file'])->setValueIf(\$value !== null, \$value); ?>";
        });

        \Blade::directive('formswitcher', function ($attrs = '') {

            $class = Switcher::class;

            return "<?php echo \\{$class}::create({$attrs})->attr(['id' => \$id, 'name' => \$name])->setCheckedIf(\$value, 'checked'); ?>";
        });

        \Blade::directive('formdaterange', function ($attrs = '') {

            return "<?php echo \\{$this->input_link}::create({$attrs})->attr(['type' => 'text', 'id' => \$id, 'name' => \$name, 'placeholder' => \$title, 'data-load' => 'picker::daterange'])->addClass('form-control')->setValueIf(\$value !== null, \$value); ?>";
        });

        \Blade::directive('formdatetimerange', function ($attrs = '') {

            return "<?php echo \\{$this->input_link}::create({$attrs})->attr(['type' => 'text', 'id' => \$id, 'name' => \$name, 'placeholder' => \$title, 'data-load' => 'picker::datetimerange'])->addClass('form-control')->setValueIf(\$value !== null, \$value); ?>";
        });

        \Blade::directive('formdatetime', function ($attrs = '') {

            return "<?php echo \\{$this->input_link}::create({$attrs})->attr(['type' => 'text', 'id' => \$id, 'name' => \$name, 'placeholder' => \$title, 'data-load' => 'picker::datetime', 'data-toggle' => 'datetimepicker', 'data-target' => '#' . \$id])->addClass('form-control')->setValueIf(\$value !== null, \$value); ?>";
        });

        \Blade::directive('formdate', function ($attrs = '') {

            return "<?php echo \\{$this->input_link}::create({$attrs})->attr(['type' => 'text', 'id' => \$id, 'name' => \$name, 'placeholder' => \$title, 'data-load' => 'picker::date', 'data-toggle' => 'datetimepicker', 'data-target' => '#' . \$id])->addClass('form-control')->setValueIf(\$value !== null, \$value); ?>";
        });

        \Blade::directive('formtime', function ($attrs = '') {

            return "<?php echo \\{$this->input_link}::create({$attrs})->attr(['type' => 'text', 'id' => \$id, 'name' => \$name, 'placeholder' => \$title, 'data-load' => 'picker::time', 'data-toggle' => 'datetimepicker', 'data-target' => '#' . \$id])->addClass('form-control')->setValueIf(\$value !== null, \$value); ?>";
        });

        \Blade::directive('formicon', function ($attrs = '') {

            return "<?php echo \\{$this->input_link}::create({$attrs})->attr(['type' => 'text', 'id' => \$id, 'name' => \$name, 'placeholder' => \$title])->addClass('form-control')->setValueIf(\$value !== null, \$value); ?><span class='input-group-append'><button class='btn btn-primary' data-icon='<?php echo \$value ?>' data-load='picker::icon'></button></span>";
        });

        \Blade::directive('formcolor', function ($attrs = '') {

            return "<?php echo \\{$this->input_link}::create({$attrs})->attr(['type' => 'text', 'id' => \$id, 'name' => \$name, 'placeholder' => \$title, 'data-load' => 'picker::color'])->addClass('form-control')->setValueIf(\$value !== null, \$value); ?><span class='input-group-append'><span class='input-group-text'><i class='fas fa-square' style='color: <?php echo \$value ?>;'></i></span></span>";
        });

        \Blade::directive('formdualselect', function ($attrs = '') {

            $class = Select2::class;

            return "<?php echo \\{$class}::create({$attrs})->attr(['name' => \$name, 'data-placeholder' => \$title, 'data-load' => 'duallist', 'multiple' => 'multiple'])->setValues(\$value)->makeOptions()->addClass('form-control duallistbox'); ?>";
        });

        \Blade::directive('formselect', function ($attrs = '') {

            $class = Select2::class;

            return "<?php echo \\{$class}::create({$attrs})->attr(['name' => \$name, 'data-placeholder' => \$title, 'data-load' => 'select2'])->setValues(\$value)->makeOptions()->addClass('form-control'); ?>";
        });

        \Blade::directive('formmiltiselect', function ($attrs = '') {

            $class = Select2::class;

            return "<?php echo \\{$class}::create({$attrs})->attr(['name' => \$name, 'data-placeholder' => \$title, 'data-load' => 'select2', 'multiple' => 'multiple'])->setValues(\$value)->makeOptions()->addClass('form-control'); ?>";
        });

        \Blade::directive('formckeditor', function ($attrs = '') {

            $class = TEXTAREA::class;

            return "<?php echo \\{$class}::create({$attrs})->attr(['name' => \$name, 'placeholder' => \$title, 'id' => \$id, 'data-load' => 'ckeditor'])->text(\$value); ?>";
        });

        \Blade::directive('formtextarea', function ($attrs = '') {

            $class = TEXTAREA::class;

            return "<?php echo \\{$class}::create({$attrs})->attr(['name' => \$name, 'placeholder' => \$title, 'id' => \$id])->addClass('form-control')->text(\$value); ?>";
        });

        \Blade::directive('formmdeditor', function ($attrs = '') {

            return "<?php echo \\{$this->div_link}::create({$attrs})->attr(['data-name' => \$name, 'data-placeholder' => \$title, 'id' => \$id, 'data-load' => 'md::simple'])->addClass('form-control')->text(\$value); ?>";
        });
    }

    /**
     * Button group
     */
    protected function buttongroup()
    {
        \Blade::directive('cardheadtoolsbg', function ($attrs = '') {

            $class = ButtonGroup::class;
            
            return "<?php echo \\{$this->div_link}::create()->addClass('card-tools')->openMode(); \$__button_group = \\{$class}::create({$attrs}); ?>";
        });

        \Blade::directive('endcardheadtoolsbg', function () {
            return '<?php echo $__button_group; ?></div>';
        });
        
        \Blade::directive('buttongroup', function ($attrs = '') {

            $class = ButtonGroup::class;

            return "<?php \$__button_group = \\{$class}::create({$attrs}); ?>";
        });

        \Blade::directive('bgroupsubmit', function ($attrs = '') {

            return "<?php \$__button_group->submit({$attrs}); ?>";
        });

        \Blade::directive('bgroupnestable', function () {

            return "<?php \$__button_group->nestable(); ?>";
        });

        \Blade::directive('bgroupinfo', function ($attrs = '') {

            return "<?php \$__button_group->info({$attrs}); ?>";
        });

        \Blade::directive('bgroupwarning', function ($attrs = '') {

            return "<?php \$__button_group->warning({$attrs}); ?>";
        });

        \Blade::directive('bgroupdanger', function ($attrs = '') {

            return "<?php \$__button_group->danger({$attrs}); ?>";
        });

        \Blade::directive('bgroupsuccess', function ($attrs = '') {

            return "<?php \$__button_group->success({$attrs}); ?>";
        });

        \Blade::directive('bgroupsecondary', function ($attrs = '') {

            return "<?php \$__button_group->secondary({$attrs}); ?>";
        });

        \Blade::directive('bgroupdefault', function ($attrs = '') {

            return "<?php \$__button_group->default({$attrs}); ?>";
        });

        \Blade::directive('bgroupprimary', function ($attrs = '') {

            return "<?php \$__button_group->primary({$attrs}); ?>";
        });

        \Blade::directive('bgroup', function ($attrs = '') {

            return "<?php \$__button_group->btn({$attrs}); ?>";
        });

        \Blade::directive('bgroupreload', function ($props = '') {

            return "<?php \$__button_group->reload($props); ?>";
        });

        \Blade::directive('bgrouprlist', function ($props = '') {

            return "<?php \$__button_group->resourceList($props); ?>";
        });

        \Blade::directive('bgroupredit', function ($props = '') {

            return "<?php \$__button_group->resourceEdit($props); ?>";
        });

        \Blade::directive('bgrouprinfo', function ($props = '') {

            return "<?php \$__button_group->resourceInfo($props); ?>";
        });

        \Blade::directive('bgrouprdestroy', function ($props = '') {

            return "<?php \$__button_group->resourceDestroy($props); ?>";
        });

        \Blade::directive('bgroupradd', function ($props = '') {

            return "<?php \$__button_group->resourceAdd($props); ?>";
        });

        \Blade::directive('endbuttongroup', function () {

            return "<?php echo \$__button_group; ?>";
        });

        GenerateBladeHelpers::$just[] = 'endbuttongroup';
        GenerateBladeHelpers::$just[] = 'endcardheadtoolsbg';
        GenerateBladeHelpers::$just[] = 'bgroupnestable';
    }

    /**
     * Make form
     */
    protected function form()
    {
        \Blade::directive('hiddens', function ($attrs = '') {

            $class = INPUT::class;
            return "<?php 
            \$model = gets()->lte->menu->model;
            foreach ({$attrs} as \$name => \$value) { 
                echo \\{$class}::create(['type' => 'hidden', 'name' => \$name, 'value' => (\$model && \$model->exists ? (multi_dot_call(\$model, \$name) ?? \$value) : \$value)]); 
            } ?>";
        });

        \Blade::directive('form', function ($attrs = '') {

            $class = Form::class;

            return "<?php \$__form = \\{$class}::create({$attrs})->openMode(); echo \$__form; \$__form_id = \$__form->getId(); \$__form_model = \$__form->model; ?>";
        });

        \Blade::directive('endform', function ($attrs = '') {

            return "</form>";
        });

        \Blade::directive('cardbodyform', function ($attrs = '') {

            $class = Form::class;

            return "<?php echo \\{$this->div_link}::create()->addClass('card-body')->openMode(); \$__form = \\{$class}::create({$attrs})->openMode(); echo \$__form; \$__form_id = \$__form->getId(); \$__form_model = \$__form->model; ?>";
        });

        \Blade::directive('endcardbodyform', function ($attrs = '') {
            return '</form></div>';
        });


        \Blade::directive('formfooter', function ($attrs = '') {

            $class = FormFooter::class;

            return "<?php echo \\{$class}::create({$attrs})->setFormId(\$__form_id ?? null)->createFooter(); ?>";
        });

        GenerateBladeHelpers::$just[] = 'endform';
        GenerateBladeHelpers::$just[] = 'endcardbodyform';
    }

    /**
     * Table components
     */
    protected function table () {

        \Blade::directive('cardbodytable', function ($attrs = '') {

            $class = Table::class;

            return "<?php \$__table = \\{$class}::create({$attrs}); echo \\{$this->div_link}::create()->addClass('card-body p-0')->openMode(); ?>";
        });

        \Blade::directive('endcardbodytable', function ($attrs = '') {
            return '<?php echo $__table; ?> </div>';
        });


        \Blade::directive('table', function ($attrs = '') {

            $class = Table::class;

            if (empty($attrs)) { $attrs = "gets()->lte->menu->model"; }

            return "<?php \$__table = \\{$class}::create({$attrs}); ?>";
        });

        \Blade::directive('tableperpage', function ($attrs = '') {

            return "<?php \$__table->perPage({$attrs}); ?>";
        });

        \Blade::directive('tableperpages', function ($attrs = '') {

            return "<?php \$__table->perPages({$attrs}); ?>";
        });

        \Blade::directive('tablemodel', function ($attrs = '') {

            return "<?php \$__table->model({$attrs}); ?>";
        });

        \Blade::directive('tableinstruction', function ($attrs = '') {

            return "<?php \$__table->instruction({$attrs}); ?>";
        });

        \Blade::directive('tabledesc', function ($attrs = '') {

            return "<?php \$__table->orderDesc({$attrs}); ?>";
        });

        \Blade::directive('tableorderby', function ($attrs = '') {

            return "<?php \$__table->orderBy({$attrs}); ?>";
        });

        \Blade::directive('column', function ($attrs = '') {

            return "<?php \$__table->column({$attrs}); ?>";
        });

        \Blade::directive('columncaretedat', function () {

            return "<?php \$__table->created_at(); ?>";
        });

        \Blade::directive('columnupdatedat', function () {

            return "<?php \$__table->updated_at(); ?>";
        });

        \Blade::directive('columndeletedat', function () {

            return "<?php \$__table->deleted_at(); ?>";
        });

        \Blade::directive('disablecontrols', function () {

            return "<?php \$__table->disableControls(); ?>";
        });

        \Blade::directive('disableinfo', function () {

            return "<?php \$__table->disableInfo(); ?>";
        });

        \Blade::directive('disableedit', function () {

            return "<?php \$__table->disableEdit(); ?>";
        });

        \Blade::directive('disabledelete', function () {

            return "<?php \$__table->disableDelete(); ?>";
        });

        \Blade::directive('endtable', function () {

            return "<?php echo \$__table; ?>";
        });

        \Blade::directive('tablefooter', function () {

            return "<?php echo \$__table->footer(); ?>";
        });

        GenerateBladeHelpers::$just[] = 'disabledelete';
        GenerateBladeHelpers::$just[] = 'disableedit';
        GenerateBladeHelpers::$just[] = 'disableinfo';
        GenerateBladeHelpers::$just[] = 'disablecontrols';
        GenerateBladeHelpers::$just[] = 'endtable';
        GenerateBladeHelpers::$just[] = 'tablefooter';
        GenerateBladeHelpers::$just[] = 'endcardbodytable';
        GenerateBladeHelpers::$just[] = 'columncaretedat';
        GenerateBladeHelpers::$just[] = 'columnupdatedat';
        GenerateBladeHelpers::$just[] = 'columndeletedat';
    }

    /**
     * Card components
     */
    protected function card () {

        \Blade::directive('card', function ($attrs = '') {

            $class = Card::class;

            return "<?php echo \\{$class}::create({$attrs}); ?>";
        });

        \Blade::directive('endcard', function ($attrs = '') {
            return '</div>';
        });



        \Blade::directive('cardheader', function ($attrs = '') {

            $class = CardHead::class;

            return "<?php echo \\{$class}::create({$attrs}); ?>";
        });



        \Blade::directive('cardhead', function ($attrs = '') {

            $class = CardHead::class;

            return "<?php echo \\{$class}::create({$attrs})->openMode(); ?>";
        });

        \Blade::directive('endcardhead', function ($attrs = '') {
            return '</div>';
        });



        \Blade::directive('cardheadtitle', function ($attrs = '') {
            $h3 = H3::class;
            return "<?php echo \\{$h3}::create({$attrs})->addClass('card-title')->openMode(); ?>";
        });

        \Blade::directive('endcardheadtitle', function ($attrs = '') {
            return '</h3>';
        });



        \Blade::directive('cardheadtools', function ($attrs = '') {
            return "<?php echo \\{$this->div_link}::create({$attrs})->addClass('card-tools')->openMode(); ?>";
        });

        \Blade::directive('endcardheadtools', function () {
            return '</div>';
        });



        \Blade::directive('cardbody', function ($attrs = '') {
            return "<?php echo \\{$this->div_link}::create({$attrs})->addClass('card-body')->openMode(); ?>";
        });

        \Blade::directive('endcardbody', function ($attrs = '') {
            return '</div>';
        });



        \Blade::directive('cardfooter', function ($attrs = '') {
            return "<?php echo \\{$this->div_link}::create({$attrs})->addClass('card-footer')->openMode(); ?>";
        });

        \Blade::directive('endcardfooter', function ($attrs = '') {
            return '</div>';
        });

        GenerateBladeHelpers::$just[] = 'endcard';
        GenerateBladeHelpers::$just[] = 'endcardbody';
        GenerateBladeHelpers::$just[] = 'endcardfooter';
        GenerateBladeHelpers::$just[] = 'endcardhead';
        GenerateBladeHelpers::$just[] = 'endcardheadtools';
    }

    /**
     * Form group
     */
    protected function formgroup()
    {
        $group = function ($attrs = '', $vertical = 'false') {

            $class = FormGroup::class;

            $data = <<<PHP
\\{$class}::\$model = \$__form_model ?? gets()->lte->menu->model; \\{$class}::\$vertical={$vertical}; \$__form_group = \\{$class}::create($attrs);
if(isset(\$name)) {\$__old_name = \$name;} if(isset(\$title)) {\$__old_title = \$title;} if(isset(\$id)) {\$__old_id = \$id;}
if(isset(\$value)) {\$__old_value = \$value;} if(isset(\$path)) {\$__old_path = \$path;}
\$name = \$__form_group->__getName(); \$title = \$__form_group->__getTitle(); \$id = \$__form_group->__getId(); \$path = \$__form_group->__getPath(); \$value = old(\$path, \$__form_group->__getValue());
echo \$__form_group;
PHP;
            return "<?php {$data}  ?>";
        };


        \Blade::directive('vformgroup', function ($data = '') use ($group) {

            return $group($data, 'true');
        });


        \Blade::directive('formgroup', function ($data = '') use ($group) {

            return $group($data);
        });

        GenerateBladeHelpers::$just[] = 'endformgroup';

        \Blade::directive('endformgroup', function () {

            $class = FormGroupEnd::class;

            $data = <<<PHP
if(isset(\$__old_name)) {\$name = \$__old_name;} if(isset(\$__old_title)) {\$title = \$__old_title;} if(isset(\$__old_id)) {\$id = \$__old_id;}
PHP;

            return "</div><?php echo \\{$class}::create(\$name, get_defined_vars()['errors'] ?? [], \$__form_group->__v); {$data} ?></div>";
        });
    }

    /**
     * Start row
     */
    protected function row()
    {
        \Blade::directive('row', function ($attrs = '') {

            return "<?php echo \$__row = \\{$this->div_link}::create({$attrs})->addClass('row')->openMode(); ?>";
        });

        \Blade::directive('endrow', function ($attrs = '') {

            return "</div>";
        });
    }

    /**
     * Make Columns
     */
    protected function col()
    {
        \Blade::directive('col', function ($attrs = '') {

            return "<?php echo \$__col = \\{$this->col_link}::create({$attrs})->colType()->openMode(); ?>";
        });

        \Blade::directive("colsm", function ($attrs = '') {

            return "<?php echo \$__col = \\{$this->col_link}::create({$attrs})->colType('sm')->openMode(); ?>";
        });

        \Blade::directive("colmd", function ($attrs = '') {

            return "<?php echo \$__col = \\{$this->col_link}::create({$attrs})->colType('md')->openMode(); ?>";
        });

        \Blade::directive("collg", function ($attrs = '') {

            return "<?php echo \$__col = \\{$this->col_link}::create({$attrs})->colType('lg')->openMode(); ?>";
        });

        \Blade::directive("colxl", function ($attrs = '') {

            return "<?php echo \$__col = \\{$this->col_link}::create({$attrs})->colType('xl')->openMode(); ?>";
        });

        \Blade::directive('endcol', function () {

            return "</div>";
        });
    }

    /**
     * Make container
     */
    protected function container() {

        \Blade::directive('container', function ($attrs = '') {

            return "<?php echo \$__container = \\{$this->div_link}::create({$attrs})->addClass('container-fluid')->openMode(); ?>";
        });

        \Blade::directive('endcontainer', function () {

            return "</div>";
        });
    }

    /**
     * Run build
     */
    public static function run()
    {
        (new static())->cycle();
    }

    /**
     * Cycle
     */
    public function cycle()
    {
        $this->div_link = DIV::class;
        $this->col_link = Col::class;
        $this->input_link = INPUT::class;

        GenerateBladeHelpers::$just[] = 'endcontainer';
        GenerateBladeHelpers::$just[] = 'endcol';
        GenerateBladeHelpers::$just[] = 'endrow';

        foreach ($this->func as $item) {

            $this->{$item}();
        }
    }
}