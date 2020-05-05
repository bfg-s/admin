@extends('admin.page')

@section('content')

    @card(__('lte::admin.admin_list'))

        @cardbodytable

            @column(__('lte::admin.avatar'), 'avatar')
            @column(__('lte::admin.role'), $roles)
            @column(__('lte::admin.email_address'), 'email', true)
            @column(__('lte::admin.login_name'), 'login', true)
            @column(__('lte::admin.name'), 'name', true)

        @endcardbodytable

        @tablefooter

    @endcard

@endsection