@extends('admin.page')

@section('content')

    @card(__('lte::admin.edit_permissions'))

        @include('lte::permission.form')

    @endcard

@endsection