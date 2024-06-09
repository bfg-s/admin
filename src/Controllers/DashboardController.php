<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin;
use Admin\Delegates\Card;
use Admin\Delegates\CardBody;
use Admin\Delegates\ChartJs;
use Admin\Delegates\Column;
use Admin\Delegates\Row;
use Admin\Delegates\SearchForm;
use Admin\Delegates\StatisticPeriod;
use Admin\Page;
use Bfg\Attributes\Attributes;
use Illuminate\Http\Request;
use ReflectionClass;

/**
 * Admin panel controller for the dashboard page.
 */
class DashboardController extends Controller
{
    /**
     * A list of widgets for the dashboard.
     *
     * @var array
     */
    protected static array $widgets = [
        \Admin\Widgets\PeriodStatisticWidget::class,
        \Admin\Widgets\ChartStatisticWidget::class,
        \Admin\Widgets\AdministratorBrowserStatisticWidget::class,
        \Admin\Widgets\ActivityStatisticWidget::class,
        \Admin\Widgets\EnvironmentsWidget::class,
        \Admin\Widgets\LaravelInfoWidget::class,
        \Admin\Widgets\ComposerInfoWidget::class,
        \Admin\Widgets\DatabaseInfoWidget::class,
    ];

    /**
     * Add new widget for the dashboard.
     *
     * @param  string  $class
     * @return void
     */
    public static function addWidget(string $class): void
    {
        static::$widgets[] = $class;
    }

    /**
     * Create a new dashboard modal handler.
     *
     * @param  \Admin\Respond  $respond
     * @param  \Illuminate\Http\Request  $request
     * @return \Admin\Respond
     */
    public function addDashboard(Admin\Respond $respond, Request $request): Admin\Respond
    {
        $name = $request->name;

        if (! $name) {

            return $respond->toast_error(__('admin.dashboard_name_is_required'));
        }

        admin()->dashboards()->create(['name' => $name]);

        return $respond->toast_success(__('admin.dashboard_added'))->reload();
    }

    /**
     * The controller index to process the page.
     *
     * @param  Page  $page
     * @param  Row  $row
     * @param  Column  $column
     * @param  \Admin\Delegates\Modal  $modal
     * @param  \Admin\Delegates\Buttons  $buttons
     * @param  \Admin\Delegates\Tab  $tab
     * @param  \Admin\Delegates\Form  $form
     * @return Page
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function index(
        Page $page,
        Admin\Delegates\Row $row,
        Admin\Delegates\Column $column,
        Admin\Delegates\Modal $modal,
        Admin\Delegates\Buttons $buttons,
        Admin\Delegates\Tab $tab,
        Admin\Delegates\Form $form,
    ): Page {

        return $page
            ->modal(
                $modal->name('add_dashboard')
                    ->title('admin.add_new_dashboard'),
                $modal->submitEvent([$this, 'addDashboard']),
                $modal->form(
                    $form->input('name', 'admin.name')
                ),
                $modal->buttons()
                    ->success()
                    ->icon_plus()
                    ->title(__('admin.create'))
                    ->modalSubmit(),
            )
            ->modal(
                $modal->name('dashboard_configuration')
                    ->title('admin.dashboard_settings')->sizeExtra()->closable(),
                $modal->use(function (Admin\Components\ModalBodyComponent $modalComponent) {

                    $widgets = [];
                    /** @var \Admin\Models\AdminDashboard $dashboard */
                    $dashboard = admin()->dashboards()->find($this->request('dashboardId'));

                    foreach (static::$widgets as $widget) {
                        /** @var \Admin\Widgets\WidgetAbstract $widget */
                        $widget = $widget::create();
                        if ($widget->isHasAccess()) {
                            $widgets[] = $widget->toArray();
                        }
                    }

                    $models = Attributes::new()
                        ->wherePath(app_path('Models'))
                        ->classes()
                        ->map(function (ReflectionClass $class) {
                            return $class->getName();
                        });

                    $dashboardLines = $dashboard->rows->map(function (Admin\Models\AdminDashboardRow $row) {
                        $cols = [];
                        foreach ($row->widgets as $widget) {
                            /** @var \Admin\Widgets\WidgetAbstract $widgetClass */
                            $widgetClass = $widget['class'];
                            $widget = $widgetClass::create($widget);
                            $cols[] = $widget->toArray();
                        }
                        return $cols;
                    })->values()->toArray();

                    return $modalComponent->vue(Admin\Components\Vue\DashboardSettingsVue::class, [
                        'widgets_hash' => base64_encode(json_encode($widgets)),
                        'models_hash' => base64_encode(json_encode($models)),
                        'dashboard_hash' => base64_encode(json_encode($dashboard)),
                        'lines_hash' => base64_encode(json_encode($dashboardLines)),
                    ]);
                }),
            )
            ->buttons(
                $buttons->success('fas fa-plus')
                    ->title('admin.add_dashboard')
                    ->modal('add_dashboard'),
            )
            ->withCollection(admin()->dashboards, function (Admin\Models\AdminDashboard $dashboard) use ($page, $tab, $buttons, $row, $column) {
                return $page->tab(
                    $tab->title($dashboard->name)->horizontal(),
                    $tab->p(),
                    $tab->withCollection($dashboard->rows, function (Admin\Models\AdminDashboardRow $dashboardRow) use ($tab, $row, $column) {

                        return $tab->row()->withCollection($dashboardRow->widgets, function (array $widget) use ($row, $column, $dashboardRow) {
                            /** @var \Admin\Widgets\WidgetAbstract $widgetClass */
                            $widgetClass = $widget['class'];
                            $widget = $widgetClass::create($widget);
                            if ($widget->isHasAccess()) {
                                return $row->column($dashboardRow->cols)->displayFlex()->appEnd($widget->render());
                            }
                        });
                    }),
                    $tab->center()->buttons(
                        $buttons->success('fas fa-cogs')
                            ->title('admin.dashboard_settings')
                            ->modal('dashboard_configuration', ['dashboardId' => $dashboard->id]),
                        $buttons->danger('fas fa-trash')
                            ->model($dashboard)
                            ->title('admin.dashboard_delete')
                            ->click(function ($id, Admin\Respond $respond) {
                                $dashboard = admin()->dashboards()->find($id);
                                if ($dashboard?->delete()) {
                                    return $respond->toast_success('admin.dashboard_deleted')->reload();
                                }
                                return $respond->toast_error('admin.error_deleting_dashboard');
                            }, ['id'], 'admin.are_you_sure_you_want_to_delete_this_dashboard'),
                    ),
                    $tab->p(),
                );
            });
    }
}
