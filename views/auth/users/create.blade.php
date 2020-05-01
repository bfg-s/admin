@extends('admin.page')

@section('content')

    @card(__('lte::admin.add_admin'))

        @include('lte::auth.users.form')

    @endcard

@endsection