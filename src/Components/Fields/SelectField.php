<?php

namespace LteAdmin\Components\Fields;

use App;
use Closure;
use Illuminate\Contracts\Support\Arrayable;
use Lar\Layout\Abstracts\Component;
use Lar\Layout\Tags\INPUT;
use LteAdmin\Components\Cores\Select2FieldCore;
use LteAdmin\Components\FormGroupComponent;
use LteAdmin\Core\Select2;
use LteAdmin\Page;
use ReflectionException;

class SelectField extends FormGroupComponent
{
    /**
     * @var string
     */
    protected $icon = 'fas fa-mouse-pointer';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string
     */
    protected $class = 'form-control';

    /**
     * @var string[]
     */
    protected $data = [
        'load' => 'select2::init',
        'theme' => 'bootstrap4',
    ];

    /**
     * @var mixed
     */
    protected $load_subject;

    /**
     * @var string
     */
    protected $load_format;

    /**
     * @var Closure|array
     */
    protected $load_where;

    /**
     * @var bool
     */
    protected $nullable = false;

    /**
     * @return Component|INPUT|mixed
     * @throws ReflectionException
     */
    public function field()
    {
        if ($this->load_subject) {
            $selector = new Select2(
                $this->load_subject,
                $this->load_format,
                $this->value,
                $this->nullable ? $this->title : null,
                $this->field_id.'_',
                $this->load_where
            );

            $r_name = $selector->getName();

            if (request()->has($r_name)) {
                exit($selector->toJson(JSON_UNESCAPED_UNICODE));
            }

            $this->data['select-name'] = $r_name;

            $this->on_load('select2::ajax');

            $vals = $selector->getValueData();

            if ($vals) {
                $this->options($vals, true);
            }
        }

        app(Page::class)->toStore('live', [$this->path => $this->value]);

        return Select2FieldCore::create($this->options, [
            'name' => $this->name,
            'data-placeholder' => $this->title,
            'id' => $this->field_id,
        ], ...$this->params)
            ->setValues($this->value)
            ->makeOptions()
            ->setDatas($this->data)
            ->addClassIf($this->has_bug, 'is-invalid')
            ->addClass($this->class);
    }

    /**
     * @param  array|Arrayable  $options
     * @param  bool  $first_default
     * @return $this
     */
    public function options($options, bool $first_default = false)
    {
        if ($options instanceof Arrayable) {
            $options = $options->toArray();
        }

        $lang = App::getLocale();

        foreach ($options as $k => $option) {
            $this->options[$k] = $option;
        }

        foreach ($this->options as $k => $option) {
            if ($option && is_array($option)) {
                $this->options[$k] = $option[$lang] ?? implode(', ', $option);
            } else {
                $this->options[$k] = $option;
            }
        }

        if ($first_default && !$this->nullable) {
            $this->default(array_key_first($this->options));
        }

        return $this;
    }

    /**
     * @param $subject
     * @param  string  $format
     * @param  Closure|array|null  $where
     * @return $this
     */
    public function load($subject, string $format = 'id:name', $where = null)
    {
        $this->load_subject = $subject;
        $this->load_format = $format;
        $this->load_where = $where;

        if ($where) {
            $this->data['with-where'] = 'true';
        }

        return $this;
    }

    /**
     * @param  string|null  $message
     * @return SelectField
     */
    public function nullable(string $message = null)
    {
        $this->nullable = true;

        if ($this->options) {
            $opts = ['' => 'none'];
            foreach ($this->options as $k => $option) {
                $opts[$k] = $option;
            }
            $this->options = $opts;
        } else {
            $this->options = ['' => 'none'];
        }

        $this->data['allow-clear'] = 'true';

        return parent::nullable($message);
    }
}
