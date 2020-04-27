@if(lte_model_type('create'))
    @cardbodyform()

        @formgroup('Avatar', 'avatar', null)
            @formfile()
        @endformgroup

        @formgroup('Login', 'login')
            @forminput(['rule' => ['required']])
        @endformgroup

        @formgroup('Email', 'email', 'fas fa-envelope')
            @formemail(['rule' => ['required']])
        @endformgroup

        @formgroup('Name', 'name')
            @forminput(['rule' => ['required']])
        @endformgroup

        <hr>

        @formgroup('New password', 'password')
            @formpassword(['rule' =>['required', 'confirmation']])
        @endformgroup

        @formgroup('Repeat new password', 'password_confirmation')
            @formpassword(['rule' => ['required', 'confirmation']])
        @endformgroup


    @endcardbodyform

    @formfooter

@else

    @tabs()

        @tab('General', 'fas fa-cogs')

            @cardbodyform()

                @formgroup('Avatar', 'avatar', null)
                    @formfile()
                @endformgroup

                @formgroup('Login', 'login')
                    @forminput(['disabled' => 'disabled'])
                @endformgroup

                @formgroup('Email', 'email')
                    @forminput(['disabled' => 'disabled'])
                @endformgroup

                @formgroup('Name', 'name')
                    @forminput()
                @endformgroup

            @endcardbodyform

            @formfooter()

        @endtab

        @tab('Change password', 'fas fa-key')

            @cardbodyform(new \Lar\LteAdmin\Models\LteUser)

                @hiddens(['ch_password' => 'true'])

                @formgroup('New password', 'password')
                    @formpassword(['rule' => ['required']])
                @endformgroup

                @formgroup('Repeat new password', 'password_confirmation')
                    @formpassword(['rule' => ['required', 'confirmation']])
                @endformgroup

            @endcardbodyform

            @formfooter()

        @endtab

    @endtabs

@endif