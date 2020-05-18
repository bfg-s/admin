@cardbodyform()

    @formgroup(__('lte.title'), 'name')
        @forminput(['rule' => ['required'], 'data' => ['keyup' => 'str::slug', 'keyup-params' => '#input_slug']])
    @endformgroup

    @formgroup(__('lte.slug'), 'slug')
        @forminput(['rule' => ['required']])
    @endformgroup

@endcardbodyform

@formfooter