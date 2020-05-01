@extends('admin.page')

@section('content')

    @card(__('lte::admin.id_edit'))

        @include('admin.resource.form')

    @endcard

@endsection