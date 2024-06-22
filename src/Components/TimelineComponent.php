<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Facades\Admin;
use Admin\Traits\Resouceable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/**
 * Timeline component of the admin panel.
 */
class TimelineComponent extends Component
{
    use Resouceable;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'timeline';

    /**
     * Data that needs to be displayed before the timeline.
     *
     * @var callable|string|null
     */
    protected mixed $prepend = null;

    /**
     * Icon field.
     *
     * @var callable|string|null
     */
    protected mixed $icon_field = 'icon';

    /**
     * Icon title.
     *
     * @var callable|string|null
     */
    protected mixed $title_field = 'title';

    /**
     * Body timeline components.
     *
     * @var callable|string|null
     */
    protected $body = null;

    /**
     * Footer timeline components.
     *
     * @var callable|string|null
     */
    protected $footer = null;

    /**
     * Timeline entries per page.
     *
     * @var int
     */
    protected int $per_page = 15;

    /**
     * It is possible to display timeline entries on a page.
     *
     * @var int[]
     */
    protected array $per_pages = [10, 15, 20, 50, 100];

    /**
     * Timeline sort field.
     *
     * @var string
     */
    protected string $order_field = 'created_at';

    /**
     * Timeline sorting type.
     *
     * @var string
     */
    protected string $order_type = 'desc';

    /**
     * Data that needs to be displayed after the timeline.
     *
     * @var callable|string|null
     */
    protected $append = null;

    /**
     * Display at full width and height without padding.
     *
     * @var bool
     */
    protected bool $full_body = false;

    /**
     * Realtime marker, if enabled, the component will be updated at the specified frequency.
     *
     * @var bool
     */
    protected bool $realTime = true;

    /**
     * TimelineComponent constructor.
     *
     * @param ...$delegates
     */
    public function __construct(...$delegates)
    {
        $this->per_page = config('admin.timeline-component.per_page', $this->per_page);
        $this->per_pages = config('admin.timeline-component.per_pages', $this->per_pages);
        $this->order_field = config('admin.timeline-component.order_field', $this->order_field);
        $this->order_type = config('admin.timeline-component.order_type', $this->order_type);
        $this->icon_field = config('admin.timeline-component.icon_field', $this->icon_field);
        $this->title_field = config('admin.timeline-component.title_field', $this->title_field);

        parent::__construct();

        $this->delegatesNow(...$delegates);

        if ($this->model) {

            $this->model_name = $this->page->getModelName($this->model);
        }
    }

    /**
     * Set timeline icon field.
     *
     * @param  callable|string  $icon
     * @return $this
     */
    public function set_icon(callable|string $icon): static
    {
        $this->icon_field = $icon;

        return $this;
    }

    /**
     * Set the timeline title field.
     *
     * @param  callable|string  $title
     * @return $this
     */
    public function set_title(callable|string $title): static
    {
        if (is_string($title) || is_embedded_call($title)) {
            $this->title_field = $title;
        }

        return $this;
    }

    /**
     * Set body timeline components.
     *
     * @param  callable|string  $body
     * @return $this
     */
    public function set_body(callable|string $body): static
    {
        if (is_string($body) || is_embedded_call($body)) {
            $this->body = $body;
        }

        return $this;
    }

    /**
     * Set footer timeline components.
     *
     * @param  callable|string  $footer
     * @return $this
     */
    public function set_footer(callable|string $footer): static
    {
        if (is_string($footer) || is_embedded_call($footer)) {
            $this->footer = $footer;
        }

        return $this;
    }

    /**
     * Set the number of entries per timeline page.
     *
     * @param  int  $per_page
     * @return $this
     */
    public function set_per_page(int $per_page): static
    {
        $this->per_page = $per_page;

        return $this;
    }

    /**
     * Set the timeline sorting type.
     *
     * @param  string  $order_type
     * @return $this
     */
    public function set_order_type(string $order_type): static
    {
        $this->order_type = $order_type;

        return $this;
    }

    /**
     * Set the timeline body mode to wide, without indents.
     *
     * @return $this
     */
    public function setFullBody(): static
    {
        $this->full_body = true;

        return $this;
    }

