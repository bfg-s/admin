@cardbodyform()

    @formgroup(__('lte::admin.title'), 'name')
        @forminput(['rule' => ['required'], 'data' => ['keyup' => 'str::slug', 'keyup-params' => '#input_slug']])
    @endformgroup

    @formgroup(__('lte::admin.slug'), 'slug')
        @forminput(['rule' => ['required']])
    @endformgroup

@endcardbodyform

@formfooter