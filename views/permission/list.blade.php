@extends('admin.page')

@section('content')

    @card(__('lte::admin.list_of_roles'))

        @cardbodytable

            @column(__('lte::admin.path'), 'badge:path,success', true)
            @column(__('lte::admin.methods'), $methods, true)
            @column(__('lte::admin.state'), $state, true)
            @column(__('lte::admin.role'), 'role.name', true)
            @column(__('lte::admin.active'), 'input_switcher:active', true)

        @endcardbodytable

        @tablefooter

    @endcard

@endsection