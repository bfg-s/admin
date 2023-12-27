<?php

/**
 * @var Builder|Model|Relation|null
 */

namespace Admin\Components;

use Admin\BladeDirectives\SystemCssBladeDirective;
use Admin\BladeDirectives\SystemJsBladeDirective;
use Admin\Components\Inputs\AmountInput;
use Admin\Components\Inputs\AutocompleteInput;
use Admin\Components\Inputs\ChecksInput;
use Admin\Components\Inputs\CKEditorInput;
use Admin\Components\Inputs\CodeMirrorInput;
use Admin\Components\Inputs\ColorInput;
use Admin\Components\Inputs\DateInput;
use Admin\Components\Inputs\DateRangeInput;
use Admin\Components\Inputs\DateTimeInput;
use Admin\Components\Inputs\DateTimeRangeInput;
use Admin\Components\Inputs\DualSelectInput;
use Admin\Components\Inputs\EmailInput;
use Admin\Components\Inputs\FileInput;
use Admin\Components\Inputs\HiddenInput;
use Admin\Components\Inputs\IconInput;
use Admin\Components\Inputs\ImageInput;
use Admin\Components\Inputs\InfoCreatedAtInput;
use Admin\Components\Inputs\InfoIdInput;
use Admin\Components\Inputs\InfoInput;
use Admin\Components\Inputs\InfoUpdatedAtInput;
use Admin\Components\Inputs\Input;
use Admin\Components\Inputs\MDEditorInput;
use Admin\Components\Inputs\MultiSelectInput;
use Admin\Components\Inputs\NumberInput;
use Admin\Components\Inputs\NumericInput;
use Admin\Components\Inputs\PasswordInput;
use Admin\Components\Inputs\RadiosInput;
use Admin\Components\Inputs\RatingInput;
use Admin\Components\Inputs\SelectInput;
use Admin\Components\Inputs\SelectTagsInput;
use Admin\Components\Inputs\SliderInput;
use Admin\Components\Inputs\SwitcherInput;
use Admin\Components\Inputs\TextareaInput;
use Admin\Components\Inputs\TimeInput;
use Admin\Components\Small\AComponent;
use Admin\Components\Small\CenterComponent;
use Admin\Components\Small\DivComponent;
use Admin\Components\Small\H1Component;
use Admin\Components\Small\H2Component;
use Admin\Components\Small\H3Component;
use Admin\Components\Small\HrComponent;
use Admin\Components\Small\IComponent;
use Admin\Components\Small\ImgComponent;
use Admin\Components\Small\PComponent;
use Admin\Components\Small\SpanComponent;
use Admin\Components\Small\TbodyComponent;
use Admin\Components\Small\TdComponent;
use Admin\Components\Small\ThComponent;
use Admin\Components\Small\TheadComponent;
use Admin\Components\Small\TrComponent;
use Admin\Controllers\SystemController;
use Admin\Core\Delegate;
use Admin\Core\MenuItem;
use Admin\Explanation;
use Admin\Page;
use Admin\Traits\AlpineInjectionTrait;
use Admin\Traits\BootstrapClassHelpers;
use Admin\Traits\BuildHelperTrait;
use Admin\Traits\DataAttributes;
use Admin\Traits\DataTrait;
use Admin\Traits\Delegable;
use Admin\Traits\FieldMassControlTrait;
use Closure;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\View\View;
use Throwable;

/**
 * Renders the component and returns the rendered HTML markup.
 *
 * @methods static::$inputs
 * @mixin ComponentInputsMethods
 *
 * @return View|string The rendered HTML markup.
 */
abstract class ComponentInputs implements Renderable
{
    /**
     * Array of input types and their corresponding classes.
     *
     * @var array
     */
    public static array $inputs = [
        'input' => Input::class,
        'password' => PasswordInput::class,
        'email' => EmailInput::class,
        'number' => NumberInput::class,
        'numeric' => NumericInput::class,
        'amount' => AmountInput::class,
        'file' => FileInput::class,
        'image' => ImageInput::class,
        'switcher' => SwitcherInput::class,
        'date_range' => DateRangeInput::class,
        'date_time_range' => DateTimeRangeInput::class,
        'date' => DateInput::class,
        'date_time' => DateTimeInput::class,
        'time' => TimeInput::class,
        'icon' => IconInput::class,
        'color' => ColorInput::class,
        'select' => SelectInput::class,
        'dual_select' => DualSelectInput::class,
        'multi_select' => MultiSelectInput::class,
        'select_tags' => SelectTagsInput::class,
        'textarea' => TextareaInput::class,
        'ckeditor' => CKEditorInput::class,
        'mdeditor' => MDEditorInput::class,
        'checks' => ChecksInput::class,
        'radios' => RadiosInput::class,
        'codemirror' => CodeMirrorInput::class,
        'info' => InfoInput::class,
        'info_id' => InfoIdInput::class,
        'info_created_at' => InfoCreatedAtInput::class,
        'info_updated_at' => InfoUpdatedAtInput::class,
        'rating' => RatingInput::class,
        'hidden' => HiddenInput::class,
        'autocomplete' => AutocompleteInput::class,
        'slider' => SliderInput::class,
    ];
}
