<?php

declare(strict_types=1);

namespace Admin\Controllers;

use Admin\Components\ChartJsComponent;
use Admin\Components\Inputs\SelectInput;
use Admin\Components\LiveComponent;
use Admin\Components\ModalComponent;
use Admin\Components\ModelTableComponent;
use Admin\Core\PrepareExport;
use Admin\Requests\CallCallbackRequest;
use Admin\Requests\CustomSaveRequest;
use Admin\Requests\ExportExcelRequest;
use Admin\Requests\LoadChartJsRequest;
use Admin\Requests\LoadSelect2Request;
use Admin\Requests\NestableSaveRequest;
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
use OpenSpout\Common\Exception\InvalidArgumentException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Common\Exception\UnsupportedTypeException;
use OpenSpout\Writer\Exception\WriterNotOpenedException;
use PhpOffice\PhpSpreadsheet\Exception;
use Stichoza\GoogleTranslate\Exceptions\LargeTextException;
use Stichoza\GoogleTranslate\Exceptions\RateLimitException;
use Stichoza\GoogleTranslate\Exceptions\TranslationRequestException;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class SystemController extends Controller
{
    /**
     * @var array
     */
    public static array $callbacks = [

    ];

    /**
     * @var int
     */
    protected static $i = 0;

    /**
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
     * @return void
     */
    protected function refererEmit(): void
    {
        $refUrl = str_replace(
            '/'.App::getLocale(), '/en',
            Request::server('HTTP_REFERER')
        );

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
     * @param  ExportExcelRequest  $request
     * @return mixed
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws Throwable
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function export_excel(ExportExcelRequest $request)
    {
        if (!check_referer()) {
            return [];
        }

        ModelTableComponent::$is_export = true;

        $this->refererEmit();

        $prepared = new PrepareExport($request->model, $request->ids ?: [], $request->order, $request->order_type,
            $request->table);

        $fileName = class_basename($request->model).'_'.now()->format('Y_m_d_His').'.xlsx';

        admin_log_warning('Export', "To [$fileName] excel", 'far fa-file-excel');

        return Excel::download($prepared, $fileName);
    }

    /**
     * @param  ExportExcelRequest  $request
     * @return array|BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export_csv(ExportExcelRequest $request)
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

        if (isset(static::$callbacks[$request->key])) {
            admin_log_warning('Call callback', (string) $request->key, 'fas fa-balance-scale-right');

            app()->call(static::$callbacks[$request->key], $request->parameters);
        } else {
            $respond->toast_error(__('admin.callback_not_found'));
        }

        return $respond;
    }

    /**
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
            static::$i = 0;
            $respond->toast_success(__('admin.saved_successfully'));
        }

        return $respond;
    }

    /**
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
    ) {
        $result = [];

        foreach ($data as $item) {
            $new = [];

            $new['id'] = $item['id'];

            if ($depth > 1) {
                $new['data'][$parent_field ?? 'parent_id'] = $parent;
            }

            $new['data'][$order_field ?? 'order'] = static::$i;

            $result[] = $new;

            static::$i++;

            if (isset($item['children'])) {
                $result = array_merge(
                    $result,
                    $this->nestable_collapse($item['children'], $depth, $parent_field, $item['id'], $order_field)
                );
            }
        }

        return $result;
    }

    public function load_chart_js(LoadChartJsRequest $request)
    {
        $this->refererEmit();

        if (isset(ChartJsComponent::$loadCallBacks[$request->name])) {
            call_user_func(...ChartJsComponent::$loadCallBacks[$request->name]);

            return ChartJsComponent::$loadCallBacks[$request->name][1]->getViewData();
        }

        return [];
    }

    public function load_select2(LoadSelect2Request $request)
    {
        $this->refererEmit();

        if (isset(SelectInput::$loadCallBacks[$request->_select2_name])) {
            return SelectInput::$loadCallBacks[$request->_select2_name]->toJson(JSON_UNESCAPED_UNICODE);
        }

        return [];
    }

    /**
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

    public function deleteOrderedImage()
    {
        return response()->json(['success' => true]);
    }
}
