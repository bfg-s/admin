<?php

namespace Lar\LteAdmin\Segments\Tagable;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Collection;
use Lar\Layout\Tags\DIV;
use Lar\Layout\Tags\H3;
use Lar\LteAdmin\Segments\Segment;

/**
 * Class Timeline
 * @package Lar\LteAdmin\Segments\Tagable
 * @methods Lar\LteAdmin\Segments\Tagable\Field::$form_components (string $name, string $label = null, ...$params)
 * @mixin TimelineMacroList
 * @mixin TimelineMethods
 */
class Timeline extends Segment {

    /**
     * @var string
     */
    protected $class = 'timeline';

    /**
     * @var callable|string|null
     */
    protected $prepend = null;

    /**
     * @var callable|string|null
     */
    protected $icon = 'icon';

    /**
     * @var callable|string|null
     */
    protected $title = 'title';

    /**
     * @var callable|string|null
     */
    protected $body = null;

    /**
     * @var callable|string|null
     */
    protected $footer = null;

    /**
     * @var int
     */
    protected $per_page = 15;

    /**
     * @var int[]
     */
    protected $per_pages = [10, 15, 20, 50, 100];

    /**
     * @var string
     */
    protected $order_field = 'created_at';

    /**
     * @var string
     */
    protected $order_type = 'desc';

    /**
     * @var callable|string|null
     */
    protected $append = null;

    /**
     * @param ...$params
     */
    public function __construct(...$params)
    {
        parent::__construct(...$this->expectModel($params));

        $this->addClass($this->class);
    }

    /**
     * @param callable|string $icon
     * @return $this
     */
    public function set_icon($icon)
    {
        if (is_string($icon) || is_embedded_call($icon)) {
            $this->icon = $icon;
        }

        return $this;
    }

    /**
     * @param callable|string $title
     * @return $this
     */
    public function set_title($title)
    {
        if (is_string($title) || is_embedded_call($title)) {
            $this->title = $title;
        }

        return $this;
    }

    /**
     * @param callable|string $body
     * @return $this
     */
    public function set_body($body)
    {
        if (is_string($body) || is_embedded_call($body)) {
            $this->body = $body;
        }

        return $this;
    }

    /**
     * @param callable|string $footer
     * @return $this
     */
    public function set_footer($footer)
    {
        if (is_string($footer) || is_embedded_call($footer)) {
            $this->footer = $footer;
        }

        return $this;
    }

    /**
     * @param callable|string $per_page
     * @return $this
     */
    public function set_per_page($per_page)
    {
        if (is_string($per_page) || is_embedded_call($per_page)) {
            $this->per_page = $per_page;
        }

        return $this;
    }

    /**
     * @param callable|string $order_type
     * @return $this
     */
    public function set_order_type($order_type)
    {
        if (is_string($order_type) || is_embedded_call($order_type)) {
            $this->order_type = $order_type;
        }

        return $this;
    }

