@extends('admin.page')

@section('content')

    @card(__('lte.admin_list'))

        @cardbodytable

            @column(__('lte.avatar'), 'avatar')
            @column(__('lte.role'), $roles)
            @column(__('lte.email_address'), 'email', true)
            @column(__('lte.login_name'), 'login', true)
            @column(__('lte.name'), 'name', true)

            @disabledelete(function(\Lar\LteAdmin\Models\LteUser $user){ return $user->id !== 1; })

        @endcardbodytable

        @tablefooter

    @endcard

@endsection