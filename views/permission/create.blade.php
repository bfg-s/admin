@extends('admin.page')

@section('content')

    @card(__('lte::admin.permissions'))

        @include('lte::permission.form')

    @endcard

@endsection