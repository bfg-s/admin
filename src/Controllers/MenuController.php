<?php

namespace LteAdmin\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LteAdmin\Delegates\Card;
use LteAdmin\Delegates\Column;
use LteAdmin\Delegates\Form;
use LteAdmin\Delegates\ModelInfoTable;
use LteAdmin\Delegates\ModelTable;
use LteAdmin\Delegates\SearchForm;
use LteAdmin\Models\LteMenu;
use LteAdmin\Models\LteRole;
use LteAdmin\Page;
use Symfony\Component\Finder\SplFileInfo;

class MenuController extends Controller
{
    /**
     * @var string
     */
    public static $model = LteMenu::class;

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
                    $card->title('lte.admin_menu_title'),
                    $card->search_form(
                        $searchForm->id(),
                        $searchForm->in_input_name,
                        $searchForm->at(),
                    ),
                    $card->model_table(
                        $modelTable->orderBy('order'),
                        $modelTable->id(),
                        $modelTable->col('lte.menu_icon', 'icon')->sort->fa_icon,
                        $modelTable->col_name('lte.menu_name')->sort,
                        $modelTable->col('lte.menu_type', 'type')->sort->badge('primary'),
                        $modelTable->col('lte.active', 'active')->sort->input_switcher,
                        $modelTable->perPage(50),
                    ),
                ),
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
                    $card->title('lte.admin_menu_title'),
                    $card->form(
                        $form->vertical(),
                        $form->icon('icon', 'lte.menu_icon')->required(),
                        $form->input_name('lte.menu_name')->required(),
                        $form->input('route', 'lte.menu_route')->required(),
                        $form->select('type', 'lte.menu_type')
                            ->options([
                                'item' => __('lte.menu_item_type'),
                                'resource' => __('lte.menu_resource_type'),
                                'group' => __('lte.menu_group_type')
                            ])
                            ->required(),
                        $form->watch(
                            $this->isRequest('type', 'item'),
                            $form->autocomplete('action', 'lte.menu_action')
                                ->options($this->actions())->vertical()->nullable(),
                        ),
                        $form->watch(
                            $this->isRequest('type', 'resource'),
                            $form->autocomplete('action', 'lte.menu_action_resource')
                                ->options($this->actions(false))->vertical()->nullable(),
                            $form->multi_select('except[]', 'lte.menu_except')->options([
                                'edit' => __('lte.menu_except_edit'),
                                'create' => __('lte.menu_except_create'),
                                'destroy' => __('lte.menu_except_destroy'),
                                'show' => __('lte.menu_except_show'),
                            ])->vertical()->nullable(),
                        ),
                        $form->switcher('active', 'lte.active'),
                    ),
                    $card->footer_form(),
                ),
            );
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
                        $modelInfoTable->row('lte.menu_name', 'name'),
                        $modelInfoTable->row('lte.menu_icon', 'icon')->fa_icon,
                        $modelInfoTable->row('lte.menu_route', 'route'),
                        $modelInfoTable->row('lte.menu_type', 'type'),
                        $modelInfoTable->row('lte.active', 'active')->input_switcher,
                        $modelInfoTable->at(),
                    )
                )
            );
    }

    protected function columnOfSort()
    {
        return $this->column->num(5)->card(
            $this->card->nestedTools(),
            $this->card->title('lte.admin_menu_sort_title'),
            $this->card->nested()
        );
    }

    protected function actions(bool $withMethods = true)
    {
        $controllersFolder = lte_app_path('Controllers');

        if (is_dir($controllersFolder)) {

            $files = collect(File::allFiles($controllersFolder));

            $parentMethods = collect((new \ReflectionClass(Controller::class))
                ->getMethods(\ReflectionMethod::IS_PUBLIC))->pluck('name', 'name')->toArray();

            $classes = $files
                ->filter(fn (SplFileInfo $info) => Str::is('*.php', $info->getPathname()))
                ->map(fn (SplFileInfo $info) => class_in_file($info->getPathname()))
                ->filter(fn ($className) => $className !== 'App\\Admin\\Controllers\\Controller');

            if (!$withMethods) {

                return $classes->mapWithKeys(fn ($s) => [$s => $s]);
            }

            return $classes->map(function (string $className) use ($parentMethods) {
                    $class = new \ReflectionClass($className);
                    $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
                    $classActions = [];
                    foreach ($methods as $method) {
                        if (!isset($parentMethods[$method->getName()])) {
                            $val = "$className@" . $method->getName();
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
}
