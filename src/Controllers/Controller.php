<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Lar\Layout\Respond;
use Lar\LteAdmin\Components\Table;
use Lar\LteAdmin\Core\ModelSaver;

/**
 * Class Controller
 *
 * @package Lar\LteAdmin\Controllers
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Permission functions for methods
     *
     * @var array
     */
    static $permission_functions = [];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|Response|\Illuminate\View\View
     */
    public function index_default() {

        return view(config('lte.paths.view', 'admin') . '.resource.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\Factory|Response|\Illuminate\View\View
     */
    public function create_default() {

        return view(config('lte.paths.view', 'admin') . '.resource.create');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Contracts\View\Factory|Response|\Illuminate\View\View
     */
    public function edit_default() {

        return view(config('lte.paths.view', 'admin') . '.resource.edit');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Contracts\View\Factory|Response|\Illuminate\View\View
     */
    public function show_default() {

        return view(config('lte.paths.view', 'admin') . '.resource.show');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  array|null  $data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_default(array $data = null) {

        if ($this->requestToModel($data)) {

            respond()->toast_success(__('lte.saved_successfully'));
        }

        else {

            respond()->toast_error(__('lte.unknown_error'));
        }

        return $this->returnTo();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  array|null  $data
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store_default(array $data = null) {

        if ($this->requestToModel($data)) {

            respond()->toast_success(__('lte.successfully_created'));
        }

        else {

            respond()->toast_error(__('lte.unknown_error'));
        }

        return $this->returnTo();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Respond
     * @throws \Exception
     */
    public function destroy_default() {

        if ($model = $this->existsModel()) {

            if ($model->delete()) {

                respond()->toast_success(__('lte.successfully_deleted'));

                if ($this->isType('index')) {

                    respond()->reload();
                }

                else {

                    respond()->location(gets()->lte->menu->now['link']);
                }
            }

            else {

                respond()->toast_error(__('lte.unknown_error'));
            }
        }

        else {

            respond()->toast_error(__('lte.model_not_found'));
        }

        return respond();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|Respond
     */
    public function returnTo()
    {
        if (request()->ajax() && !request()->pjax()) {

            return respond()->reload();
        }

        $_after = request()->get('_after', 'index');

        if ($_after === 'index' && $menu = gets()->lte->menu->now) {

            $last = Table::getLastRequest();

            return \redirect($menu['link'] . (count($last) ? '?'.http_build_query($last) : ''))->with('_after', $_after);
        }

        return back()->with('_after', $_after);
    }

    /**
     * Trap for default methods
     *
     * @param string $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $method_default = "{$method}_default";

        if (method_exists($this, $method_default)) {
            
            return $this->{$method_default}();
        }

        parent::__call($method, $parameters);
    }
}
