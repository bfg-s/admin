<?php

namespace Admin\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Admin\Delegates\Card;
use Admin\Delegates\Column;
use Admin\Delegates\Form;
use Admin\Delegates\ModelInfoTable;
use Admin\Delegates\ModelTable;
use Admin\Delegates\SearchForm;
use Admin\Models\AdminMenu;
use Admin\Page;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Finder\SplFileInfo;

class MenuController extends Controller
{
    /**
     * @var string
     */
    public static $model = AdminMenu::class;

    /**
     * @param  Page  $page
     * @param  Column  $column
     * @param  Card  $card
     * @param  SearchForm  $searchForm
     * @param  ModelTable  $modelTable
     * @return Page
     */
    public function index(
        Page $page,
        Column $column,
        Card $card,
        SearchForm $searchForm,
        ModelTable $modelTable
    ) {
        return $page
            ->column(
                $this->columnOfSort(),
            )
            ->column(
                $column->num(7)->card(
                    $card->defaultTools(),
                    $card->title('admin.admin_menu_title'),
                    $card->search_form(
                        $searchForm->id(),
                        $searchForm->in_input_name,
                        $searchForm->at(),
                    ),
                    $card->model_table(
                        $modelTable->orderBy('order'),
                        $modelTable->id(),
                        $modelTable->col('admin.menu_icon', 'icon')->sort->fa_icon,
                        $modelTable->col_name('admin.menu_name')->sort,
                        $modelTable->col('admin.menu_type', 'type')->sort->badge('primary'),
                        $modelTable->col('admin.active', 'active')->sort->input_switcher,
                        $modelTable->perPage(50),
                    ),
                ),
            );
    }

    protected function columnOfSort()
    {
        return $this->column->num(5)->card(
            $this->card->nestedTools(),
            $this->card->title('admin.admin_menu_sort_title'),
            $this->card->nested()
        );
    }

    /**
     * @param  Page  $page
     * @param  Column  $column
     * @param  Card  $card
     * @param  Form  $form
     * @return Page
     */
    public function matrix(
        Page $page,
        Column $column,
        Card $card,
        Form $form,
    ) {
        return $page
            ->column(
                $this->columnOfSort(),
            )
            ->column(
                $column->num(7)->card(
                    $card->defaultTools(),
                    $card->title('admin.admin_menu_title'),
                    $card->form(
                        $form->vertical(),
                        $form->icon('icon', 'admin.menu_icon')->required(),
                        $form->input_name('admin.menu_name')->required(),
                        $form->input('route', 'admin.menu_route')->required(),
                        $form->select('type', 'admin.menu_type')
                            ->options([
                                'item' => __('admin.menu_item_type'),
                                'resource' => __('admin.menu_resource_type'),
                                'group' => __('admin.menu_group_type')
                            ])
                            ->required(),
                        $form->watch(
                            $this->isRequest('type', 'item'),
                            $form->autocomplete('action', 'admin.menu_action')
                                ->options($this->actions())->vertical()->nullable(),
                        ),
                        $form->watch(
                            $this->isRequest('type', 'resource'),
                            $form->autocomplete('action', 'admin.menu_action_resource')
                                ->options($this->actions(false))->vertical()->nullable(),
                            $form->multi_select('except[]', 'admin.menu_except')->options([
                                'edit' => __('admin.menu_except_edit'),
                                'create' => __('admin.menu_except_create'),
                                'destroy' => __('admin.menu_except_destroy'),
                                'show' => __('admin.menu_except_show'),
                            ])->vertical()->nullable(),
                        ),
                        $form->switcher('active', 'admin.active'),
                    ),
                    $card->footer_form(),
                ),
            );
    }

    protected function actions(bool $withMethods = true)
    {
        $controllersFolder = admin_app_path('Controllers');

        if (is_dir($controllersFolder)) {
            $files = collect(File::allFiles($controllersFolder));

            $parentMethods = collect((new ReflectionClass(Controller::class))
                ->getMethods(ReflectionMethod::IS_PUBLIC))->pluck('name', 'name')->toArray();

            $classes = $files
                ->filter(fn(SplFileInfo $info) => Str::is('*.php', $info->getPathname()))
                ->map(fn(SplFileInfo $info) => class_in_file($info->getPathname()))
                ->filter(fn($className) => $className !== 'App\\Admin\\Controllers\\Controller');

            if (!$withMethods) {
                return $classes->mapWithKeys(fn($s) => [$s => $s]);
            }

            return $classes->map(function (string $className) use ($parentMethods) {
                $class = new ReflectionClass($className);
                $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);
                $classActions = [];
                foreach ($methods as $method) {
                    if (!isset($parentMethods[$method->getName()])) {
                        $val = "$className@".$method->getName();
                        $classActions[$val] = $val;
                    }
                }
                return $classActions;
            })
                ->filter()
                ->collapse()
                ->toArray();
        }

        return [];
    }

    /**
     * @param  Page  $page
     * @param  Column  $column
     * @param  Card  $card
     * @param  ModelInfoTable  $modelInfoTable
     * @return Page
     */
    public function show(
        Page $page,
        Column $column,
        Card $card,
        ModelInfoTable $modelInfoTable,
    ) {
        return $page
            ->column(
                $this->columnOfSort(),
            )
            ->column(
                $column->num(7)->card(
                    $card->defaultTools(),
                    $card->model_info_table(
                        $modelInfoTable->id(),
                        $modelInfoTable->row('admin.menu_name', 'name'),
                        $modelInfoTable->row('admin.menu_icon', 'icon')->fa_icon,
                        $modelInfoTable->row('admin.menu_route', 'route'),
                        $modelInfoTable->row('admin.menu_type', 'type'),
                        $modelInfoTable->row('admin.active', 'active')->input_switcher,
                        $modelInfoTable->at(),
                    )
                )
            );
    }
}
