<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Lar\Layout\Tags\DIV;
use Lar\LteAdmin\Controllers\Generators\DashboardGenerator;
use Lar\LteAdmin\Segments\Container;
use Lar\LteAdmin\Segments\Tagable\Cores\ChartJsBuilder;

/**
 * Class DashboardController.
 *
 * @package Lar\LteAdmin\Controllers
 */
class DashboardController extends Controller
{
    /**
     * @return Container
     */
    public function index()
    {
        return Container::create(function (DashboardGenerator $generator, DIV $div) {
            $div->statistic_periods(config('auth.providers.users.model'))
                ->icon_users()
                ->forToday()
                ->perWeek()
                ->perYear()
                ->total();

            $div->card()->fullBody()->chart_js(function (ChartJsBuilder $builder) {
                /** @var Collection $all */
                $all = config('auth.providers.users.model')::get(['created_at']);
                $all = $all->groupBy(function (Model $model) {
                    return $model->created_at->format('Y.m.d');
                })->map(function (Collection $collection) {
                    return $collection->count();
                });

                $builder
                    ->type('line')
                    ->size(['width' => 400, 'height' => 100])
                    ->labels($all->keys()->toArray())
                    ->simpleDatasets(
                        __('lte.added_to_users'),
                        $all->values()->toArray()
                    );
            });

            if (admin()->isRoot()) {
                $generator->aboutServer();
            }
        });
    }
}
