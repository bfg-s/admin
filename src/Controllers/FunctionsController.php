<?php

namespace Lar\LteAdmin\Controllers;

use Illuminate\Http\Request;
use Lar\Layout\Respond;
use Lar\LteAdmin\Core\FunctionsHelperGenerator;
use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Models\LteRole;

use function foo\func;

/**
 * Class HomeController
 *
 * @package Lar\LteAdmin\Controllers
 */
class FunctionsController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //dump(lte_func()->dcfdsfs2);

        $roles = function (LteFunction $function) {

            return '<span class="badge badge-success">' . $function->roles->pluck('name')->implode('</span> <span class="badge badge-success">') . '</span>';
        };

        return view('lte::functions.list', [
            'roles' => $roles
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('lte::functions.create', [
            'roles' => LteRole::all()->pluck('name', 'id')
        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit()
    {
        return view('lte::functions.edit', [
            'roles' => LteRole::all()->pluck('name', 'id')
        ]);
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        if ($back = back_validate($request->all(), [
            'slug' => 'unique:' . LteFunction::class
        ])) {

            return $back;
        }

        $result = $this->store_default();

        \Artisan::call('lar:dump', [
            '--class' => FunctionsHelperGenerator::class
        ]);

        return $result;
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        if (
            $request->get('slug') !== $this->model()->slug &&
            $back = back_validate($request->all(), [
                'slug' => 'unique:' . LteFunction::class
            ])
        ) {

            return $back;
        }

        $result = $this->update_default();

        if ($request->pjax()) {

            \Artisan::call('lar:dump');
        }

        return $result;
    }

    /**
     * @return Respond
     * @throws \Exception
     */
    public function destroy()
    {
        $result = $this->destroy_default();

        \Artisan::call('lar:dump', [
            '--class' => FunctionsHelperGenerator::class
        ]);

        return $result;
    }
}
