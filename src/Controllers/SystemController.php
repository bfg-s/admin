<?php

namespace Admin\Controllers;

use Admin\Components\ChartJsComponent;
use Admin\Components\LiveComponent;
use Admin\Components\ModalComponent;
use Admin\Components\ModelTableComponent;
use Admin\Core\PrepareExport;
use Admin\Middlewares\BrowserDetectMiddleware;
use Admin\Requests\CalendarEventRequest;
use Admin\Requests\CallCallbackRequest;
use Admin\Requests\CustomSaveRequest;
use Admin\Requests\DropEventTemplateRequest;
use Admin\Requests\ExportExcelRequest;
use Admin\Requests\LoadChartJsRequest;
use Admin\Requests\NestableSaveRequest;
use Admin\Requests\NewEventTemplateRequest;
use Admin\Requests\NotificationSettingsRequest;
use Admin\Requests\TableActionRequest;
use Admin\Requests\TranslateRequest;
use Admin\Respond;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
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

            $content = $item->render()->render();
            $result_areas[$area] = [
                'hash' => sha1($content),
                'content' => $content,
            ];
        }

        return $result_areas;
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
                'content' => $modal->getRenderedView()->render(),
            ];
        }

        abort(404);
    }

    /**
     * @param  ExportExcelRequest  $request
     * @return array|BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export_excel(ExportExcelRequest $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|array
    {
        if (!check_referer()) {
            return [];
        }

        ModelTableComponent::$is_export = true;

        $this->refererEmit();

        $prepared = new PrepareExport($request->model, $request->ids, $request->order, $request->order_type, $request->table);

        $fileName = class_basename($request->model) . '_' . now()->format('Y_m_d_His').'.xlsx';

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

        $prepared = new PrepareExport($request->model, $request->ids, $request->order, $request->order_type, $request->table);

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
                admin_log_warning('Save custom field', "[$request->field_name] from [$oldValue] to [$request->val]", 'fas fa-save');
                $respond->put('alert::success', __('admin.saved_successfully'))->reload();
            } else {
                admin_log_danger('Decline custom save field', "[$request->field_name] from [$oldValue] to [$request->val]", 'fas fa-save');
                $respond->put('alert::error', __('admin.unknown_error'));
            }
        }

        return $respond;
    }

    /**
     * @param  int  $key
     * @param  array  $parameters
     * @return array|void
     */
    public function call_callback(CallCallbackRequest $request, Respond $respond)
    {
        if (!check_referer()) {
            return [];
        }

        $this->refererEmit();

        if (isset(static::$callbacks[$request->key])) {

            admin_log_warning('Call callback', $request->key, 'fas fa-balance-scale-right');

            app()->call(static::$callbacks[$request->key], $request->parameters);
        } else {
            $respond->toast_error(__('admin.callback_not_found'));
        }

        return $respond;
    }

    /**
     * @param  string  $class
     * @param  array  $ids
     * @return array
     * @throws Exception
     */
    public function mass_delete(TableActionRequest $request, Respond $respond)
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
                admin_log_warning('Delete', "[$request->class] for ids [".json_encode($request->ids)."]", 'fas fa-trash-alt');
                $respond->put('alert::success', __('admin.successfully_deleted'))->reload();
            } else {
                admin_log_danger('Error delete', "[$request->class] for ids [".json_encode($request->ids)."]", 'fas fa-trash-alt');
                $respond->put('alert::error', __('admin.unknown_error'));
            }
        } else {
            $respond->put('alert::error', __('admin.unknown_error'));
        }

        return $respond;
    }

    /**
     * @param  string  $model
     * @param  int  $depth
     * @param  array  $data
     * @param  string|null  $parent_field
     * @param  string  $order_field
     * @return array
     * @throws Throwable
     */
    public function nestable_save(
        NestableSaveRequest $request,
        Respond $respond,
    ) {
        if (!check_referer('PUT')) {
            return [];
        }

        if (class_exists($request->model)) {
            DB::transaction(function () use ($request) {
                foreach ($this->nestable_collapse($request->data, $request->depth, $request->parent_field, null, $request->order_field) as $item) {

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
     * @param  int  $depth
     * @param  string|null  $parent_field
     * @param  null  $parent
     * @param  string  $order_field
     * @return array
     */
    private function nestable_collapse(
        array $data,
        int $depth,
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

    /**
     * @param  TranslateRequest  $request
     * @return string|null
     * @throws \Stichoza\GoogleTranslate\Exceptions\LargeTextException
     * @throws \Stichoza\GoogleTranslate\Exceptions\RateLimitException
     * @throws \Stichoza\GoogleTranslate\Exceptions\TranslationRequestException
     */
    public function translate(TranslateRequest $request): ?string
    {
        $tr = new GoogleTranslate();

        return $tr->setTarget($request->toLang == 'ua' ? 'uk' : $request->toLang)->translate($request->data);
    }

    /**
     * @param  NotificationSettingsRequest  $request
     * @return void
     */
    public function updateNotificationBrowserSettings(NotificationSettingsRequest $request): void
    {
        if (BrowserDetectMiddleware::$browser) {

            BrowserDetectMiddleware::$browser->update([
                'notification_settings' => $request->settings
            ]);
        }
    }

    /**
     * @param  NewEventTemplateRequest  $request
     * @return Model
     */
    public function addNewEventTemplate(NewEventTemplateRequest $request): Model
    {
        return admin()->eventsTemplates()->create([
            'name' => $request->name,
            'color' => $request->color,
        ]);
    }

    /**
     * @param  DropEventTemplateRequest  $request
     * @return int|null
     */
    public function dropEventTemplate(DropEventTemplateRequest $request): ?int
    {
        return admin()->eventsTemplates()->find($request->id)?->delete();
    }

    public function calendarData()
    {
        return admin()->events;
    }

    public function calendarEvent(CalendarEventRequest $request)
    {
        $data = [];

        if ($request->title) {
            $data['title'] = $request->title;
        }

        if ($request->description) {
            $data['description'] = $request->description == 'null' ? null : $request->description;
        }

        if ($request->url) {
            $data['url'] = $request->url == 'null' ? null : $request->url;
        }

        $start = Carbon::parse($request->start);
        $end = $request->end
            ? Carbon::parse($request->end)
            : ($start->hour ? Carbon::parse($request->start)->addHour() : Carbon::parse($request->start));

//        if ($end->hour === 0) {
//            $end = $end->endOfDay();
//        }

        $data['start'] = Carbon::parse($start)->setTimezone('UTC');
        $data['end'] = Carbon::parse($end)->setTimezone('UTC');

        if ($request->id) {
            $result = admin()->events()->find($request->id);
            $result?->update($data);
            return $result;
        } else {
            return admin()->events()->create($data);
        }
    }

    /**
     * @param  DropEventTemplateRequest  $request
     * @return int|null
     */
    public function dropEvent(DropEventTemplateRequest $request): ?int
    {
        return admin()->events()->find($request->id)?->delete();
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

        Route::dispatch(
            Request::create(
                $refUrl
            )
        )->getContent();
    }
}
