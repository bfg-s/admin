@extends('admin.page')

@section('content')

    @card(__('lte.list_of_roles'))

        @cardbodytable

            @column(__('lte.title'), 'name', true)
            @column(__('lte.slug'), 'badge:slug,success', true)

        @endcardbodytable

        @tablefooter

    @endcard

@endsection