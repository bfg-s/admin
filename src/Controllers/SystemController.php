<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Components\ChartJsComponent;
use Admin\Components\Inputs\SelectInput;
use Admin\Components\LiveComponent;
use Admin\Components\LoadContentComponent;
use Admin\Components\ModalComponent;
use Admin\Core\PrepareExport;
use Admin\Middlewares\Authenticate;
use Admin\Requests\CallCallbackRequest;
use Admin\Requests\CustomSaveRequest;
use Admin\Requests\ExportExcelRequest;
use Admin\Requests\LoadChartJsRequest;
use Admin\Requests\LoadContentRequest;
use Admin\Requests\LoadSelect2Request;
use Admin\Requests\NestableSaveRequest;
use Admin\Requests\RealtimeRequest;
use Admin\Requests\SaveDashboardRequest;
use Admin\Requests\SaveImageOrderRequest;
use Admin\Requests\TableActionRequest;
use Admin\Requests\TranslateRequest;
use Admin\Respond;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

/**
 * System controller admin panel for processing all requests under the hood.
 */
class SystemController extends Controller
{
    /**
     * Callbacks of all events that the admin panel uses. Click, double click, hover.
     *
     * @var array
     */
    public static array $componentEventCallbacks = [

    ];

    /**
     * The list of realtime components for update.
     *
     * @var \Admin\Components\Component[]
     */
    public static array $realtimeComponents = [

    ];

    /**
     * Accounting iteration to handle nesting and sorting component data persistence.
     *
     * @var int
     */
    protected static int $iteration = 0;

    /**
     * Flag for checking the is referer request.
     *
     * @var bool
     */
    public static bool $isReferer = false;

    /**
     * Simulate the processing of the previous page in order to build the page anew and select the necessary data.
     *
     * @param  bool  $isReferer
     * @return void
     */
    protected function refererEmit(bool $isReferer = true): void
    {
        Authenticate::$noLog = true;

        static::$isReferer = $isReferer;

        $refUrl = Request::server('HTTP_REFERER');

        $page = Route::dispatch(
            Request::create(
                $refUrl
            )
        );

        $content = $page->getContent();

        if ($page->getStatusCode() == 500) {

            echo $content; die;
        }
    }

    /**
     * Endpoint for loading live parts of the page.
     *
     * @return array
     * @throws Throwable
     */
    public function load_lives(): array
    {
        $this->refererEmit();

        $result_areas = [];

        foreach (LiveComponent::$list as $area => $item) {
            $content = $item->render();

            $pattern = '/<div[^>]*>(.*)<\/div>\s*<\/div>/s';

            preg_match($pattern, $content, $matches);

            $contentInsideDiv = $matches[1] ?? '';

            $result_areas[$area] = [
                'hash' => sha1($contentInsideDiv),
                'content' => $contentInsideDiv,
            ];
        }

        return $result_areas;
    }

    /**
     * Endpoint for loading modal windows.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|mixed|void
     * @throws Throwable
     */
    public function load_modal(\Illuminate\Http\Request $request)
    {
        if (!check_referer()) {
            return [];
        }

        $this->refererEmit();

        $modalName = request('_modal');

        $modal = ModalComponent::$list[$modalName] ?? null;

        if ($modal) {
            if ($request->has('_modal_submit')) {
                return $modal->submitEvent ? app()->call($modal->submitEvent) : [];
            }

            admin_log_warning('Load modal', $modalName, 'far fa-window-restore');

            return [
                'size' => $modal->size,
                'backdrop' => $modal->backdrop,
                'temporary' => $modal->temporary,
                'content' => $modal->getRenderedView(),
            ];
        }

        abort(404);
    }

    /**
     * Endpoint for exporting data to Excel.
     *
     * @param  ExportExcelRequest  $request
     * @return array|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export_excel(ExportExcelRequest $request): BinaryFileResponse|array
    {
        if (!check_referer()) {
            return [];
        }

        $this->refererEmit();

        $prepared = new PrepareExport($request->model, $request->ids ?: [], $request->order, $request->order_type,
            $request->table);

        $fileName = class_basename($request->model).'_'.now()->format('Y_m_d_His').'.xlsx';

        admin_log_warning('Export', "To [$fileName] excel", 'far fa-file-excel');

        return Excel::download($prepared, $fileName);
    }

    /**
     * Endpoint for exporting data to csv.
     *
     * @param  ExportExcelRequest  $request
     * @return array|BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export_csv(ExportExcelRequest $request): BinaryFileResponse|array
    {
        if (!check_referer()) {
            return [];
        }

        $this->refererEmit();

        $prepared = new PrepareExport($request->model, $request->ids ?: [], $request->order, $request->order_type,
            $request->table);

        $fileName = class_basename($request->model).'_'.now()->format('Y_m_d_His').'.csv';

        admin_log_warning('Export', "To [$fileName] csv", 'fas fa-file-csv');

        return Excel::download($prepared, $fileName);
    }

    /**
     * Endpoint for switching the dark theme of the admin panel.
     *
     * @param  Respond  $respond
     * @return Respond
     */
    public function toggle_dark(Respond $respond): Respond
    {
        $status = (int) !admin_repo()->isDarkMode;
        Cookie::queue(
            'admin-dark-mode', $status, time() * 2
        );
        $respond->reboot();

        if ($status) {
            admin_log_warning('Theme', "Switch on dark mode", 'fas fa-adjust');
        } else {
            admin_log_warning('Theme', "Switch on light mode", 'fas fa-sun');
        }

        return $respond;
    }

