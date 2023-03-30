<?php

namespace Admin\Components\Inputs;

use Admin\Components\Component;
use Admin\Traits\Stateable;

/**
 * @property string|array|null $value
 * @property string|null $field
 * @property string|null $path
 */
abstract class InputComponent extends Component
{
    use Stateable;

    protected array $state = [
        'value' => null,
        'field' => null,
        'path' => null,
    ];

    protected $store = '';
    protected $storePath = '';

    public function __construct($field, ...$delegates)
    {
        $this->field = $field;
        $this->path = str_replace('..', '.', trim(preg_replace('/[^A-Za-z0-9-_]/', '.', $field), '.'));

        parent::__construct(...$delegates);

        $this->value = old($this->path);
        if (!$this->value) {
            $this->value = request(str_replace('.', '_', $this->path));
        }
        if (!$this->value) {
            $modelData = multi_dot_call($this->model, $this->path, false);
            $this->value = is_array($modelData) ? $modelData : e($modelData);
        }
        $this->page->toStore($this->model_name, [
            $this->field => $this->value,
        ]);
        $this->storePath = $this->model_name.'.'.$this->field;
        $this->store = '$store.'.$this->storePath;
    }

    public function default($default = null)
    {
        if (!$this->value) {
            $this->value = $default;
        }

        return $this;
    }

    public function onRender()
    {
        $js = $this->javascript();

        $this->xData("{value: $this->store}");

        if ($js) {
            $this->xInit($js);
        }

        parent::onRender();
    }

    protected function javascript()
    {
        return '';
    }
}
