<?php

namespace Admin\Controllers;

use Admin\Components\ModalComponent;
use Admin\Components\ModelTableComponent;
use Admin\Core\PrepareExport;
use Admin\Requests\CallCallbackRequest;
use Admin\Requests\CustomSaveRequest;
use Admin\Requests\ExportExcelRequest;
use Admin\Respond;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SystemController extends Controller
{
    /**
     * @var array
     */
    public static array $callbacks = [

    ];

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array|mixed|void
     * @throws \Throwable
     */
    public function load_modal(\Illuminate\Http\Request $request)
    {
        if (!check_referer()) {
            return [];
        }

        $this->refererEmit();

        $modal = ModalComponent::$list[request('_modal')] ?? null;

        if ($modal) {
            if ($request->has('_modal_submit')) {
                return $modal->submitEvent ? app()->call($modal->submitEvent) : [];
            }

            admin_log_warning('Call executing', "Load modal", 'fas fa-exchange-alt');

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

        return Excel::download($prepared, class_basename($request->model) . '_' . now()->format('Y_m_d_His').'.xlsx');
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

        return Excel::download($prepared, class_basename($request->model).'_'.now()->format('Y_m_d_His').'.csv');
    }

    /**
     * @param  Respond  $respond
     * @return Respond
     */
    public function toggle_dark(Respond $respond): Respond
    {
        Cookie::queue(
            'admin-dark-mode',
            (int) !admin_repo()->isDarkMode,
            time() * 2
        );
        $respond->reboot();

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
            $find->{$request->field_name} = $request->val;

            if ($find->save()) {
                $respond->put('alert::success', __('admin.saved_successfully'))->reload();
            } else {
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
            app()->call(static::$callbacks[$request->key], $request->parameters);
        } else {
            $respond->toast_error(__('admin.callback_not_found'));
        }

        return $respond;
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