    /**
     * @throws \Throwable
     */
    protected function mount()
    {
        $this->per_page = $this->callCallableCurrent('per_page', $this);

        $this->order_type = $this->callCallableCurrent('order_type', $this);

        if (request()->has($this->model_name . '_per_page') && in_array(request()->get($this->model_name . '_per_page'), $this->per_pages)) {

            $this->per_page = (string)request()->get($this->model_name . '_per_page');
        }

        $paginate = $this->getPaginate();

        $this->text(
            $this->callCallableCurrent('prepend', $this)
        );

        foreach ($paginate as $model) {

            $rootDiv = $this->div();

            if ($model[$this->order_field] instanceof Carbon && $model[$this->order_field]->day == 1) {

                $rootDiv->div(['time-label'])->span(['bg-green'])->text($model[$this->order_field]->toDateTimeString());
            }

            $rootDiv->when(function (DIV $div) use ($model) {

                $i = $div->i();

                $icon = $this->callCallableExtender('icon', $model, $i, 'fas fa-lightbulb bg-blue');

                if ($icon && is_string($icon)) {
                    $i->addClass($icon);
                }

                $div->div(['timeline-item'], function (DIV $div) use ($model) {

                    $div->span(['time'])->i(['fas fa-clock'])->_()->text($model[$this->order_field] ? " " . butty_date_time($model[$this->order_field]) : '');

                    $h3 = $div->h3(['timeline-header']);

                    $title = $this->callCallableExtender('title', $model, $h3);

                    if ($title && is_string($title)) {
                        $h3->text($title);
                    }

                    if ($this->body) {
                        $div = $div->div(['timeline-body']);
                        $body = $this->callCallableExtender('body', $model, $div);

                        if ($body && is_string($body)) {
                            $div->text($body);
                        }
                    }

                    if ($this->footer) {
                        $div = $div->div(['timeline-footer']);
                        $footer = $this->callCallableExtender('footer', $model, $div);
                        if ($footer && is_string($footer)) {
                            $div->text($footer);
                        }
                    }
                });
            });
        }

        if ($paginate->lastPage() == $paginate->currentPage()) {

            $this->div()->i(['fas fa-clock bg-gray']);
        }

        $this->text(
            $this->callCallableCurrent('append', $this)
        );

        if ($paginate) {

            $this->appEnd(
                $this->paginateFooter($paginate)
            );
        }
    }

    /**
     * @param  string  $segment
     * @param $model
     * @param $area
     * @param  null  $default
     * @return mixed
     * @throws \Throwable
     */
    protected function callCallableExtender(string $segment, $model, $area, $default = null)
    {
        $s = $this->{$segment};
        return $s && is_string($s) ? ($model->{$s} ?: $default) : (
            $s && is_embedded_call($s) ? embedded_call($s, [
                get_class($area) => $area,
                (is_object($model) ? get_class($model) : 'model') => $model,
                'model' => $model,
                static::class => $this,
            ])  : $default
        );
    }

    /**
     * @param  string  $segment
     * @param $area
     * @return mixed
     * @throws \Throwable
     */
    protected function callCallableCurrent(string $segment, $area)
    {
        $isProp = $segment && property_exists($this, $segment);
        $value = $isProp ? $this->{$segment} : null;

        return $value && is_string($value) ? $value : (
            $value && is_embedded_call($value) ? embedded_call($value, [
                get_class($area) => $area,
                static::class => $this,
            ])  : $value
        );
    }

    /**
     * @return array|\Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Pagination\LengthAwarePaginator
     */
    protected function getPaginate()
    {
        if (!$this->model) {
            return [];
        }

        if ($this->model instanceof Model) {

            $paginate = new (get_class($this->model));

        } else if (is_string($this->model)) {

            $paginate = new $this->model;

        } else if (is_array($this->model)) {

            $paginate = collect($this->model);

        } else {

            $paginate = $this->model;
        }

        return ($paginate instanceof Collection
                    ? $paginate->sortByDesc($this->order_field, 'desc')
                    : $paginate->orderBy($this->order_field, 'desc')
                )->paginate($this->per_page, ['*'],$this->model_name . "_page");
    }

    /**
     * @return DIV|string
     * @throws \Throwable
     */
    protected function paginateFooter($paginate)
    {
        return $this->model_name && $paginate ? DIV::create(['paginate-footer'])->view('lte::segment.paginate_footer', [
            'model' => $this->model,
            'paginator' => $paginate,
            'from' => (($paginate->currentPage() * $paginate->perPage()) - $paginate->perPage()) + 1,
            'to' => ($paginate->currentPage() * $paginate->perPage()) > $paginate->total() ? $paginate->total() : ($paginate->currentPage() * $paginate->perPage()),
            'per_page' => $this->per_page,
            'per_pages' => $this->per_pages,
            'elements' => $this->paginationElements($paginate),
            'page_name' => $this->model_name . "_page",
            'per_name' => $this->model_name . '_per_page',
        ]) : '';
    }

    /**
     * Get the array of elements to pass to the view.
     *
     * @param LengthAwarePaginator $page
     * @return array
     */
    protected function paginationElements(LengthAwarePaginator $page)
    {
        $window = UrlWindow::make($page);

        return array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }
}