    /**
     * Generate and receive timeline pagination.
     *
     * @return mixed
     */
    protected function getPaginate(): mixed
    {
        if (!$this->model) {
            return [];
        }

        if ($this->model instanceof Model) {
            $paginate = new (get_class($this->model));
        } elseif (is_string($this->model)) {
            $paginate = new $this->model();
        } elseif (is_array($this->model)) {
            $paginate = collect($this->model);
        } else {
            $paginate = $this->model;
        }

        return ($paginate instanceof Collection
            ? $paginate->sortByDesc($this->order_field, 'desc')
            : $paginate->orderBy($this->order_field, 'desc')
        )->paginate($this->per_page, ['*'], $this->model_name.'_page');
    }

    /**
     * @param $paginate
     * @return View|string
     */
    protected function paginateFooter($paginate): string|View
    {
        return $this->model_name && $paginate ? admin_view('components.time-line.paginate-footer', [
            'model' => $this->model,
            'paginator' => $paginate,
            'from' => (($paginate->currentPage() * $paginate->perPage()) - $paginate->perPage()) + 1,
            'to' => ($paginate->currentPage() * $paginate->perPage()) > $paginate->total() ? $paginate->total() : ($paginate->currentPage() * $paginate->perPage()),
            'per_page' => $this->per_page,
            'per_pages' => $this->per_pages,
            'elements' => $this->paginationElements($paginate),
            'page_name' => $this->model_name.'_page',
            'per_name' => $this->model_name.'_per_page',
        ]) : '';
    }

    /**
     * Create a timeline footer with pagination.
     *
     * @param  LengthAwarePaginator  $page
     * @return array
     */
    protected function paginationElements(LengthAwarePaginator $page): array
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

    /**
     * Get the extension value and property with the call.
     *
     * @param  string  $segment
     * @param $model
     * @param $area
     * @param  null  $default
     * @return mixed
     * @throws Throwable
     */
    protected function callCallableExtender(string $segment, $model, $area, $default = null): mixed
    {
        $result = $this->{$segment};

        return $result && is_string($result) ? ($model->{$result} ?: $default) : (
        $result && is_embedded_call($result) ? embedded_call($result, [
            get_class($area) => $area,
            (is_object($model) ? get_class($model) : 'model') => $model,
            'model' => $model,
            static::class => $this,
        ]) : $default
        );
    }

    /**
     * Get the current value and property with a call.
     *
     * @param  string  $segment
     * @param $area
     * @return mixed
     * @throws Throwable
     */
    protected function callCallableCurrent(string $segment, $area): mixed
    {
        $isProp = $segment && property_exists($this, $segment);
        $value = $isProp ? $this->{$segment} : null;

        return $value && is_string($value) ? $value : (
        $value && is_embedded_call($value) ? embedded_call($value, [
            get_class($area) => $area,
            static::class => $this,
        ]) : $value
        );
    }

    /**
     * Additional data to be sent to the template.
     *
     * @return array|null[]|string[]
     */
    protected function viewData(): array
    {

        return [
            'full_body' => $this->full_body,
            'per_page' => $this->per_page,
            'order_type' => $this->order_type,
            'prepend' => $this->prepend,
            'order_field' => $this->order_field,
            'append' => $this->append,
            'paginate' => $paginate = $this->getPaginate(),
            'paginateFooter' => $this->paginateFooter($paginate),
            'icon' => function ($model) {
                return $this->callCallableExtender('icon_field', $model, $this);
            },
            'title' => function ($model) {
                return $this->callCallableExtender('title_field', $model, $this);
            },
            'body' => function ($model) {
                return $this->callCallableExtender('body', $model, $this);
            },
            'footer' => function ($model) {
                return $this->callCallableExtender('footer', $model, $this);
            },
        ];
    }

    /**
     * Component data for API requests.
     *
     * @return array
     */
    protected function apiData(): array
    {
        Admin::important($this->model_name, $this->getPaginate(), $this->getResource());
        Admin::expectedQuery($this->model_name.'_page');
        Admin::expectedQuery($this->model_name.'_per_page');

        return [
            'full_body' => $this->full_body,
            'per_page' => $this->per_page,
            'order_type' => $this->order_type,
            'prepend' => $this->prepend,
            'order_field' => $this->order_field,
            'append' => $this->append,
        ];
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     * @throws Throwable
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function mount(): void
    {
        $this->per_page = $this->callCallableCurrent('per_page', $this);
        $this->order_type = $this->callCallableCurrent('order_type', $this);
        $this->prepend = $this->callCallableCurrent('prepend', $this);
        $this->append = $this->callCallableCurrent('append', $this);

        if (request()->has($this->model_name.'_per_page')) {
            $this->per_page = (int) request()->get($this->model_name.'_per_page');
        }
    }
}
