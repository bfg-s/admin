@if(lte_model_type('create'))
    @cardbodyform()

        @formgroup(__('lte.avatar'), 'avatar', null)
            @formfile()
        @endformgroup

        @formgroup(__('lte.login_name'), 'login')
            @forminput(['rule' => ['required']])
        @endformgroup

        @formgroup(__('lte.email_address'), 'email', 'fas fa-envelope')
            @formemail(['rule' => ['required']])
        @endformgroup

        @formgroup(__('lte.name'), 'name')
            @forminput(['rule' => ['required']])
        @endformgroup

        @formgroup(__('lte.role'), 'roles[]')
            @formmiltiselect($roles, ['rule' => ['required']])
        @endformgroup

        <hr>

        @formgroup(__('lte.new_password'), 'password')
            @formpassword(['rule' => ['required', 'confirmation']])
        @endformgroup

        @formgroup(__('lte.repeat_new_password'), 'password_confirmation')
            @formpassword(['rule' => ['required', 'confirmation']])
        @endformgroup


    @endcardbodyform

    @formfooter

@else

    @tabs()

        @tab(__('lte.common'), 'fas fa-cogs')

            @cardbodyform()

                @formgroup(__('lte.avatar'), 'avatar', null)
                    @formfile()
                @endformgroup

                @formgroup(__('lte.login_name'), 'login')
                    @forminput(['disabled' => 'disabled'])
                @endformgroup

                @formgroup(__('lte.email_address'), 'email')
                    @forminput(['disabled' => 'disabled'])
                @endformgroup

                @formgroup(__('lte.name'), 'name')
                    @forminput()
                @endformgroup

                @formgroup(__('lte.role'), 'roles[]')
                    @formmiltiselect($roles, ['rule' => ['required']])
                @endformgroup

            @endcardbodyform

            @formfooter()

        @endtab

        @tab(__('lte.change_password'), 'fas fa-key')

            @cardbodyform(new \Lar\LteAdmin\Models\LteUser())

                @hiddens(['ch_password' => 'true'])

                @formgroup(__('lte.new_password'), 'password')
                    @formpassword(['rule' => ['required']])
                @endformgroup

                @formgroup(__('lte.repeat_new_password'), 'password_confirmation')
                    @formpassword(['rule' => ['required', 'confirmation']])
                @endformgroup

            @endcardbodyform

            @formfooter(false)

        @endtab

    @endtabs

@endif