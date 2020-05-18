@extends('admin.page')

@section('content')

    @card(__('lte.list_of_roles'))

        @cardbodytable

            @column(__('lte.path'), 'badge:path,success', true)
            @column(__('lte.methods'), $methods, true)
            @column(__('lte.state'), $state, true)
            @column(__('lte.role'), 'role.name', true)
            @column(__('lte.active'), 'input_switcher:active', true)

        @endcardbodytable

        @tablefooter

    @endcard

@endsection