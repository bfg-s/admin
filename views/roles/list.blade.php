@extends('admin.page')

@section('content')

    @card(__('lte::admin.list_of_roles'))

        @cardbodytable

            @column(__('lte::admin.title'), 'name', true)
            @column(__('lte::admin.slug'), 'badge:slug,success', true)

        @endcardbodytable

        @tablefooter

    @endcard

@endsection