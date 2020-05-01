@if(lte_model_type('create'))
    @cardbodyform()

        @formgroup(__('lte::admin.avatar'), 'avatar', null)
            @formfile()
        @endformgroup

        @formgroup(__('lte::admin.login_name'), 'login')
            @forminput(['rule' => ['required']])
        @endformgroup

        @formgroup(__('lte::admin.email_address'), 'email', 'fas fa-envelope')
            @formemail(['rule' => ['required']])
        @endformgroup

        @formgroup(__('lte::admin.name'), 'name')
            @forminput(['rule' => ['required']])
        @endformgroup

        <hr>

        @formgroup(__('lte::admin.new_password'), 'password')
            @formpassword(['rule' => ['required', 'confirmation']])
        @endformgroup

        @formgroup(__('lte::admin.repeat_new_password'), 'password_confirmation')
            @formpassword(['rule' => ['required', 'confirmation']])
        @endformgroup


    @endcardbodyform

    @formfooter

@else

    @tabs()

        @tab(__('lte::admin.common'), 'fas fa-cogs')

            @cardbodyform()

                @formgroup(__('lte::admin.avatar'), 'avatar', null)
                    @formfile()
                @endformgroup

                @formgroup(__('lte::admin.login_name'), 'login')
                    @forminput(['disabled' => 'disabled'])
                @endformgroup

                @formgroup(__('lte::admin.email_address'), 'email')
                    @forminput(['disabled' => 'disabled'])
                @endformgroup

                @formgroup(__('lte::admin.name'), 'name')
                    @forminput()
                @endformgroup

            @endcardbodyform

            @formfooter()

        @endtab

        @tab(__('lte::admin.change_password'), 'fas fa-key')

            @cardbodyform(new \Lar\LteAdmin\Models\LteUser)

                @hiddens(['ch_password' => 'true'])

                @formgroup(__('lte::admin.new_password'), 'password')
                    @formpassword(['rule' => ['required']])
                @endformgroup

                @formgroup(__('lte::admin.repeat_new_password'), 'password_confirmation')
                    @formpassword(['rule' => ['required', 'confirmation']])
                @endformgroup

            @endcardbodyform

            @formfooter()

        @endtab

    @endtabs

@endif