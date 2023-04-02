<?php

namespace Admin\Controllers;

use Admin;
use Admin\Components\ModelTableComponent;
use Admin\Delegates\Card;
use Admin\Delegates\Form;
use Admin\Delegates\ModelTable;
use Admin\Delegates\SearchForm;
use Admin\Models\AdminSetting;
use Admin\Page;

class SettingsController extends Controller
{
    /**
     * @var string
     */
    public static $model = AdminSetting::class;

    /**
     * @var array|string[]
     */
    protected array $inputs = [
        'input' => 'Input',
        'textarea' => 'Textarea',
        'switcher' => 'Switcher',
        'email' => 'Email',
        'amount' => 'Amount',
        'ckeditor' => 'Ckeditor',
        'codemirror' => 'Codemirror',
        'color' => 'Color',
        'date' => 'Date',
        'date_range' => 'Date range',
        'date_time' => 'Date time',
        'date_time_range' => 'Date time range',
        'file' => 'File',
        'icon' => 'Icon',
        'image' => 'Image',
        'mdeditor' => 'MDeditor',
        'number' => 'Number',
        'numeric' => 'Numeric',
        'password' => 'Password',
        'rating' => 'Rating',
        'time' => 'Time',
    ];

    /**
     * @var array|string[]
     */
    protected array $groups = [
        'Admin' => 'Admin',
        'General' => 'General',
    ];

    public function defaultTools($type)
    {
        return !($type === 'info');
    }

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  SearchForm  $searchForm
     * @param  ModelTable  $modelTable
     * @return Page
     */
    public function index(Page $page, Card $card, SearchForm $searchForm, ModelTable $modelTable)
    {
        return $page->card(
            $card->title('admin.settings'),
            $card->search_form(
                $searchForm->id(),
                $searchForm->select('group', 'admin.group')->options($this->groups),
                $searchForm->input('title', 'admin.title'),
                $searchForm->select('type', 'admin.type')->options($this->inputs),
                $searchForm->at(),
            ),
            $card->model_table(
                $modelTable->id(),
                $modelTable->col('admin.group', 'group')->sort(),
                $modelTable->col('admin.title', 'title')->sort(),
                $modelTable->if(!admin()->isRoot())->col('admin.description', 'description')->sort(),
                $modelTable->if(admin()->isRoot())->col('admin.name', 'name')->sort()->copied(),
                $modelTable->if(admin()->isRoot())->col('admin.type', 'type')->sort()->input_select($this->inputs),
                $modelTable->col('Value', function (AdminSetting $settings) {
                    if ($settings->type == 'switcher') {
                        return ModelTableComponent::callExtension('input_switcher', [
                            'value' => $settings->value,
                            'model' => $settings,
                            'field' => 'value'
                        ]);
                    } else if ($settings->type == 'input') {
                        return ModelTableComponent::callExtension('input_editable', [
                            'value' => $settings->value,
                            'model' => $settings,
                            'field' => 'value',
                            'title' => 'Value',
                        ]);
                    } else if ($settings->type == 'textarea') {
                        return ModelTableComponent::callExtension('textarea_editable', [
                            'value' => $settings->value,
                            'model' => $settings,
                            'field' => 'value',
                            'title' => 'Value',
                        ]);
                    }
                    return $settings->value;
                })->sort('value'),
                $modelTable->controlInfo(false)
            ),
        );
    }

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  Form  $form
     * @return Page
     */
    public function matrix(Page $page, Card $card, Form $form)
    {
        return $page->card(
            $card->title(['admin.add_role', 'admin.edit_role']),
            $card->form(
                $form->ifEdit()->info_id(),
                $form->if(admin()->isRoot())->select('group', 'admin.group')->required()->options($this->groups),
                $form->if(admin()->isRoot())->input('title', 'admin.title')->required(),
                $form->if(admin()->isRoot())->select('type', 'admin.type')->required()->options($this->inputs),
                $form->if(admin()->isRoot())->input('name', 'admin.name')->required(), //->slugable(),
                $form->if($this->isType('edit'))->{$this->model()->type ?: 'input'}('value', 'admin.value')->info($this->model()->description),
                $form->if(admin()->isRoot())->textarea('description', 'admin.description')->required(),
                $form->ifEdit()->info_updated_at(),
                $form->ifEdit()->info_created_at(),
            ),
            $card->footer_form(),
        );
    }

    /**
     * @param  Page  $page
     * @param  Card  $card
     * @param  ModelInfoTable  $modelInfoTable
     * @return Page
     */
    public function show(Page $page, Card $card, ModelInfoTable $modelInfoTable)
    {
        return $page->card(
            $card->model_info_table(
                $modelInfoTable->id(),
                $modelInfoTable->row('admin.title', 'name'),
                $modelInfoTable->row('admin.slug', 'slug')->badge('success'),
                $modelInfoTable->at(),
            )
        );
    }
}
