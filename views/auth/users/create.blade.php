@extends('admin.page')

@section('content')

    @card(__('lte.add_admin'))

        @include('lte::auth.users.form')

    @endcard

@endsection