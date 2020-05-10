@extends('admin.page')

@section('content')

    @card(__('lte::admin.edit_permission'))

        @include('lte::permission.form')

    @endcard

@endsection