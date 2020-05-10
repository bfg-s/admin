@extends('admin.page')

@section('content')

    @card(__('lte::admin.list_of_functions'))

        @cardbodytable

            @column(__('lte::admin.role'), $roles)
            @column(__('lte::admin.slug'), 'slug', true)
            @column(__('lte::admin.description'), 'str_limit:description,50', true)
            @column(__('lte::admin.active'), 'input_switcher:active', true)

        @endcardbodytable

        @tablefooter

    @endcard

@endsection