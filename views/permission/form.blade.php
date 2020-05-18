@cardbodyform()

    @formgroup(__('lte.path'), 'path')
        @forminput(['rule' => ['required']])
    @endformgroup

    @formgroup(__('lte.methods'), 'method[]')
        @formmiltiselect(collect(array_merge(['*'], \Illuminate\Routing\Router::$verbs))->mapWithKeys(function($i) {return [$i => $i];})->toArray(), ['rule' => ['required']])
    @endformgroup

    @formgroup(__('lte.state'), 'state')
        @formselect(['close' => __('lte.close'), 'open' => __('lte.open')], ['rule' => ['required']])
    @endformgroup

    @formgroup(__('lte.role'), 'lte_role_id')
        @formselect(\Lar\LteAdmin\Models\LteRole::all()->pluck('name', 'id'), ['rule' => ['required']])
    @endformgroup

    @formgroup(__('lte.active'), 'active', null)
        @formswitcher()
    @endformgroup

@endcardbodyform

@formfooter