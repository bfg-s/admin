<?php

namespace Lar\LteAdmin\Components\RootTools;

use Lar\LteAdmin\Components\Vue\GatesTools;
use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Models\LteRole;
use Lar\LteAdmin\Resources\LteFunctionResource;
use Lar\LteAdmin\Segments\Tagable\TabContent;

/**
 * Class GatesTool
 * @package Lar\LteAdmin\Components\RootTools
 */
class GatesTool extends TabContent
{
    /**
     * @var string
     */
    protected $icon = "fas fa-key";

    /**
     * @var string
     */
    protected $title = "Preferences";

    /**
     * @var array
     */
    public $execute = [
        'build'
    ];

    /**
     * Build tab
     */
    protected function build()
    {
        $action = \Str::parseCallback(\Route::currentRouteAction());

        if (lte_now()) {
            $this->appEnd(GatesTools::create([
                'lte' => lte_now(),
                'roles' => LteRole::all(),
                'action' => $action,
                'funcs' => LteFunctionResource::collection(
                    LteFunction::with('roles')->where('class', trim($action[0], '\\'))->get()
                )->toArray(request())
            ]));
        } else {
            $this->div()->textCenter()->textMuted()->w100()
                ->h3('No roles available!');
        }
    }
}