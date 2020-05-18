@extends('admin.page')

@section('content')

    @card(__('lte.new_function'))

        @include('lte::functions.form')

    @endcard

@endsection