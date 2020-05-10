@extends('admin.page')

@section('content')

    @card(__('lte::admin.edit_function'))

        @include('lte::functions.form')

    @endcard

@endsection