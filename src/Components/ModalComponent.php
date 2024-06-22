<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Facades\Admin;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * Modal window component of the admin panel.
 */
class ModalComponent extends Component
{
    /**
     * List of modal windows on the page, used to load a modal window.
     *
     * @var array|ModalComponent[]
     */
    public static array $list = [];

    /**
     * Modal window counter for generating unique names of modal windows.
     *
     * @var int
     */
    protected static int $count = 0;

    /**
     * The modal window mode is temporary.
     *
     * @var bool
     */
    public bool $temporary = false;

    /**
     * Callback for sending form data.
     *
     * @var mixed|null
     */
    public mixed $submitEvent = null;

    /**
     * Modal window size.
     *
     * @var string
     */
    public string $size = 'default';

    /**
     * Mode to disable a modal window by clicking on the background of the modal window.
     *
     * @var bool
     */
    public bool $backdrop = false;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'modal';

    /**
     * Title of the modal window.
     *
     * @var string|null
     */
    protected string|null $title = null;

    /**
     * The body of the modal window.
     *
     * @var \Admin\Components\ModalBodyComponent|null
     */
    protected ModalBodyComponent|null $body = null;

    /**
     * Groups of buttons located on the right.
     *
     * @var array
     */
    protected array $footer_buttons = [];

    /**
     * Groups of buttons located on the left.
     *
     * @var array
     */
    protected array $left_footer_buttons = [];

    /**
     * Groups of buttons located in the center.
     *
     * @var array
     */
    protected array $center_footer_buttons = [];

    /**
     * The unique name of the modal window.
     *
     * @var string
     */
    protected string $modalName;

    /**
     * Modal window delegations.
     *
     * @var array
     */
    protected array $modalDelegates = [];

    /**
     * The action vector of the standard method of adding buttons.
     *
     * @var string
     */
    protected string $vector = "footer_buttons";

    /**
     * ModalComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        parent::__construct();

        $this->modalDelegates = $delegates;

        if (static::$count) {
            $this->modalName = 'modal-'.static::$count;
        } else {
            $this->modalName = 'modal';
        }

        static::$count++;
    }

    /**
     * Set a custom name for the modal window. Must be unique on the page.
     *
     * @param  string  $name
     * @return $this
     */
    public function name(string $name): static
    {
        $this->modalName = $name;

        return $this;
    }

    /**
     * Set the vector for adding buttons using the standard method to the left one.
     *
     * @return $this
     */
    public function buttonsLeftVector(): static
    {
        $this->vector = "left_footer_buttons";

        return $this;
    }

    /**
     * Set the vector for adding buttons using the standard method, in the center.
     *
     * @return $this
     */
    public function buttonsCenterVector(): static
    {
        $this->vector = "center_footer_buttons";

        return $this;
    }

    /**
     * Set the vector for adding buttons using the standard method, on the right (default).
     *
     * @return $this
     */
    public function buttonsRightVector(): static
    {
        $this->vector = "footer_buttons";

        return $this;
    }

    /**
     * Adding a group of buttons to the current vector.
     *
     * @param ...$delegates
     * @return ButtonsComponent
     */
    public function buttons(...$delegates): ButtonsComponent
    {
        $group = ButtonsComponent::create(...$delegates);

        $this->{$this->vector}[] = $group;

        return $group;
    }

    /**
     * Adding a form to the body of a modal window.
     *
     * @param ...$delegates
     * @return FormComponent|$this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function form(...$delegates): FormComponent|static
    {
        $this->createBody()->form(...$delegates);

        return $this;
    }

    /**
     * Adding a table to the body of the modal window.
     *
     * @param ...$delegates
     * @return TableComponent
     */
    public function table(...$delegates): TableComponent
    {
        $this->createBody();

        return $this->body
            ->withOutPadding()
            ->table(...$delegates);
    }

    /**
     * Adding a model table to the body of the modal window.
     *
     * @param ...$delegates
     * @return $this
     */
    public function model_table(...$delegates): static
    {
        $this->createBody()->model_table(...$delegates);

        return $this;
    }

    /**
     * Adding a model info table to the body of the modal window.
     *
     * @param ...$delegates
     * @return $this
     */
    public function model_info_table(...$delegates): static
    {
        $this->createBody()->model_info_table(...$delegates);

        return $this;
    }

    /**
     * Create a new VUE component.
     *
     * @param  string  $class
     * @param  array  $params
     * @return \Admin\Components\ModalComponent
     */
    public function vue(string $class, array $params = []): static
    {
        $body = $this->createBody();

        $body->appEnd(
            $body->createComponent($class)->attr($params)
        );

        return $this;
    }

