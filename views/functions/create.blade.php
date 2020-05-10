@extends('admin.page')

@section('content')

    @card(__('lte::admin.new_function'))

        @include('lte::functions.form')

    @endcard

@endsection