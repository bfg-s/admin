<?php

namespace Lar\LteAdmin\Jax;

use Lar\LteAdmin\Models\LteFunction;
use Lar\LteAdmin\Resources\LteFunctionResource;

/**
 * Class LteAdmin
 * @package App\Http\JaxExecutors
 */
class LteRootPreferences extends LteAdmin
{
    /**
     * Public method access
     * 
     * @return bool
     */
    public function access() {
        
        return parent::access() && \LteAdmin::user()->isRoot();
    }

    /**
     * @param  array  $funcs
     * @param  string  $class
     * @return array
     */
    public function update_functions(array $funcs, string $class)
    {
        foreach ($funcs as $func) {

            if (isset($func['id'])) {
                /** @var LteFunction $f */
                $f = LteFunction::find($func['id']);
                $f->update([
                        'description' => $func['description'],
                        'slug' => $func['slug']
                    ]);
            }

            else {
                /** @var LteFunction $f */
                $f = LteFunction::create([
                    'description' => $func['description'],
                    'slug' => $func['slug'],
                    'class' => trim($class, '\\')
                ]);
            }
            $f->roles()->sync(collect($func['roles'])->pluck('id')->toArray());
        }

        return LteFunctionResource::collection(
            LteFunction::with('roles')->where('class', trim($class, '\\'))->get()
        )->toArray(request());
    }

    /**
     * @param  int  $id
     * @param $class
     * @return array
     * @throws \Exception
     */
    public function drop_function(int $id, $class)
    {
        $func = LteFunction::find($id);

        if ($func) {
            $func->delete();
        }

        return LteFunctionResource::collection(
            LteFunction::with('roles')->where('class', trim($class, '\\'))->get()
        )->toArray(request());
    }
}
