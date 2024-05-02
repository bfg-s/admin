<?php

declare(strict_types=1);

namespace Admin\Components;

use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class ModalComponent extends Component
{
    /**
     * @var array|ModalComponent[]
     */
    public static array $list = [];

    /**
     * @var int
     */
    protected static int $count = 0;

    /**
     * @var bool
     */
    public bool $temporary = false;

    /**
     * @var mixed|null
     */
    public mixed $submitEvent = null;

    /**
     * @var string
     */
    public string $size = 'default';

    /**
     * @var bool
     */
    public bool $backdrop = false;

    /**
     * @var string
     */
    protected string $view = 'modal';

    /**
     * @var bool
     */
    protected bool $vertical = true;

    /**
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * @var mixed
     */
    protected mixed $body = null;

    /**
     * @var array
     */
    protected array $footer_buttons = [];

    /**
     * @var array
     */
    protected array $left_footer_buttons = [];

    /**
     * @var array
     */
    protected array $center_footer_buttons = [];

    /**
     * @var string
     */
    protected string $modalName;

    /**
     * @var array
     */
    protected array $buttonGroups = [];

    /**
     * @var array
     */
    protected array $modalDelegates = [];

    /**
     * @var string
     */
    protected string $vector = "footer_buttons";

    /**
     * @var View|string|null
     */
    protected View|string|null $renderedView = null;

    /**
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
     * @param  string  $name
     * @return $this
     */
    public function name(string $name): static
    {
        $this->modalName = $name;

        return $this;
    }

    /**
     * @return $this
     */
    public function buttonsLeftVector(): static
    {
        $this->vector = "left_footer_buttons";

        return $this;
    }

    /**
     * @return $this
     */
    public function buttonsCenterVector(): static
    {
        $this->vector = "center_footer_buttons";

        return $this;
    }

    /**
     * @return $this
     */
    public function buttonsRightVector(): static
    {
        $this->vector = "footer_buttons";

        return $this;
    }

    /**
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
     * @param ...$delegates
     * @return $this
     */
    public function modal_body(...$delegates): static
    {
        $this->bodyDelegates = $delegates;

        return $this;
    }

    /**
     * @param ...$delegates
     * @return FormComponent|$this
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function form(...$delegates): FormComponent|static
    {
        if (!$this->body) {
            $this->body = $this->createComponent(ModalBodyComponent::class);
        }

        $this->body->form(...$delegates);

        return $this;
    }

    /**
     * @param ...$delegates
     * @return TableComponent
     */
    public function table(...$delegates): TableComponent
    {
        if (!$this->body) {
            $this->body = $this->createComponent(ModalBodyComponent::class);
            $this->body->addClass('p-0');
        }

        return $this->body->table(...$delegates);
    }

    /**
     * @param ...$delegates
     * @return $this
     */
    public function model_table(...$delegates): static
    {
        if (!$this->body) {
            $this->body = $this->createComponent(ModalBodyComponent::class);
        }

        $this->body->model_table(...$delegates);

        return $this;
    }

    /**
     * @param ...$delegates
     * @return $this
     */
    public function model_info_table(...$delegates): static
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->model_info_table(...$delegates);

        return $this;
    }

    /**
     * @param ...$delegates
     * @return NestedComponent|$this
     */
    public function nested(...$delegates): NestedComponent|static
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->nested(...$delegates);

        return $this;
    }

    /**
     * @param ...$delegates
     * @return CardComponent|$this
     */
    public function card(...$delegates): CardComponent|static
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->card(...$delegates);

        return $this;
    }

    /**
     * @param ...$delegates
     * @return SearchFormComponent|$this
     */
    public function search_form(...$delegates): SearchFormComponent|static
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->search_form(...$delegates);

        return $this;
    }

    /**
     * @param ...$delegates
     * @return ChartJsComponent|$this
     */
    public function chart_js(...$delegates): ChartJsComponent|static
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->chart_js(...$delegates);

        return $this;
    }

    /**
     * @param ...$delegates
     * @return $this
     */
    public function model_relation(...$delegates): static
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->model_relation(...$delegates);

        return $this;
    }

    /**
     * @param ...$delegates
     * @return GridRowComponent|$this
     */
    public function row(...$delegates): GridRowComponent|static
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->row(...$delegates);

        return $this;
    }

    /**
     * @param ...$delegates
     * @return GridColumnComponent|$this
     */
    public function column(...$delegates): GridColumnComponent|static
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->column(...$delegates);

        return $this;
    }

    /**
     * @param  string  $text
     * @return $this
     */
    public function title(string $text): static
    {
        $this->title = $text;

        return $this;
    }

    /**
     * @param  callable  $callable
     * @return $this
     */
    public function submitEvent(callable $callable): static
    {
        $this->submitEvent = $callable;

        return $this;
    }

    /**
     * Extra big size.
     * @return $this
     */
    public function sizeExtra(): static
    {
        $this->size = 'extra';

        return $this;
    }

    /**
     * Big size.
     * @return $this
     */
    public function sizeBig(): static
    {
        $this->size = 'big';

        return $this;
    }

    /**
     * Small size.
     * @return $this
     */
    public function sizeSmall(): static
    {
        $this->size = 'small';

        return $this;
    }


    /**
     * @return $this
     */
    public function temporary(): static
    {
        $this->temporary = true;

        return $this;
    }


    /**
     * @return $this
     */
    public function closable(): static
    {
        $this->backdrop = true;

        return $this;
    }

    /**
     * @return string|View|null
     */
    public function getRenderedView(): string|View|null
    {
        return $this->renderedView;
    }

    /**
     * @return View|string
     * @throws Throwable
     */
    public function render(): View|string
    {
        return $this->renderedView = parent::render(); // TODO: Change the autogenerated stub
    }

    /**
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
