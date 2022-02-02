<?php

namespace LteAdmin\Components;

use LteAdmin\Traits\FontAwesome;

class StatisticPeriodComponent extends Component
{
    use FontAwesome;

    /**
     * @var string
     */
    protected $class = 'row';
    protected $model;
    protected $entity;
    protected $icon = 'fas fa-lightbulb';

    public function title(string $nameOfSubject = null)
    {
        $this->entity = __($nameOfSubject);

        return $this;
    }

    public function forToday()
    {
        $this->column()
            ->info_box(
                __('lte.statistic_for_today', ['entity' => $this->entity]),
                $this->model->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count().' ',
                $this->icon
            )->successType();

        return $this;
    }

    public function perWeek()
    {
        $this->column()
            ->info_box(
                __('lte.statistic_per_week', ['entity' => $this->entity]),
                $this->model->whereBetween('created_at',
                    [now()->subWeek()->startOfDay(), now()->endOfDay()])->count().' ',
                $this->icon
            )->infoType();

        return $this;
    }

    public function perYear()
    {
        $this->column()
            ->info_box(
                __('lte.statistic_per_year', ['entity' => $this->entity]),
                $this->model->whereBetween('created_at',
                    [now()->subYear()->startOfDay(), now()->endOfDay()])->count().' ',
                $this->icon
            )->warningType();

        return $this;
    }

    public function total()
    {
        $this->column()
            ->info_box(
                __('lte.statistic_total', ['entity' => mb_strtolower($this->entity)]),
                $this->model->count().' ',
                $this->icon
            )->primaryType();

        return $this;
    }

    /**
     * @param  string  $icon
     * @return $this
     */
    public function icon(string $icon)
    {
        $this->icon = $icon;

        return $this;
    }

    protected function mount()
    {
        // TODO: Implement mount() method.
    }
}
