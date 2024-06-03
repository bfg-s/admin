<?php

declare(strict_types=1);

namespace Admin\Components;

/**
 * The language component of the input turns the content input into tabs with languages.
 */
class LangComponent extends Component
{
    /**
     * Language wrapper input counter.
     *
     * @var int
     */
    protected static int $counter = 0;

    /**
     * Template mode is enabled for the model relation component.
     *
     * @var bool
     */
    protected static bool $tplMode = false;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'lang';

    /**
     * List of languages that need to be processed.
     *
     * @var array|null
     */
    protected ?array $lang_list = null;

    /**
     * List of internal inputs for the language field.
     *
     * @var array
     */
    protected array $insideInputs = [];

    /**
     * The title of the language input group.
     *
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * Identifier of the language input group.
     *
     * @var string|null
     */
    protected ?string $id = null;

    /**
     * Vertical mode.
     *
     * @var bool
     */
    protected ?bool $verticalSet = null;

    /**
     * Reverse mode.
     *
     * @var bool
     */
    protected ?bool $reversedSet = null;

    /**
     * Label width in columns.
     *
     * @var int|null
     */
    protected ?int $labelWidth = null;

    /**
     * LangComponent constructor.
     *
     * @param  array|null  $lang_list
     */
    public function __construct(array $lang_list = null)
    {
        $this->lang_list = $lang_list;

        parent::__construct();
    }

    /**
     * Set template mode.
     *
     * @param  bool  $state
     * @return void
     */
    public static function templateMode(bool $state): void
    {
        static::$tplMode = $state;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'inside_inputs' => $this->insideInputs,
            'title' => $this->title,
            'id' => $this->id,
            'vertical' => $this->verticalSet,
            'label_width' => $this->labelWidth,
            'reversed' => $this->reversedSet,
            'current_lang' => \Illuminate\Support\Facades\App::getLocale(),
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        foreach ($this->contents as $key => $inner_input) {
            if ($inner_input instanceof InputGroupComponent) {
                foreach ($this->lang_list ?: config('admin.languages', []) as $lang) {
                    $input = clone $inner_input;
                    $input->only_input();
                    if (!$this->title) {
                        $this->title = $input->get_title();
                    }
                    if (!$this->id) {
                        $this->id = $input->get_id()
                            .'_lang'
                            .self::$counter++
                            .(static::$tplMode ? '{__val__}' : '');
                    }
                    if ($this->verticalSet === null) {
                        $this->verticalSet = $input->get_vertical();
                    }
                    if ($this->labelWidth === null) {
                        $this->labelWidth = $input->get_label_width();
                    }
                    if ($this->reversedSet === null) {
                        $this->reversedSet = $input->get_reversed();
                    }
                    $input->set_name($input->get_name()."[{$lang}]");
                    $input->set_path($input->get_path().".{$lang}");
                    $input->set_id($input->get_id()."_{$lang}");
                    $input->set_title($input->get_title().' ['.strtoupper($lang).']');
                    $this->insideInputs[$lang] = $input;
                }

                unset($this->contents[$key]);
            }
        }
    }
}
