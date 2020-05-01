@extends('admin.page')

@section('content')

    @card(__('lte::admin.edit_admin'))

        @include('lte::auth.users.form')

    @endcard

@endsection