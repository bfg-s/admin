<?php

namespace Admin\Components;

use Admin\Traits\FontAwesome;

class StatisticPeriodComponent extends Component
{
    use FontAwesome;

    /**
     * @var string
     */
    protected string $view = 'statistic-period';

    /**
     * @var string
     */
    protected $class = 'row';

    /**
     * @var mixed
     */
    protected $model = null;

    /**
     * @var mixed
     */
    protected mixed $entity = null;

    /**
     * @var string|null
     */
    protected ?string $icon = 'fas fa-lightbulb';

    /**
     * @param  string|null  $nameOfSubject
     * @return $this
     */
    public function title(string $nameOfSubject = null): static
    {
        $this->entity = __($nameOfSubject);

        return $this;
    }

    /**
     * @return $this
     */
    public function forToday(): static
    {
        $this->column()
            ->info_box(
                __('admin.statistic_for_today', ['entity' => $this->entity]),
                $this->model->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count().' ',
                $this->icon
            )->successType();

        return $this;
    }

    /**
     * @return $this
     */
    public function perWeek(): static
    {
        $this->column()
            ->info_box(
                __('admin.statistic_per_week', ['entity' => $this->entity]),
                $this->model->whereBetween(
                    'created_at',
                    [now()->subWeek()->startOfDay(), now()->endOfDay()]
                )->count().' ',
                $this->icon
            )->infoType();

        return $this;
    }

    /**
     * @return $this
     */
    public function perYear(): static
    {
        $this->column()
            ->info_box(
                __('admin.statistic_per_year', ['entity' => $this->entity]),
                $this->model->whereBetween(
                    'created_at',
                    [now()->subYear()->startOfDay(), now()->endOfDay()]
                )->count().' ',
                $this->icon
            )->warningType();

        return $this;
    }

    /**
     * @return $this
     */
    public function total(): static
    {
        $this->column()
            ->info_box(
                __('admin.statistic_total', ['entity' => mb_strtolower($this->entity)]),
                $this->model->count().' ',
                $this->icon
            )->primaryType();

        return $this;
    }

    /**
     * @param  string  $name
     * @return $this
     */
    public function icon(string $name): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * @return void
     */
    protected function mount(): void
    {
        // TODO: Implement mount() method.
    }
}