    /**
     * Adding a nested component to the body of the modal window.
     *
     * @param ...$delegates
     * @return NestedComponent|$this
     */
    public function nested(...$delegates): NestedComponent|static
    {
        $this->createBody()->nested(...$delegates);

        return $this;
    }

    /**
     * Adding a card to the body of the modal window.
     *
     * @param ...$delegates
     * @return CardComponent|$this
     */
    public function card(...$delegates): CardComponent|static
    {
        $this->createBody()->card(...$delegates);

        return $this;
    }

    /**
     * Adding a search form to the body of the modal window.
     *
     * @param ...$delegates
     * @return SearchFormComponent|$this
     */
    public function search_form(...$delegates): SearchFormComponent|static
    {
        $this->createBody()->search_form(...$delegates);

        return $this;
    }

    /**
     * Adding a chart to the body of the modal window.
     *
     * @param ...$delegates
     * @return ChartJsComponent|$this
     */
    public function chart_js(...$delegates): ChartJsComponent|static
    {
        $this->createBody()->chart_js(...$delegates);

        return $this;
    }

    /**
     * Adding a modal window model relationship.
     *
     * @param ...$delegates
     * @return $this
     */
    public function model_relation(...$delegates): static
    {
        $this->createBody()->model_relation(...$delegates);

        return $this;
    }

    /**
     * Adding grid components to the modal window row.
     *
     * @param ...$delegates
     * @return GridRowComponent|$this
     */
    public function row(...$delegates): GridRowComponent|static
    {
        $this->createBody()->row(...$delegates);

        return $this;
    }

    /**
     * Adding a grid component to a modal window column.
     *
     * @param ...$delegates
     * @return GridColumnComponent|$this
     */
    public function column(...$delegates): GridColumnComponent|static
    {
        $this->createBody()->column(...$delegates);

        return $this;
    }

    /**
     * Modal use for adding to the body of the modal window.
     *
     * @param  callable|string|array  $callable
     * @return $this
     */
    public function use(callable|string|array $callable): static
    {
        if (request('_modal') === $this->modalName) {

            $this->createBody()->use($callable);
        }

        return $this;
    }

    /**
     * Create if the body of the modal window does not exist and return it.
     *
     * @return ModalBodyComponent
     */
    protected function createBody(): ModalBodyComponent
    {
        if (!$this->body) {

            $this->body = $this->createComponent(ModalBodyComponent::class);
        }

        return $this->body;
    }

    /**
     * Set the title of the modal window.
     *
     * @param  string  $text
     * @return $this
     */
    public function title(string $text): static
    {
        $this->title = $text;

        return $this;
    }

    /**
     * Set an event when form data is submitted from a modal window.
     *
     * @param  callable  $callable
     * @return $this
     */
    public function submitEvent(callable $callable): static
    {
        $this->submitEvent = $callable;

        return $this;
    }

    /**
     * Set the modal window size to very large.
     *
     * @return $this
     */
    public function sizeExtra(): static
    {
        $this->size = 'extra';

        return $this;
    }

    /**
     * Set the modal window size to large.
     *
     * @return $this
     */
    public function sizeBig(): static
    {
        $this->size = 'big';

        return $this;
    }

    /**
     * Set the modal window size to small.
     *
     * @return $this
     */
    public function sizeSmall(): static
    {
        $this->size = 'small';

        return $this;
    }

    /**
     * Enable temporary mode for the modal window.
     *
     * @return $this
     */
    public function temporary(): static
    {
        $this->temporary = true;

        return $this;
    }


    /**
     * Enable closable mode for the modal window (by background).
     *
     * @return $this
     */
    public function closable(): static
    {
        $this->backdrop = true;

        return $this;
    }

    /**
     * Get the modal name.
     *
     * @return string
     */
    public function getModalName(): string
    {
        return $this->modalName;
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array
     */
    protected function viewData(): array
    {
        $request = request();

        return [
            'write' => $request->_modal == $this->modalName || ($request->ajax() && !$request->pjax()),
            'body' => $this->body,
            'modalName' => $this->modalName,
            'title' => $this->title,
            'footer_buttons' => $this->footer_buttons,
            'left_footer_buttons' => $this->left_footer_buttons,
            'center_footer_buttons' => $this->center_footer_buttons,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        $request = request();

        $this->delegatesNow($this->modalDelegates);

        if (
            $request->_modal == $this->modalName
            || ($request->ajax() && !$request->pjax())
            || $request->_build_modal
        ) {
            if (!$this->body) {
                $this->body = new ModalBodyComponent();
            }

            ModalComponent::$list[$this->modalName] = $this;

            $this->body->delegatesNow($this->bodyDelegates);
            $this->body->delegatesNow($this->modalDelegates);

            $this->appEnd($this->body);
        }
    }
}
