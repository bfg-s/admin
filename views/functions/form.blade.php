@cardbodyform()

    @formgroup(__('lte::admin.slug'), 'slug')
        @forminput(['rule' => ['required'], 'data' => ['change' => 'str::slug']])
    @endformgroup

    @formgroup(__('lte::admin.roles'), 'roles', null)
        @formcheckbox($roles, ['rule' => ['any-checked']])
    @endformgroup

    @formgroup(__('lte::admin.description'), 'description', null)
        @formtextarea()
    @endformgroup

    @formgroup(__('lte::admin.active'), 'active', null)
        @formswitcher()
    @endformgroup

@endcardbodyform

@formfooter