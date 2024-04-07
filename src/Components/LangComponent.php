<?php

declare(strict_types=1);

namespace Admin\Components;

class LangComponent extends Component
{
    /**
     * @var string
     */
    protected string $view = 'lang';

    /**
     * @var array|null
     */
    protected ?array $lang_list = null;

    /**
     * @var array
     */
    protected array $insideInputs = [];

    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var string|null
     */
    protected ?string $id = null;

    /**
     * @var bool
     */
    protected ?bool $verticalSet = null;

    /**
     * @var bool
     */
    protected ?bool $reversedSet = null;

    /**
     * @var int|null
     */
    protected ?int $label_width = null;

    /**
     * @var int
     */
    protected static int $counter = 0;

    /**
     * @var bool
     */
    protected static bool $tplMode = false;

    /**
     * Lang constructor.
     * @param  array|null  $lang_list
     */
    public function __construct(array $lang_list = null)
    {
        $this->lang_list = $lang_list;

        parent::__construct();
    }

    /**
     * @param  bool  $state
     * @return void
     */
    public static function templateMode(bool $state): void
    {
        static::$tplMode = $state;
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        foreach ($this->contents as $key => $inner_input) {

            if ($inner_input instanceof FormGroupComponent) {

                foreach ($this->lang_list ?: config('admin.languages', []) as $lang) {
                    $input = clone $inner_input;
                    $input->only_input();
                    if (! $this->name) {
                        $this->name = $input->get_title();
                    }
                    if (! $this->id) {
                        $this->id = $input->get_id()
                            . '_lang'
                            . self::$counter++
                            . (static::$tplMode ? '{__val__}' : '');
                    }
                    if ($this->verticalSet === null) {
                        $this->verticalSet = $input->get_vertical();
                    }
                    if ($this->label_width === null) {
                        $this->label_width = $input->get_label_width();
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

    /**
     * @return array
     */
    protected function viewData(): array
    {
        return [
            'insideInputs' => $this->insideInputs,
            'name' => $this->name,
            'id' => $this->id,
            'vertical' => $this->verticalSet,
            'label_width' => $this->label_width,
            'reversed' => $this->reversedSet,
        ];
    }
}