    /**
     * Endpoint for saving custom data, for custom inputs that can be embedded in a table, for example.
     *
     * @param  CustomSaveRequest  $request
     * @param  Respond  $respond
     * @return Respond|array
     */
    public function custom_save(CustomSaveRequest $request, Respond $respond): Respond|array
    {
        if (!check_referer('PUT')) {
            return [];
        }

        /** @var Model $find */
        if (
            $request->model
            && class_exists($request->model)
            && $request->id
            && $request->field_name
            && ($find = $request->model::find($request->id))
        ) {
            $oldValue = $find->{$request->field_name};

            $find->{$request->field_name} = $request->val;

            if ($find->save()) {
                admin_log_warning('Save custom field', "[$request->field_name] from [$oldValue] to [$request->val]",
                    'fas fa-save');
                $respond->put('alert::success', __('admin.saved_successfully'))->reload();
            } else {
                admin_log_danger('Decline custom save field',
                    "[$request->field_name] from [$oldValue] to [$request->val]", 'fas fa-save');
                $respond->put('alert::error', __('admin.unknown_error'));
            }
        }

        return $respond;
    }

    /**
     * Endpoint for calling the callback of a click, double-click or hover event.
     *
     * @param  CallCallbackRequest  $request
     * @param  Respond  $respond
     * @return Respond|array
     */
    public function call_callback(CallCallbackRequest $request, Respond $respond): Respond|array
    {
        if (!check_referer()) {
            return [];
        }

        $this->refererEmit();

        if (isset(static::$componentEventCallbacks[$request->key])) {
            admin_log_warning('Call callback', (string) $request->key, 'fas fa-balance-scale-right');

            app()->call(static::$componentEventCallbacks[$request->key], $request->parameters);
        } else {
            $respond->toast_error(__('admin.callback_not_found'));
        }

        return $respond;
    }

    /**
     * Endpoint for mass deletion of records using table actions.
     *
     * @param  TableActionRequest  $request
     * @param  Respond  $respond
     * @return Respond|array
     */
    public function mass_delete(TableActionRequest $request, Respond $respond): Respond|array
    {
        if (!check_referer('DELETE')) {
            return [];
        }

        if (class_exists($request->class) && method_exists($request->class, 'delete')) {
            $success = false;
            foreach ($request->class::whereIn('id', $request->ids)->get() as $item) {
                if ($item->delete()) {
                    $success = true;
                } else {
                    $success = false;
                }
            }
            if ($success) {
                admin_log_warning('Delete', "[$request->class] for ids [".json_encode($request->ids)."]",
                    'fas fa-trash-alt');
                $respond->put('alert::success', __('admin.successfully_deleted'))->reload();
            } else {
                admin_log_danger('Error delete', "[$request->class] for ids [".json_encode($request->ids)."]",
                    'fas fa-trash-alt');
                $respond->put('alert::error', __('admin.unknown_error'));
            }
        } else {
            $respond->put('alert::error', __('admin.unknown_error'));
        }

        return $respond;
    }

    /**
     * Endpoint for saving data from nesting and sorting components.
     *
     * @param  NestableSaveRequest  $request
     * @param  Respond  $respond
     * @return Respond|array
     * @throws Throwable
     */
    public function nestable_save(
        NestableSaveRequest $request,
        Respond $respond,
    ): Respond|array {
        if (!check_referer('PUT')) {
            return [];
        }

        if (class_exists($request->model)) {
            DB::transaction(function () use ($request) {
                foreach (
                    $this->nestable_collapse($request->data, $request->depth, $request->parent_field, null,
                        $request->order_field) as $item
                ) {
                    if ($model = $request->model::where('id', $item['id'])->first()) {
                        $model->update($item['data']);
                    }
                }
            });
            admin_log_warning('Nested save', "For [$request->model]", 'fas fa-network-wired');
            static::$iteration = 0;
            $respond->toast_success(__('admin.saved_successfully'));
        }

        return $respond;
    }

