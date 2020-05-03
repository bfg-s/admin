@cardbodyform()

    @formgroup(__('lte::admin.path'), 'path')
        @forminput(['rule' => ['required']])
    @endformgroup

    @formgroup(__('lte::admin.methods'), 'method[]')
        @formmiltiselect(collect(array_merge(['*'], \Illuminate\Routing\Router::$verbs))->mapWithKeys(function($i) {return [$i => $i];})->toArray(), ['rule' => ['required']])
    @endformgroup

    @formgroup(__('lte::admin.state'), 'state')
        @formselect(['close' => __('lte::admin.close'), 'open' => __('lte::admin.open')], ['rule' => ['required']])
    @endformgroup

    @formgroup(__('lte::admin.role'), 'lte_role_id')
        @formselect(\Lar\LteAdmin\Models\LteRole::all()->pluck('name', 'id'), ['rule' => ['required']])
    @endformgroup

    @formgroup(__('lte::admin.active'), 'active', null)
        @formswitcher()
    @endformgroup

@endcardbodyform

@formfooter