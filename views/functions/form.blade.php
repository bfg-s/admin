@cardbodyform()

    @formgroup(__('lte.slug'), 'slug')
        @forminput(['rule' => ['required'], 'data' => ['change' => 'str::slug']])
    @endformgroup

    @formgroup(__('lte.roles'), 'roles', null)
        @formcheckbox($roles, ['rule' => ['any-checked']])
    @endformgroup

    @formgroup(__('lte.description'), 'description', null)
        @formtextarea()
    @endformgroup

    @formgroup(__('lte.active'), 'active', null)
        @formswitcher()
    @endformgroup

@endcardbodyform

@formfooter