    /**
     * A helper method for handling nested data for the nesting and sorting component.
     *
     * @param  array  $data
     * @param  int|string  $depth
     * @param  string|null  $parent_field
     * @param  null  $parent
     * @param  string  $order_field
     * @return array
     */
    private function nestable_collapse(
        array $data,
        int|string $depth,
        string $parent_field = null,
        $parent = null,
        string $order_field = 'order'
    ): array {
        $result = [];

        foreach ($data as $item) {
            $new = [];

            $new['id'] = $item['id'];

            if ($depth > 1) {
                $new['data'][$parent_field ?? 'parent_id'] = $parent;
            }

            $new['data'][$order_field ?? 'order'] = static::$iteration;

            $result[] = $new;

            static::$iteration++;

            if (isset($item['children'])) {
                $result = array_merge(
                    $result,
                    $this->nestable_collapse($item['children'], $depth, $parent_field, $item['id'], $order_field)
                );
            }
        }

        return $result;
    }

    /**
     * Endpoint for downloading graph data.
     *
     * @param  \Admin\Requests\LoadChartJsRequest  $request
     * @return array
     */
    public function load_chart_js(LoadChartJsRequest $request): array
    {
        $this->refererEmit();

        if (isset(ChartJsComponent::$loadCallBacks[$request->name])) {
            call_user_func(...ChartJsComponent::$loadCallBacks[$request->name]);

            return ChartJsComponent::$loadCallBacks[$request->name][1]->getViewData();
        }

        return [];
    }

    /**
     * Endpoint for loading select2 data.
     *
     * @param  \Admin\Requests\LoadSelect2Request  $request
     * @return array
     */
    public function load_select2(LoadSelect2Request $request): string|array
    {
        $this->refererEmit();

        if (isset(SelectInput::$loadCallBacks[$request->_select2_name])) {
            return SelectInput::$loadCallBacks[$request->_select2_name]->toJson(JSON_UNESCAPED_UNICODE);
        }

        return [];
    }

    /**
     * Realtime endpoint for updating the component.
     *
     * @param  \Admin\Requests\RealtimeRequest  $request
     * @return \Illuminate\Http\JsonResponse|string|null
     */
    public function realtime(RealtimeRequest $request): \Illuminate\Http\JsonResponse|string|null
    {
        $this->refererEmit(false);

        $result = [];

        foreach ($request->names as $name) {

            $component = static::$realtimeComponents[$name] ?? null;

            if ($component) {

                $result[$name] = $component->getRenderedView();
            }
        }

        if ($result) {

            return response()->json($result);
        }
//dd(array_keys(static::$realtimeComponents));
        return response()->json([
            'status' => 'fail',
            'exists' => array_keys(static::$realtimeComponents),
        ]);
    }

    /**
     * Action for loading content in the admin panel.
     *
     * @param  \Admin\Requests\LoadContentRequest  $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function loadContent(LoadContentRequest $request): \Illuminate\Http\JsonResponse
    {
        $this->refererEmit(false);

        if (isset(LoadContentComponent::$componentsForLoad[$request->name])) {
            /** @var LoadContentComponent $component */
            $component = LoadContentComponent::$componentsForLoad[$request->name];
            $component->resetContent();
            $component->contentOnly();
            $component->useCallBack();
            return response()->json([
                'content' => $component->render(),
            ]);
        }

        return response()->json([
            'status' => 'fail',
        ]);
    }

    /**
     * Endpoint for text translation.
     *
     * @param  TranslateRequest  $request
     * @return string|null
     * @throws LargeTextException
     * @throws RateLimitException
     * @throws TranslationRequestException
     */
    public function translate(TranslateRequest $request): ?string
    {
        $tr = new GoogleTranslate();

        return $tr->setTarget($request->toLang == 'ua' ? 'uk' : $request->toLang)->translate($request->data);
    }

    /**
     * Endpoint for maintaining the order of images in the image component in multi mode.
     *
     * @param  SaveImageOrderRequest  $request
     * @param  Respond  $respond
     * @return Respond
     */
    public function saveImageOrder(SaveImageOrderRequest $request, Respond $respond): Respond
    {
        $model = $request->model::find($request->id);

        if ($model) {
            $model->update([
                $request->field => $request->fileList
            ]);

            $respond->toast_success(__('admin.success'));
        }

        return $respond;
    }

    /**
     * Endpoint stub for "deleting an image".
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteOrderedImage(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['success' => true]);
    }

    public function saveDashboard(SaveDashboardRequest $request, Respond $respond)
    {
        $data = $request->validated();
        /** @var \Admin\Models\AdminDashboard $dashboard */
        $dashboard = admin()->dashboards()->find($data['dashboard_id']);
        if ($dashboard) {
            $dashboard->rows()->delete();
            foreach ($data['lines'] ?? [] as $order => $line) {
                $dashboard->rows()->create([
                    'admin_user_id' => admin()->id,
                    'order' => $order,
                    'widgets' => $line,
                ]);
            }
            return $respond->toast_success('Dashboard saved')->reload();
        }
        return $respond->toast_error('Dashboard not found');
    }
}
