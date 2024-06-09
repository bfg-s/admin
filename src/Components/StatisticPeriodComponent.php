<?php

declare(strict_types=1);

namespace Admin\Components;

use Admin\Traits\FontAwesomeTrait;

/**
 * Period statistics component of the admin panel.
 */
class StatisticPeriodComponent extends Component
{
    use FontAwesomeTrait;

    /**
     * The name of the component template.
     *
     * @var string
     */
    protected string $view = 'statistic-period';

    /**
     * The name of the entity (model) that is displayed as statistics.
     *
     * @var mixed
     */
    protected mixed $entity = null;

    /**
     * Statistics boxes icon.
     *
     * @var string|null
     */
    protected ?string $icon = 'fas fa-lightbulb';

    /**
     * Realtime marker, if enabled, the component will be updated at the specified frequency.
     *
     * @var bool
     */
    protected bool $realTime = true;

    /**
     * Show the statistics for today.
     *
     * @var bool
     */
    protected bool $forToday = false;

    /**
     * Show the statistics per week.
     *
     * @var bool
     */
    protected bool $perWeek = false;

    /**
     * Show the statistics per year.
     *
     * @var bool
     */
    protected bool $perYear = false;

    /**
     * Show the total statistics.
     *
     * @var bool
     */
    protected bool $total = false;

    /**
     * Set the entity (model) name as title.
     *
     * @param  string|null  $nameOfSubject
     * @return $this
     */
    public function title(string $nameOfSubject = null): static
    {
        $this->entity = __($nameOfSubject);

        return $this;
    }

    /**
     * Display statistics for today in the component.
     *
     * @return $this
     */
    public function forToday(): static
    {
        $this->forToday = true;

        return $this;
    }

    /**
     * Display statistics for the current week in the component.
     *
     * @return $this
     */
    public function perWeek(): static
    {
        $this->perWeek = true;

        return $this;
    }

    /**
     * Display statistics for the current year in the component.
     *
     * @return $this
     */
    public function perYear(): static
    {
        $this->perYear = true;

        return $this;
    }

    /**
     * Display statistics for all time in the component.
     *
     * @return $this
     */
    public function total(): static
    {
        $this->total = true;

        return $this;
    }

    /**
     * Set the statistics box icon.
     *
     * @param  string  $name
     * @return $this
     */
    public function icon(string $name): static
    {
        $this->icon = $name;

        return $this;
    }

    /**
     * Method for mounting components on the admin panel page.
     *
     * @return void
     */
    protected function mount(): void
    {
        $includedCount = 0;
        if ($this->forToday) $includedCount++;
        if ($this->perWeek) $includedCount++;
        if ($this->perYear) $includedCount++;
        if ($this->total) $includedCount++;

        $columns = 3;
        if ($includedCount === 1) $columns = 12;
        if ($includedCount === 2) $columns = 6;
        if ($includedCount === 3) $columns = 4;


        if ($this->forToday) {

            $this->column($columns)
                ->info_box(
                    __('admin.statistic_for_today', ['entity' => $this->entity]),
                    $this->model->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count().' ',
                    $this->icon
                )->withoutRealtime()->successType();
        }

        if ($this->perWeek) {

            $this->column($columns)
                ->info_box(
                    __('admin.statistic_per_week', ['entity' => $this->entity]),
                    $this->model->whereBetween(
                        'created_at',
                        [now()->subWeek()->startOfDay(), now()->endOfDay()]
                    )->count().' ',
                    $this->icon
                )->withoutRealtime()->infoType();
        }

        if ($this->perYear) {

            $this->column($columns)
                ->info_box(
                    __('admin.statistic_per_year', ['entity' => $this->entity]),
                    $this->model->whereBetween(
                        'created_at',
                        [now()->subYear()->startOfDay(), now()->endOfDay()]
                    )->count().' ',
                    $this->icon
                )->withoutRealtime()->warningType();
        }

        if ($this->total) {

            $this->column($columns)
                ->info_box(
                    __('admin.statistic_total', ['entity' => mb_strtolower($this->entity ?: '')]),
                    $this->model->count().' ',
                    $this->icon
                )->withoutRealtime()->primaryType();
        }
    }
}
