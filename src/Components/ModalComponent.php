<?php

namespace LteAdmin\Components;

use Lar\Layout\Tags\DIV;
use ReflectionException;

class ModalComponent extends Component
{
    /**
     * @var array|ModalComponent[]
     */
    public static array $list = [];

    protected static int $count = 0;
    /**
     * @var bool
     */
    public $temporary = false;
    public $submitEvent = null;
    public $size;
    public bool $backdrop = false;
    protected $vertical = true;
    /**
     * @var string|null
     */
    protected $title;
    /**
     * @var mixed
     */
    protected $body;
    /**
     * @var array
     */
    protected $footer_buttons = [];
    /**
     * @var array
     */
    protected $left_footer_buttons = [];
    /**
     * @var array
     */
    protected $center_footer_buttons = [];
    protected string $modalName;
    protected array $buttonGroups = [];
    protected array $modalDelegates = [];
    protected array $bodyDelegates = [];
    protected string $vector = "footer_buttons";

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

    public function name(string $name)
    {
        $this->modalName = $name;

        return $this;
    }

    public function buttonsLeftVector()
    {
        $this->vector = "left_footer_buttons";
        return $this;
    }

    public function buttonsCenterVector()
    {
        $this->vector = "center_footer_buttons";
        return $this;
    }

    public function buttonsRightVector()
    {
        $this->vector = "footer_buttons";
        return $this;
    }

    public function buttons(...$delegates)
    {
        $group = ButtonsComponent::create(...$delegates);

        $this->{$this->vector}[] = $group;

        return $group;
    }

    public function modal_body(...$delegates)
    {
        $this->bodyDelegates = $delegates;

        return $this;
    }

    public function form(...$delegates)
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->form(...$delegates);

        return $this;
    }

    public function model_table(...$delegates)
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->model_table(...$delegates);

        return $this;
    }

    public function model_info_table(...$delegates)
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->model_info_table(...$delegates);

        return $this;
    }

    public function nested(...$delegates)
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->nested(...$delegates);

        return $this;
    }

    public function card(...$delegates)
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->card(...$delegates);

        return $this;
    }

    public function search_form(...$delegates)
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->search_form(...$delegates);

        return $this;
    }

    public function chart_js(...$delegates)
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->chart_js(...$delegates);

        return $this;
    }

    public function model_relation(...$delegates)
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->model_relation(...$delegates);

        return $this;
    }

    public function row(...$delegates)
    {
        if (!$this->body) {
            $this->body = new ModalBodyComponent();
        }

        $this->body->row(...$delegates);

        return $this;
    }

    public function column(...$delegates)
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
    public function title(string $text)
    {
        $this->title = $text;

        return $this;
    }

    public function submitEvent(callable $callable)
    {
        $this->submitEvent = $callable;

        return $this;
    }

    /**
     * Extra big size.
     * @return $this
     */
    public function sizeExtra()
    {
        $this->size = 'extra';

        return $this;
    }

    /**
     * Big size.
     * @return $this
     */
    public function sizeBig()
    {
        $this->size = 'big';

        return $this;
    }

    /**
     * Small size.
     * @return $this
     */
    public function sizeSmall()
    {
        $this->size = 'small';

        return $this;
    }


    /**
     * @return $this
     */
    public function temporary()
    {
        $this->temporary = true;

        return $this;
    }


    /**
     * @return $this
     */
    public function closable()
    {
        $this->backdrop = true;

        return $this;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    protected function mount()
    {
        $request = request();

        $this->delegatesNow($this->modalDelegates);

        if ($request->_modal == $this->modalName || ($request->ajax() && !$request->pjax())) {
            $this->addClass('modal-content');

            if (!$this->body) {
                $this->body = new ModalBodyComponent();
            }

            ModalComponent::$list[$this->modalName] = $this;

            $this->body->delegatesNow($this->bodyDelegates);
            $this->body->delegatesNow($this->modalDelegates);
            $this->setDatas(['modal-name' => $this->modalName]);

            $this->div(['modal-header'])->when(function (DIV $div) {
                $div->h5(['modal-title'])->text($this->title ?: ':space');
                $div->a(['refresh_modal', 'href' => 'javascript:void(0)'])
                    ->span()->text('⟳');
                $div->a(['close', 'style' => 'margin-left: 8px; padding-left: 0', 'href' => 'javascript:void(0)'])
                    ->span(['aria-hidden' => 'true'])->text('&times;');
            });

            $this->appEnd($this->body);

            if (count($this->footer_buttons)) {
                $footer = $this->div(['modal-footer']);
                $row = $footer->row();
                $col_l = $row->div(['col-auto'])->textLeft();
                $col_c = $row->div(['col-auto'])->textCenter();
                $col_r = $row->div(['col-auto'])->textRight();
                foreach ($this->left_footer_buttons as $footer_button) {
                    $col_l->appEnd($footer_button);
                }
                foreach ($this->center_footer_buttons as $footer_button) {
                    $col_c->appEnd($footer_button);
                }
                foreach ($this->footer_buttons as $footer_button) {
                    $col_r->appEnd($footer_button);
                }
            }
        } else {
            $this->only_content = true;
        }
    }
}
